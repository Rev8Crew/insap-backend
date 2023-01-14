<?php

namespace App\Modules\Processing\Services;

use App\Enums\ActiveStatus;
use App\Enums\ImportStatus;
use App\Enums\Process\ProcessOption;
use App\Enums\Process\ProcessType;
use App\Events\PreImportEvent;
use App\Exceptions\ProcessException;
use App\Modules\Plugins\Services\PluginService;
use App\Modules\Processing\Models\Dto\ProcessParamsDto;
use App\Modules\Processing\Models\Process;
use App\Modules\Project\Models\Record;
use App\Modules\Project\Models\RecordInfo;
use App\Modules\Project\Services\RecordService;
use Illuminate\Support\Facades\DB;
use Mts88\MongoGrid\Services\MongoGrid;
use Ramsey\Uuid\Uuid;
use RuntimeException;
use Throwable;

class ImporterService implements ProcessServiceInterface
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

    /**
     * @throws Throwable
     * @throws ProcessException
     */
    public function executeProcess(Process $process, Record $record, array $params = [], array $files = []): bool
    {
        ini_set('memory_limit', '2G');

        throw_if($record->import_status === ImportStatus::FINAL, new \RuntimeException("Can't execute process in FINAL state"));

        try {
            DB::beginTransaction();

            PreImportEvent::dispatch($process, $record, $params, $files);

            $pluginService = $this->pluginService->getPluginService($process->plugin);

            $processParamsDto = new ProcessParamsDto($params);
            $processParamsDto->setFilesFromUploaded($files);

            if ($pluginService->isRecordHasImport($record) && $process->getOptionsByKey(ProcessOption::TRANSFER_DATA_ON_MULTIPLE_IMPORT) === true) {
                $processParamsDto->setData($pluginService->getDataFromRecord($record));
            }

            $record->import_log = 'Start import process...' . PHP_EOL;

            // Processing data
            $processParamsDto = $this->processExecuteService->execute(ProcessType::create(ProcessType::IMPORTER), $process, $processParamsDto);

            // Add to DB
            $pluginService->addDataToDatabase($record, $processParamsDto, $process);

            $fileIds = [];
            foreach ($processParamsDto->getFiles() as $file) {

                $fileIds[] = $this->mongoGrid->storeFile($file->getUploadedFile()->getContent(), Uuid::uuid4(), [
                    'record_id' => $record->id,
                    'filename' => $file->getUploadedFile()->getFilename(),
                    'extension' => $file->getUploadedFile()->getClientOriginalExtension(),
                    'type' => $file->getAlias()
                ]);

            }

            $record->files = array_merge($record->files ?? [], $fileIds);
            $record->params = $processParamsDto->getParams()->all();
            $record->import_status = ImportStatus::SUCCESS;
            $record->process()->associate($process);

            $record->save();

            DB::commit();
        } catch (Throwable $exception) {
            DB::rollBack();

            $record->import_status = ImportStatus::ERROR;

            if ($exception instanceof ProcessException) {
                $record->import_log = $exception->getMessage() . ' Processing Error ' . $exception->getProcessError();
            } else {
                $record->import_log = $exception->getMessage() . ' Trace: ' . $exception->getTraceAsString();
            }

            $record->save();

            $this->recordService->deleteRecordFiles($record);

            throw $exception;
        }

        return true;
    }
}
