<?php

namespace App\Modules\Processing\Services;

use App\Enums\Process\ProcessType;
use App\Events\PreImportEvent;
use App\Modules\Plugins\Services\PluginService;
use App\Modules\Processing\Models\Dto\ProcessParamsDto;
use App\Modules\Processing\Models\Process;
use App\Modules\Project\Models\Record;
use App\Modules\Project\Models\RecordInfo;
use App\Modules\Project\Services\RecordService;
use Illuminate\Support\Facades\DB;
use Mts88\MongoGrid\Services\MongoGrid;
use Plugins\adcp\Services\ProcessingService;
use Ramsey\Uuid\Uuid;
use RuntimeException;
use Throwable;

class ImporterService extends ProcessingService implements ProcessServiceInterface
{
    protected RecordService $recordService;
    protected PluginService $pluginService;
    protected ProcessExecuteService $processExecuteService;
    private MongoGrid $mongoGrid;

    public function __construct(
        RecordService         $recordService,
        PluginService         $pluginService,
        ProcessExecuteService $processExecuteService,
        MongoGrid             $mongoGrid
    )
    {
        $this->recordService = $recordService;
        $this->pluginService = $pluginService;
        $this->processExecuteService = $processExecuteService;
        $this->mongoGrid = $mongoGrid;
    }

    public function executeProcess(Process $process, Record $record, array $params = [], array $files = []): bool
    {
        ini_set('memory_limit', '2G');

        try {
            DB::beginTransaction();

            PreImportEvent::dispatch($process, $record, $params, $files);

            $processParamsDto = new ProcessParamsDto($params);
            $processParamsDto->setFilesFromUploaded($files);

            // Processing data
            $processParamsDto = $this->processExecuteService->execute(ProcessType::create(ProcessType::IMPORTER), $process, $processParamsDto);

            // Add to DB
            if ($process->plugin) {
                $service = $this->pluginService->getPluginService($process->plugin);
                $service->preprocess($record, $processParamsDto);
            } else {
                $this->addToDatabase($record, $processParamsDto->getData());
            }

            $fileIds = [];
            foreach ($processParamsDto->getFiles() as $file) {

                $fileIds[] = $this->mongoGrid->storeFile($file->getUploadedFile()->getContent(), Uuid::uuid4(), [
                    'record_id' => $record->id,
                    'importer_id' => $process->id,
                    'filename' => $file->getUploadedFile()->getClientOriginalName(),
                    'extension' => $file->getUploadedFile()->getClientOriginalExtension(),
                    'type' => $file->getAlias()
                ]);

            }

            $record->files = $fileIds;
            $record->params = $processParamsDto->getParams();
            $record->process()->associate($process);

            $record->save();

            DB::commit();
        } catch (Throwable $exception) {
            DB::rollBack();
            $this->recordService->deleteRecordsInfo($record);

            throw new RuntimeException('[ImporterService] Import event failed <' . $exception->getMessage() . '>', null, $exception);
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
