<?php


namespace App\Modules\Importer\Services;


use App\Modules\Importer\Models\Importer\Importer;
use App\Modules\Importer\Models\Importer\ImporterDto;
use App\Modules\Importer\Models\ImporterEvents\ImporterEventEvent;
use App\Modules\Project\Models\Record;
use App\Modules\Project\Services\RecordService;
use Illuminate\Support\Facades\DB;
use Throwable;

/**
 * Class ImporterService
 * @package App\Modules\Importer\Service
 */
class ImporterService
{
    private ImporterEventService $importerEventService;
    private RecordService $recordService;

    /**
     * ImporterService constructor.
     * @param ImporterEventService $importerEventService
     * @param RecordService $recordService
     */
    public function __construct(ImporterEventService $importerEventService, RecordService $recordService)
    {
        $this->importerEventService = $importerEventService;
        $this->recordService = $recordService;
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
     * @param array $files
     * @throws Throwable
     */
    public function import(Importer $importer, Record $record, array $params = [], array $files = [])
    {
        try {
            DB::beginTransaction();

            // Event before import
            $this->importerEventService->executeEvent(ImporterEventEvent::EVENT_PRE_IMPORT, $importer, $params, $files);

            // Processing data
            $data = $this->importerEventService->executeEvent(ImporterEventEvent::EVENT_IMPORT, $importer, $params, $files);

            // Event with processed data
            $this->importerEventService->executeEvent(ImporterEventEvent::EVENT_POST_IMPORT_BEFORE_DB, $importer, $params, $files, $data);

            // Add to DB
            $this->addToDatabase($record, $data);

            // Event after DB
            $this->importerEventService->executeEvent(ImporterEventEvent::EVENT_POST_IMPORT_AFTER_DB, $importer, $params, $files, $data);

            DB::commit();
        } catch (Throwable $exception) {
            // If smt goes wrong
            DB::rollBack();
            $this->recordService->delete($record);
        }

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
            if (count($chunk) == 1000) {
                Record::insert($chunk);
                $chunk = [];
            }

        }
    }
}
