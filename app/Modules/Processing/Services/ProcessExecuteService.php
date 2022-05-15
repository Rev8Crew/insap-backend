<?php

namespace App\Modules\Processing\Services;

use App\Enums\Process\ProcessType;
use App\Exceptions\ProcessException;
use App\Modules\Processing\Models\Dto\ProcessFileDto;
use App\Modules\Processing\Models\Dto\ProcessParamsDto;
use App\Modules\Processing\Models\Process;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use JsonMachine\JsonMachine;
use Throwable;

class ProcessExecuteService
{
    /**
     * @throws ProcessException
     */
    public function execute(
        ProcessType      $processType,
        Process          $process,
        ProcessParamsDto $paramsDto
    ): ProcessParamsDto
    {
        /**
         *  First, we need to pass some params to importer directory (copy files, data, params)
         */
        $this->passParamsToImporter($process, $paramsDto);

        /**
         *  Execute importer app
         */
        $interpreter = $process->getInterpreter();
        $cd = 'cd ' . $process->getStoragePath();

        try {
            $exitCode = $interpreter->execute("$cd;{$interpreter->getAppCommand()}");
            throw_if($exitCode > 0, new \RuntimeException('[executeEvent] Exit code greater then 0, [' . $exitCode . ']'));
        } catch (Throwable $exception) {

            // Если есть ошибки с импортера берем их
            $importerErrors = $this->getErrorFromImporter($process);

            Log::error('Process execute failed', [
                'processType' => $processType->getValue(),
                'process' => $process->toArray(),
                'command' => "$cd;{$interpreter->getAppCommand()}",
                "error" => $exception->getMessage(),
                "trace" => $exception->getTrace(),
                "importer_Errors" => $importerErrors
            ]);

            $this->clearAppDir($process);

            if ($importerErrors) {
                throw new ProcessException("[ExecuteEvent] Error from process", 0, $exception, implode(";", $importerErrors));
            }

            throw new \RuntimeException("[ExecuteEvent] Can't execute command", 0, $exception);
        }

        $importerErrors = $this->getErrorFromImporter($process);

        if ($importerErrors) {
            throw new ProcessException("Error from importer after script command", 0, null, implode(";", $importerErrors));
        }

        /**
         *  Retrieve updated files from importer
         */
        return $this->retrieveInformationFromImporter($process);
    }

    private function passParamsToImporter(Process $process, ProcessParamsDto $paramsDto): void
    {
        $path = $process->getStoragePath();

        $dataPath = $path . DIRECTORY_SEPARATOR . 'data';
        $importerFilesPath = $dataPath . DIRECTORY_SEPARATOR . 'files';

        $paramsJsonFile = $dataPath . DIRECTORY_SEPARATOR . 'params.json';
        $filesJsonFile = $dataPath . DIRECTORY_SEPARATOR . 'files.json';
        $dataJsonFile = $dataPath . DIRECTORY_SEPARATOR . 'data.json';

        $this->clearAppDir($process);

        $this->writeArrayToJsonFile($paramsDto->getParams()->all(), $paramsJsonFile);

        // Files
        $copyFiles = $this->copyFilesToPath($paramsDto->getFiles()->all(), $importerFilesPath);
        $this->writeArrayToJsonFile($copyFiles, $filesJsonFile);

        if ($paramsDto->getData()->all()) {
            $this->writeArrayToJsonFile($paramsDto->getData()->all(), $dataJsonFile);
        }
    }

    private function getErrorFromImporter(Process $process): array
    {
        $path = $process->getStoragePath();

        $resultPath = $path . DIRECTORY_SEPARATOR . 'result';
        $errorJsonFile = $resultPath . DIRECTORY_SEPARATOR . 'errors.json';

        // Если импортер сгенерировал файл с ошибками, то выводим их
        if (file_exists($errorJsonFile)) {
            $errors = $this->decodeJsonToArray($errorJsonFile);

            if ($errors) {
                return $errors;
            }

        }

        return [];
    }

    private function clearAppDir(Process $process)
    {
        $path = $process->getStoragePath();

        $importerDataPath = $path . DIRECTORY_SEPARATOR . 'data';
        $importerFilesPath = $importerDataPath . DIRECTORY_SEPARATOR . 'files';
        $importerResultPath = $path . DIRECTORY_SEPARATOR . 'result';

        $this->clearAndCreateDir($importerDataPath);
        $this->clearAndCreateDir($importerFilesPath);
        $this->clearAndCreateDir($importerResultPath);

    }

    /**
     * @param string $path
     * @param bool $onlyClear
     */
    private function clearAndCreateDir(string $path, bool $onlyClear = false): void
    {
        if (File::exists($path)) {
            File::deleteDirectory($path);
        }

        if (!$onlyClear) {
            File::makeDirectory($path, 0755, true, true);
        }
    }

    /**
     * @param array $array
     * @param string $jsonFile
     * @return void
     */
    private function writeArrayToJsonFile(array $array, string $jsonFile): void
    {
        File::put($jsonFile, json_encode($array, JSON_UNESCAPED_UNICODE));
    }

    /**
     * @param ProcessFileDto[] $files
     */
    private function copyFilesToPath(array $files, string $path): array
    {
        $result = [];

        foreach ($files as $fileDto) {
            $file = $fileDto->getUploadedFile();

            $extension = $file->getClientOriginalExtension() ?: $file->getExtension();
            $result[] = [
                'name' => $file->hashName(),
                'original' => $file->getClientOriginalName() . '.' . $extension,
                'mime' => $file->getMimeType(),
                'type' => $fileDto->getAlias(),
                'alias' => $fileDto->getAlias(),
            ];

            $fullPath = $path . DIRECTORY_SEPARATOR . $file->hashName();
            File::put($fullPath, $file->getContent());
        }

        return $result;
    }

    /**
     * @throws ProcessException
     */
    private function retrieveInformationFromImporter(Process $process): ProcessParamsDto
    {
        $path = $process->getStoragePath();

        $resultPath = $path . DIRECTORY_SEPARATOR . 'result';
        $filesPath = $resultPath . DIRECTORY_SEPARATOR . 'files';

        $paramsJsonFile = $resultPath . DIRECTORY_SEPARATOR . 'params.json';
        $filesJsonFile = $resultPath . DIRECTORY_SEPARATOR . 'files.json';
        $dataJsonFile = $resultPath . DIRECTORY_SEPARATOR . 'data.json';

        $params = $this->decodeJsonToArray($paramsJsonFile);
        $files = $this->decodeJsonToArray($filesJsonFile);

        $processedData = [];
        if (file_exists($dataJsonFile)) {
            $processedData = $this->decodeJsonToArray($dataJsonFile);
        }

        $processParamsDto = new ProcessParamsDto($params, $processedData);

        foreach ($files as $file) {
            $path = $filesPath . DIRECTORY_SEPARATOR . $file['name'];
            $processParamsDto->addFileFromUploaded(new UploadedFile($path, $file['original'], $file['mime']), $file['type']);
        }

        return $processParamsDto;
    }

    /**
     * @param string $path
     * @return array
     */
    private function decodeJsonToArray(string $path): array
    {
        $result = [];
        foreach (JsonMachine::fromFile($path) as $item) {
            $result[] = $item;
        }

        return $result;
    }
}
