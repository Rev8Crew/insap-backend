<?php


namespace App\Modules\Importer\Services;


use App\Events\PreImportEvent;
use App\Modules\Importer\Models\Importer\Importer;
use App\Modules\Importer\Models\Importer\ImporterDto;
use App\Modules\Importer\Models\ImporterEvents\ImporterEventEvent;
use App\Modules\Importer\Models\ImporterEvents\ImporterEventFile;
use App\Modules\Importer\Models\ImporterEvents\ImporterEventParams;
use App\Modules\Plugins\Models\PluginServiceInterface;
use App\Modules\Plugins\Services\PluginService;
use App\Modules\Project\Models\Record;
use App\Modules\Project\Models\RecordInfo;
use App\Modules\Project\Services\RecordService;
use Illuminate\Support\Facades\DB;
use Mts88\MongoGrid\Facades\MongoGrid;
use Ramsey\Uuid\Uuid;
use Throwable;

/**
 * Class ImporterService
 * @package App\Modules\Importer\Service
 */
class ImporterService
{
    private ImporterEventService $importerEventService;
    private ImporterExecuteService $importerExecuteService;

    private RecordService $recordService;

    private PluginService $pluginService;

    /**
     * ImporterService constructor.
     * @param ImporterEventService $importerEventService
     * @param RecordService $recordService
     * @param ImporterExecuteService $importerExecuteService
     */
    public function __construct(
        ImporterEventService $importerEventService,
        RecordService $recordService,
        ImporterExecuteService $importerExecuteService,
        PluginService $pluginService
    )
    {
        $this->importerEventService = $importerEventService;
        $this->recordService = $recordService;
        $this->importerExecuteService = $importerExecuteService;
        $this->pluginService = $pluginService;
    }

    public function create(ImporterDto $dto): Importer
    {
        $importer = Importer::create($dto->toArray());
        return $importer;
    }

    /**
     * @param Importer $importer
     */
    public function delete(Importer $importer)
    {
        /**
         *  Delete all events
         */
        foreach ($importer->events as $event) {
            $this->importerEventService->delete($event);
        }

        $importer->delete();
    }

    /**
     * @param Importer $importer
     * @param Record $record
     * @param array $params
     * @param ImporterEventFile[] $files
     * @throws Throwable
     */
    public function import(Importer $importer, Record $record, array $params = [], array $files = []): bool
    {
        ini_set('memory_limit', '1G');
        try {
            DB::beginTransaction();

            PreImportEvent::dispatch($importer, $record, $params, $files);

            $eventParams = new ImporterEventParams($params);
            $eventParams->setFilesFromUploaded($files);

            // Processing data
            $eventParams = $this->importerExecuteService->executeEvent(ImporterEventEvent::EVENT_IMPORT, $importer, $eventParams);

            // Add to DB
            if ($importer->plugin) {
                $service = $this->pluginService->getPluginService($importer->plugin);
                $service->preprocess($record, $eventParams);
            } else {
                $this->addToDatabase($record, $eventParams->getData());
            }


            $fileIds = [];
            $gridFsService = app(\Mts88\MongoGrid\Services\MongoGrid::class);
            foreach ($eventParams->getFiles() as $file) {

                $fileIds[] = $gridFsService->storeFile($file->getUploadedFile()->getContent(), Uuid::uuid4(), [
                    'record_id' => $record->id,
                    'importer_id' => $importer->id,
                    'filename' => $file->getUploadedFile()->getClientOriginalName(),
                    'extension' => $file->getUploadedFile()->getClientOriginalExtension(),
                    'type' => $file->getType()
                ]);

            }

            $record->files = $fileIds;
            $record->params = $eventParams->getParams();
            $record->importer()->associate($importer);

            $record->save();

            DB::commit();
        } catch (Throwable $exception) {
            DB::rollBack();
            $this->recordService->deleteRecordsInfo($record);

            throw new \RuntimeException('[ImporterService] Import event failed <' . $exception->getMessage() . '>', null, $exception);
        }

        return true;
    }

    /**
     * @param Record $record
     * @param array $data
     */
    protected function addToDatabase(Record $record, array $data)
    {
        $chunk = [];
        foreach ($data as $array) {
            // Add record_id to each record
            $array['record_id'] = $record->id;

            $chunk[] = $array;
            if (count($chunk) === 1000) {

                RecordInfo::insert($chunk);
                $chunk = [];
            }

        }
    }
}
