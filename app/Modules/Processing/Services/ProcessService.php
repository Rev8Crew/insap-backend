<?php

namespace App\Modules\Processing\Services;

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

class ProcessService
{

    protected RecordService $recordService;
    protected PluginService $pluginService;
    protected ProcessExecuteService $processExecuteService;
    private FileService $fileService;

    public function __construct(RecordService $recordService, PluginService $pluginService, ProcessExecuteService $processExecuteService, FileService $fileService)
    {
        $this->recordService = $recordService;
        $this->pluginService = $pluginService;
        $this->processExecuteService = $processExecuteService;
        $this->fileService = $fileService;
    }

    public function getAllByProject(Project $project): Collection
    {
        return Process::whereProjectId($project->id)->get();
    }

    /**
     * @throws Exception|\Throwable
     */
    public function create(ProcessDto $dto, UploadedFile $archive): Process
    {
        \DB::beginTransaction();
        $process = Process::create($dto->toArray());

        try {
            $this->install($process, $archive);

            $file = $this->fileService->createFromUploadedFile($archive, User::whereId($dto->userId)->first());
            $process->archiveFile()->associate($file)->save();

            \DB::commit();
        } catch (\Throwable $throwable) {
            $this->delete($process);

            $process = null;
            \DB::rollBack();
            throw $throwable;
        }

        return $process;
    }

    /**
     * @throws Exception|\Throwable
     */
    public function install(Process $process, UploadedFile $archive)
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

        /** Install requirements */
        $requirementsJsonFile = $path . DIRECTORY_SEPARATOR . Process::REQUIREMENTS_FILE_NAME;
        if (file_exists($requirementsJsonFile)) {
            /**
             *  Parse commands from json
             */
            $commands = JsonMachine::fromFile($requirementsJsonFile, '/commands');

            $cdCommand = 'cd ' . $path;
            foreach ($commands as $command) {
                $exitCode = $interpreter->execute("$cdCommand;$command");

                if ($exitCode) {
                    throw new Exception("[Install] Failed exit code from $command [cd $cdCommand]");
                }

            }
        }

        $optionsJsonFile = $path . DIRECTORY_SEPARATOR . Process::OPTIONS_FILE_NAME;

        if (file_exists($optionsJsonFile)) {
            $process->options = json_decode(file_get_contents($optionsJsonFile));
            $process->save();

            // Try to import fields from json file
            $this->importFieldsFromOptions($process);
        }

        /** Execute Test Command */
        $interpreter->addArg('--test=', '1');
        $cdCommand = 'cd ' . $path;
        $exitCode = $interpreter->execute("$cdCommand;{$interpreter->getAppCommand()}");
        throw_if($exitCode > 0, new \RuntimeException('[Install] Test command exit code greater then 0, [' . $exitCode . ']'));
    }

    private function importFieldsFromOptions(Process $process)
    {
        $fields = $process->options['fields'] ?? [];

        if (!$fields) {
            return;
        }

        $processFields = collect();
        foreach ($fields as $field) {
            $processFields->push(ProcessFieldType::create($field));
        }

        $process->fields()->saveMany($processFields);
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

        if ($plugin) {
            $process->plugin()->associate($plugin);
        }

        if ($plugin === null) {
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
        $this->install($process, $archive);

        return $process;
    }

    public function getFieldsByProcess(Process $process): Collection
    {
        return ProcessFieldType::whereHas('process', function (Builder $query) use ($process) {
            $query->where('id', $process->id);
        })->active()->orderBy('order')->get();
    }

}
