<?php

namespace App\Modules\Processing\Services;

use App\Modules\Plugins\Services\PluginService;
use App\Modules\Processing\Models\Dto\ProcessDto;
use App\Modules\Processing\Models\Process;
use App\Modules\Project\Services\RecordService;
use Exception;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use JsonMachine\JsonMachine;
use PhpZip\Exception\ZipException;
use PhpZip\ZipFile;

class ProcessService
{
    public const REQUIREMENTS_FILE_NAME = 'requirements.json';

    protected RecordService $recordService;
    protected PluginService $pluginService;
    protected ProcessExecuteService $processExecuteService;

    public function __construct(RecordService $recordService, PluginService $pluginService, ProcessExecuteService $processExecuteService)
    {
        $this->recordService = $recordService;
        $this->pluginService = $pluginService;
        $this->processExecuteService = $processExecuteService;
    }

    /**
     * @throws Exception
     */
    public function create(ProcessDto $dto, UploadedFile $archive)
    {
        $process = Process::create($dto->toArray());

        try {
            $this->install($process, $archive);
        } catch (\Throwable $throwable) {
            $this->delete($process);
            throw new Exception('[Create] Process service create failed ...', 0, $throwable);
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
        $requirementsJsonFile = $path . DIRECTORY_SEPARATOR . self::REQUIREMENTS_FILE_NAME;
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

        /** Execute Test Command */
        $interpreter->addArg('--test=', '1');
        $cdCommand = 'cd ' . $path;
        $exitCode = $interpreter->execute("$cdCommand;{$interpreter->getAppCommand()}");
        throw_if($exitCode > 0, new \RuntimeException('[Install] Test command exit code greater then 0, [' . $exitCode . ']'));
    }

    public function delete(Process $process): ?bool
    {
        $this->deleteApp($process);
        return $process->delete();

    }

    public function deleteApp(Process $process): bool
    {
        return Storage::disk('process')->deleteDirectory($process->id);
    }

    /**
     * @throws Exception|\Throwable
     */
    public function updateApp(Process $process, UploadedFile $archive): Process
    {
        $this->deleteApp($process);
        $this->install($process, $archive);

        return $process;
    }

}
