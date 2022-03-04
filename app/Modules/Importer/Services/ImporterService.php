<?php


namespace App\Modules\Importer\Services;


use App\Events\PreImportEvent;
use App\Modules\Importer\Models\Importer\Importer;
use App\Modules\Importer\Models\Importer\ImporterDto;
use App\Modules\Importer\Models\ImporterEvents\ImporterEventEvent;
use App\Modules\Importer\Models\ImporterEvents\ImporterEventFile;
use App\Modules\Importer\Models\ImporterEvents\ImporterEventParams;
use App\Modules\Project\Models\Record;
use App\Modules\Project\Models\RecordInfo;
use App\Modules\Project\Services\RecordService;
use Exception;
use Illuminate\Support\Facades\DB;
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

    /**
     * ImporterService constructor.
     * @param ImporterEventService $importerEventService
     * @param RecordService $recordService
     * @param ImporterExecuteService $importerExecuteService
     */
    public function __construct(ImporterEventService $importerEventService, RecordService $recordService, ImporterExecuteService $importerExecuteService)
    {
        $this->importerEventService = $importerEventService;
        $this->recordService = $recordService;
        $this->importerExecuteService = $importerExecuteService;
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
        try {
            DB::beginTransaction();

            PreImportEvent::dispatch();

            $eventParams = new ImporterEventParams($params);
            $eventParams->setFilesFromUploaded($files);

            // Processing data
            $eventParams = $this->importerExecuteService->executeEvent(ImporterEventEvent::EVENT_IMPORT, $importer, $eventParams);

            // Add to DB
            $this->addToDatabase($record, $eventParams->getData());

            DB::commit();
        } catch (Throwable $exception) {
            // If smt goes wrong
            DB::rollBack();
            // TODO: ???????????
            $this->recordService->deleteRecordsInfo($record);

            throw new Exception("[ImporterService] Method import failed...", 0, $exception);
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
