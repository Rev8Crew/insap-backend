<?php

namespace App\Modules\Processing\Services;

use App\Enums\Process\ProcessOption;
use App\Models\User;
use App\Modules\Plugins\Models\Plugin;
use App\Modules\Plugins\Services\PluginService;
use App\Modules\Processing\Models\Dto\ProcessDto;
use App\Modules\Processing\Models\Process;
use App\Modules\Processing\Models\ProcessFieldType;
use App\Modules\Project\Models\Project;
use App\Modules\Project\Services\RecordService;
use App\Services\File\FileService;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use JsonMachine\JsonMachine;
use PhpZip\Exception\ZipException;
use PhpZip\ZipFile;
use Webmozart\Assert\Assert;

class ProcessAppService
{
    protected RecordService $recordService;
    protected PluginService $pluginService;
    protected ProcessExecuteService $processExecuteService;
    private FileService $fileService;
    private ProcessOptionService $optionsService;
    private ProcessFieldService $processFieldService;

    public function __construct(
        RecordService $recordService,
        PluginService $pluginService,
        ProcessExecuteService $processExecuteService,
        FileService $fileService,
        ProcessOptionService $optionsService,
        ProcessFieldService $processFieldService
    )
    {
        $this->recordService = $recordService;
        $this->pluginService = $pluginService;
        $this->processExecuteService = $processExecuteService;
        $this->fileService = $fileService;
        $this->optionsService = $optionsService;
        $this->processFieldService = $processFieldService;
    }

    public function getAllByProject(Project $project): Collection
    {
        return Process::whereProjectId($project->id)->get();
    }

    /**
     * @throws Exception|\Throwable
     */
    public function createWithApp(ProcessDto $dto, UploadedFile $archive): Process
    {
        \DB::beginTransaction();
        $process = Process::create($dto->toArray());

        try {
            $this->installApp($process, $archive);

            $file = $this->fileService->createFromUploadedFile($archive, User::whereId($dto->userId)->first());
            $process->archiveFile()->associate($file)->save();

            \DB::commit();
        } catch (\Throwable $throwable) {
            //$this->delete($process);

            $process = null;
            \DB::rollBack();
            throw $throwable;
        }

        return $process;
    }

    /**
     * @throws Exception|\Throwable
     */
    public function installApp(Process $process, UploadedFile $archive): void
    {
        $storage = Storage::disk('process');
        $storage->makeDirectory($process->id);

        $path = $process->getStoragePath();

        try {
            $zip = new ZipFile();
            $zip->openFile($archive->getRealPath())
                ->extractTo($path);
        } catch (ZipException $exception) {
            Log::error("Can't Open/Extract file '{$archive->getRealPath()}' to '$path'", [
                'message' => $exception->getMessage(),
                'trace' => $exception->getTrace(),
                'uploadedFile' => $archive,
                'path' => $path,
                'process' => $process
            ]);

            throw new \RuntimeException("Can't Open/Extract file '{$archive->getRealPath()}' to '$path'", 0, $exception);
        }

        $interpreter = $process->getInterpreter();

        $this->installRequirements($process);
        $this->installOptions($process);

        /** Execute Test Command */
        $interpreter->addArg('--test=', '1');
        $cdCommand = 'cd ' . $path;

        $exitCode = $interpreter->execute("$cdCommand;{$interpreter->getAppCommand()}");
        throw_if($exitCode > 0, new \RuntimeException('[Install] Test command exit code greater then 0, [' . $exitCode . ']'));
    }

    /**
     * @throws \Throwable
     */
    private function installRequirements(Process $process): void
    {
        $path = $process->getStoragePath();
        $interpreter = $process->getInterpreter();

        $requirementsJsonFile = $path . DIRECTORY_SEPARATOR . Process::REQUIREMENTS_FILE_NAME;

        throw_if(!\File::exists($requirementsJsonFile), new \RuntimeException("Can't find options file in [". $requirementsJsonFile ."]"));

        $cdCommand = 'cd ' . $path;
        $detectComposer = false;
        foreach (JsonMachine::fromFile($requirementsJsonFile, '/commands') as $command) {

            if (\Str::startsWith($command, 'composer')) {
                $detectComposer = true;
            }

            try {
                $finalCommand = $detectComposer ? "export COMPOSER_HOME=\"/tmp\";$cdCommand;$command" : "$cdCommand;$command";
                $exitCode = $interpreter->execute($finalCommand);
                throw_if($exitCode > 0, new \RuntimeException('[Install] Exit code greater then 0, [' . $exitCode . '] | Command:' . $cdCommand . ';' . $command));
            } catch (\Throwable $throwable) {

                throw $throwable;
            }
        }
    }

    /**
     * @throws \Throwable
     * @throws \JsonException
     */
    private function installOptions(Process $process): void {
        throw_if(!$this->optionsService->isOptionsFileExists($process), new \RuntimeException("Can't find options file in [". $this->optionsService->getOptionFilePath($process) ."]"));
        throw_if(!$this->optionsService->isAllRequiredOptionsExists($process), new \RuntimeException("Please provide all required options, such as " . $this->optionsService->getOptionsDiff($process)->implode(',')));

        $process->fill(['options' => $this->optionsService->getAllOptions($process)->all()])->save();

        $this->processFieldService->installFieldsToProcess($process);
    }

    public function delete(Process $process): ?bool
    {
        $this->deleteApp($process);

        if ($process->imageFile) {
            $this->fileService->delete($process->imageFile);
        }

        if ($process->archiveFile) {
            $this->fileService->delete($process->archiveFile);
        }

        return $process->delete();

    }

    public function deleteApp(Process $process): bool
    {
        return Storage::disk('process')->deleteDirectory($process->id);
    }

    public function update(Process $process, ?string $name, ?string $description, ?Plugin $plugin): bool
    {
        if ($name && $process->name !== $name) {
            $process->fill(['name' => $name]);
        }

        if ($description && $process->description !== $description) {
            $process->fill(['description' => $description]);
        }

        if ($plugin && $plugin->id !== $process->plugin_id) {
            Assert::eq(0, $process->records()->count(), trans('process.change_plugin_while_has_record'));
            $process->plugin()->associate($plugin);
        }

        if ($plugin === null && $process->plugin_id) {
            Assert::eq(0, $process->records()->count(), trans('process.change_plugin_while_has_record'));

            $process->plugin()->delete();
        }


        return $process->save();
    }

    /**
     * @throws Exception|\Throwable
     */
    public function updateApp(Process $process, UploadedFile $archive): Process
    {
        $this->deleteApp($process);
        $process->fields()->delete();
        $this->installApp($process, $archive);

        return $process;
    }

    public function getFieldsByProcess(Process $process): Collection
    {
        return ProcessFieldType::whereHas('process', function (Builder $query) use ($process) {
            $query->where('id', $process->id);
        })->active()->orderBy('order')->get();
    }

    public function getById(int $id) : Process
    {
        $model = Process::find($id);

        Assert::notNull($model, "Can't find process with id $id");

        return $model;

    }

}
