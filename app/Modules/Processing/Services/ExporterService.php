<?php

namespace App\Modules\Processing\Services;

use App\Enums\Process\ProcessType;
use App\Models\Common\Response;
use App\Modules\Processing\Models\Dto\ProcessFileDto;
use App\Modules\Processing\Models\Dto\ProcessParamsDto;
use App\Modules\Processing\Models\Process;
use App\Modules\Project\Models\Record;
use App\Modules\Project\Services\RecordService;
use App\Services\ZipperService;
use RuntimeException;
use Throwable;

class ExporterService implements ProcessServiceInterface
{
    protected RecordService $recordService;
    protected ProcessExecuteService $processExecuteService;
    protected ZipperService $zipperService;

    private Response $response;

    public function __construct(
        RecordService         $recordService,
        ProcessExecuteService $processExecuteService,
        ZipperService         $zipperService
    )
    {
        $this->recordService = $recordService;
        $this->processExecuteService = $processExecuteService;
        $this->zipperService = $zipperService;
    }

    public function executeProcess(Process $process, Record $record, array $params = [], array $files = [])
    {
        ini_set('memory_limit', '2G');

        try {
            $processParamsDto = new ProcessParamsDto($params);
            $processParamsDto->setFilesFromUploaded($files);

            $processParamsDto->setData($this->recordService->getRecordInfo($record)->toArray());

            // Processing data
            $processParamsDto = $this->processExecuteService->execute(ProcessType::create(ProcessType::EXPORTER), $process, $processParamsDto);

            return $processParamsDto;

        } catch (Throwable $exception) {
            throw new RuntimeException('[Exporter] export event failed <' . $exception->getMessage() . '>', null, $exception);
        }
    }

    public function formatResponse(ProcessParamsDto $processParamsDto, Record $record, Process $process)
    {
        $response = new Response();

        if ($processParamsDto->getFiles()->count() > 0) {
            // Если несколько файлов, то помещаем их в архив
            if ($processParamsDto->getFiles()->count() > 1) {
                $name = $process->name . ' ' . $record->name . '.zip';
                return $this->zipperService->createAndDownloadZip($name, $processParamsDto->getFiles());
            }

            /** @var ProcessFileDto $file */
            $file = $processParamsDto->getFiles()->first();
            return $response->file($file->getUploadedFile()->getRealPath());
        }

        return $response->withData($processParamsDto->getData());
    }
}
