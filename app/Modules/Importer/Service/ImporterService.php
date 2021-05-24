<?php


namespace App\Modules\Importer\Service;


use App\Modules\Importer\Models\Importer\Importer;
use App\Modules\Importer\Models\ImporterEvent\ImporterEvent;
use App\Modules\Project\Models\Record;
use App\Modules\Project\Services\RecordService;
use Exception;
use Illuminate\Http\UploadedFile;
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

    public function __construct(ImporterEventService $importerEventService, RecordService $recordService)
    {
        $this->importerEventService = $importerEventService;
        $this->recordService = $recordService;
    }

    /**
     * @param Importer $importer
     * @param Record $record
     * @param array $params - request params
     * @param UploadedFile[] $files - request files
     * @throws Throwable
     */
    public function import(Importer $importer, Record $record, array $params = [], array $files = [])
    {
        // Pre import event
        $event = ImporterEvent::EVENT_PRE;

        try {
            DB::beginTransaction();

            // Event before import
            $this->importerEventService->event($event, ImporterEvent::EVENT_TYPE_PRE, $importer->appliance, $params, $files);

            $data = $this->exec($importer, $params, $files);

            // Event with processed data
            $this->importerEventService->event($event, ImporterEvent::EVENT_TYPE_POST_BEFORE_DB, $importer->appliance, $params, $files, $data);

            // Add to DB
            $this->addToDatabase($record, $data);

            // Event after DB
            $this->importerEventService->event($event, ImporterEvent::EVENT_TYPE_POST_AFTER_DB, $importer->appliance, $params, $files, $data);

            DB::commit();
        } catch (Exception $exception) {
            // If smth goes wrong
            DB::rollBack();
            $this->recordService->delete($record);
        }

    }

    /**
     * @param Importer $importer
     * @param array $params
     * @param array $files
     * @return array
     */
    protected function exec(Importer $importer, array $params, array $files): array
    {
        return $importer->exec($params, $files);
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
