<?php

namespace App\Modules\Project\Controllers;

use App\Enums\Process\ProcessType;
use App\Http\Controllers\Controller;
use App\Models\Common\Response;
use App\Modules\Processing\Factories\ProcessTypeFactory;
use App\Modules\Processing\Services\ProcessAppService;
use App\Modules\Project\DTO\RecordCreateDto;
use App\Modules\Project\DTO\RecordFieldDto;
use App\Modules\Project\Requests\GetRecordByIdRequest;
use App\Modules\Project\Requests\RecordCreateRequest;
use App\Modules\Project\Requests\RecordDataRequest;
use App\Modules\Project\Requests\RecordIdRequest;
use App\Modules\Project\Requests\RecordImportRequest;
use App\Modules\Project\Requests\RecordUpdateRequest;
use App\Modules\Project\Resources\RecordResource;
use App\Modules\Project\Services\RecordDataService;
use App\Modules\Project\Services\RecordService;
use Carbon\Carbon;

class RecordController extends Controller
{
    private RecordService $recordService;
    private RecordDataService $recordDataService;
    private ProcessAppService $processService;

    public function __construct(RecordService $recordService, RecordDataService $recordDataService, ProcessAppService $processService)
    {
        $this->recordService = $recordService;
        $this->recordDataService = $recordDataService;
        $this->processService = $processService;
    }

    public function getRecordsByRecordData(RecordDataRequest $request) : Response {
        $response = Response::make();

        $recordData = $this->recordDataService->getRecordDataById($request->input('record_data_id'));

        $records = $this->recordService->getRecordsByRecordData($recordData);
        $records->load(['user']);

        return $response->withData(RecordResource::collection($records));
    }

    public function create(RecordCreateRequest $request): Response
    {
        $response = Response::make();

        $dto = RecordCreateDto::fromRequest($request);
        $record = $this->recordService->createFromDto($dto);

        return $response->withData($record);
    }

    public function update(RecordUpdateRequest $request): Response
    {
        $response = Response::make();

        $record = $this->recordService->getRecordById($request->input('record_id'));
        $process = $request->input('process_id') ? $this->processService->getById($request->input('process_id')) : null;

        try {
            $this->recordService->update(
                $record,
                $request->input('name'),
                $request->input('description'),
                Carbon::parse($request->input('date')),
                $process
            );
        } catch (\Throwable $throwable) {
            return $response->catch($throwable);
        }

        return $response->success();
    }

    public function import(RecordImportRequest $request, ProcessTypeFactory $processTypeFactory): Response
    {
        $response = Response::make();

        $record = $this->recordService->getRecordById($request->input('record_id'));

        $fields = $this->recordService->makeFieldDtoFromArray($request->input('fields'));
        $files = $this->recordService->getFilesFromFieldsCollectionAndRequest($fields, $request);

        // Удаляем все данные с пустыми значениями(после того как выделили среди них файлы)
        $fields = $fields->filter( fn(RecordFieldDto $dto) => $dto->isNotEmpty())->mapWithKeys( fn(RecordFieldDto $dto) => [$dto->getAlias() => $dto->getValue()]);

        $importerService = $processTypeFactory->create(ProcessType::create(ProcessType::IMPORTER));

        try {
            $importerService->executeProcess($record->process, $record, $fields->all(), $files->all());
        } catch (\Throwable $throwable) {
            return $response->catch($throwable);
        }

        return $response->success();
    }

    public function deleteRecordsInRecord(RecordIdRequest $request): Response
    {
        $response = Response::make();

        $record = $this->recordService->getRecordById($request->input('record_id'));

        try {
            $this->recordService->deleteRecordsFromRecord($record);
        } catch (\Throwable $throwable) {
            return $response->catch($throwable);
        }

        return $response->success();
    }

    public function getById(GetRecordByIdRequest $request): Response
    {
        $response = Response::make();

        $data = $this->recordService->getRecordById($request->input('id'));
        $data->load(['user', 'process', 'recordData']);

        return $response->withData( RecordResource::make($data));
    }
}
