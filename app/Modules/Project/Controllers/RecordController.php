<?php

namespace App\Modules\Project\Controllers;

use App\Enums\ImporterField;
use App\Enums\Process\ProcessType;
use App\Http\Controllers\Controller;
use App\Models\Common\Response;
use App\Modules\Processing\Factories\ProcessTypeFactory;
use App\Modules\Processing\Models\Dto\ProcessFileDto;
use App\Modules\Processing\Models\Process;
use App\Modules\Processing\Services\ProcessAppService;
use App\Modules\Project\DTO\RecordCreateDto;
use App\Modules\Project\DTO\RecordFieldDto;
use App\Modules\Project\Requests\GetRecordByIdRequest;
use App\Modules\Project\Requests\RecordCreateRequest;
use App\Modules\Project\Requests\RecordDataRequest;
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
        $this->recordService->createFromDto($dto);

        return $response->success();
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

    public function import(): Response
    {
        $response = Response::make();

        return $response->success();
    }

    public function getById(GetRecordByIdRequest $request): Response
    {
        $response = Response::make();

        $data = $this->recordService->getRecordById($request->input('id'));
        $data->load(['user', 'process', 'recordData']);

        return $response->withData( RecordResource::make($data));
    }

    public function createWithInstall(RecordCreateRequest $request, ProcessTypeFactory $processTypeFactory): Response {
        $response = Response::make();

        $process = Process::find($request->input('process_id'));
        $dto = RecordCreateDto::fromRequest($request);

        $fields = collect($request->input('fields'))->map( fn(string $field) => RecordFieldDto::makeFromArray(json_decode($field, true, 512, JSON_THROW_ON_ERROR)));
        $files = $fields->filter( fn(RecordFieldDto $dto) => $dto->getFieldType()->getValue() === ImporterField::FIELD_FILE)
            ->map( fn(RecordFieldDto $dto) => ProcessFileDto::make($request->file('_file_alias_' . $dto->getAlias()), $dto->getAlias()));

        // Удаляем все данные с пустыми значениями
        $fields = $fields->filter( fn(RecordFieldDto $dto) => $dto->getValue())->mapWithKeys( fn(RecordFieldDto $dto) => [$dto->getAlias() => $dto->getValue()]);
        $record = $this->recordService->createFromDto($dto);

        $importerService = $processTypeFactory->create(ProcessType::create(ProcessType::IMPORTER));

        try {
            $importerService->executeProcess($process, $record, $fields->all(), $files->all());
        } catch (\Throwable $throwable) {
            return $response->catch($throwable);
        }

        return $response->withData( $request->allFiles());
    }
}
