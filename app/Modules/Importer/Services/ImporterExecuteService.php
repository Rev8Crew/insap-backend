<?php


namespace App\Modules\Importer\Services;


use App\Modules\Importer\Models\Importer\Importer;
use App\Modules\Importer\Models\ImporterEvents\ImporterEvent;
use App\Modules\Importer\Models\ImporterEvents\ImporterEventEvent;
use App\Modules\Importer\Models\ImporterEvents\ImporterEventFile;
use App\Modules\Importer\Models\ImporterEvents\ImporterEventParams;
use App\Modules\Importer\Models\ImporterInterpreter\ImporterInterpreter;
use Exception;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use JsonMachine\JsonMachine;
use Throwable;


/**
 * Class ImporterExecuteService
 * @package App\Modules\Importer\Services
 */
class ImporterExecuteService
{
    /**
     * @param int $event
     * @param Importer $importer
     * @param ImporterEventParams $eventParams
     * @return ImporterEventParams
     * @throws Throwable
     */
    public function executeEvent(
        int $event,
        Importer $importer,
        ImporterEventParams $eventParams
    ): ImporterEventParams
    {
        $eventEvent = new ImporterEventEvent($event);

        $importerEvents = ImporterEvent::where('event', $eventEvent->getEvent())
            ->where('importer_id', $importer->id)
            ->orderBy('order')->get();

        /**
         *  Execute all importer's by order
         */
        $importerParams = null;
        foreach ($importerEvents as $importerEvent) {
            /**
             *  First of all, we need to pass some params to importer directory (copy files, data, params)
             */
            $this->passParamsToImporter($importerEvent, $eventParams);

            /**
             *  Execute importer app
             */
            $interpreter = $importerEvent->interpreter_class;
            /** @var ImporterInterpreter $interpreter */
            $interpreter = new $interpreter;

            $cd = 'cd ' . $importerEvent->getStoragePath();
            try {
                $exitCode = $interpreter->execute("$cd;{$interpreter->getAppCommand()}");
                throw_if($exitCode > 0, new Exception('[executeEvent] Exit code greater then 0, [' . $exitCode . ']'));
            } catch (Throwable $exception) {
                Log::error('Importer failed to execute', [
                    'event' => $event,
                    'importerEvent' => $importerEvent->toArray(),
                    'command' => "$cd;{$interpreter->getAppCommand()}",
                    "error" => $exception->getMessage()
                ]);
                throw new Exception("[ExecuteEvent] Can't execute command", 0, $exception);
            }

            /**
             *  Retrieve updated files from importer
             */
            $importerParams = $this->retrieveInformationFromImporter($importerEvent);
        }

        return $importerParams ?? $eventParams;
    }

    /**
     * @param ImporterEvent $importerEvent
     * @param ImporterEventParams $eventParams
     */
    private function passParamsToImporter(ImporterEvent $importerEvent, ImporterEventParams $eventParams)
    {
        $importerPath = $importerEvent->getStoragePath();

        $importerDataPath = $importerPath . DIRECTORY_SEPARATOR . 'data';
        $importerFilesPath = $importerDataPath . DIRECTORY_SEPARATOR . 'files';
        $importerResultPath = $importerPath . DIRECTORY_SEPARATOR . 'result';

        $paramsJsonFile = $importerDataPath . DIRECTORY_SEPARATOR . 'params.json';
        $filesJsonFile = $importerDataPath . DIRECTORY_SEPARATOR . 'files.json';
        $dataJsonFile = $importerDataPath . DIRECTORY_SEPARATOR . 'data.json';

        $this->clearAndCreateDir($importerDataPath);
        $this->clearAndCreateDir($importerFilesPath);
        $this->clearAndCreateDir($importerResultPath);

        $this->writeArrayToJsonFile($eventParams->getParams(), $paramsJsonFile);

        // Files
        $copyFiles = $this->copyFilesToPath($eventParams->getFiles(), $importerFilesPath);
        $this->writeArrayToJsonFile($copyFiles, $filesJsonFile);

        if ($eventParams->getData()) {
            $this->writeArrayToJsonFile($eventParams->getData(), $dataJsonFile);
        }

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
     * @return bool|int
     */
    private function writeArrayToJsonFile(array $array, string $jsonFile)
    {
        return File::put($jsonFile, json_encode($array, JSON_UNESCAPED_UNICODE));
    }

    /**
     * @param ImporterEventFile[] $files
     */
    private function copyFilesToPath(array $files, string $path): array
    {
        $result = [];

        foreach ($files as $eventFile) {
            $file = $eventFile->getUploadedFile();

            $result[] = [
                'name' => $file->hashName(),
                'original' => $file->getClientOriginalName() . '.' . $file->getClientOriginalExtension(),
                'mime' => $file->getMimeType(),
                'type' => $eventFile->getType()
            ];

            $file->storeAs($path, $file->hashName());
        }

        return $result;
    }

    /**
     * @param ImporterEvent $importerEvent
     * @return ImporterEventParams
     */
    private function retrieveInformationFromImporter(ImporterEvent $importerEvent): ImporterEventParams
    {
        $importerPath = $importerEvent->getStoragePath();

        $importerResultPath = $importerPath . DIRECTORY_SEPARATOR . 'result';
        $importerFilesPath = $importerResultPath . DIRECTORY_SEPARATOR . 'files';

        $paramsJsonFile = $importerResultPath . DIRECTORY_SEPARATOR . 'params.json';
        $filesJsonFile = $importerResultPath . DIRECTORY_SEPARATOR . 'files.json';
        $dataJsonFile = $importerResultPath . DIRECTORY_SEPARATOR . 'data.json';

        $params = $this->decodeJsonToArray($paramsJsonFile);
        $files = $this->decodeJsonToArray($filesJsonFile);

        $processedData = [];
        if (file_exists($dataJsonFile)) {
            $processedData = $this->decodeJsonToArray($dataJsonFile);
        }

        $importerParams = new ImporterEventParams($params, $processedData);

        foreach ($files as $file) {
            $path = $importerFilesPath . DIRECTORY_SEPARATOR . $file['name'];
            $importerParams->addFileFromUploaded(new UploadedFile($path, $file['original'], $file['mime']), $file['type']);
        }

        return $importerParams;
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
