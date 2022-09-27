<?php

namespace App\Modules\Processing\Controllers;

use App\Enums\Process\ProcessInterpreter;
use App\Http\Controllers\Controller;
use App\Models\Common\Response;
use App\Modules\Processing\Models\Dto\ProcessDto;
use App\Modules\Processing\Requests\ProcessCreateRequest;
use App\Modules\Processing\Resources\ProcessResource;
use App\Modules\Processing\Services\ProcessService;

class ProcessController extends Controller
{
    private ProcessService $processService;

    public function __construct(ProcessService $processService)
    {
        $this->processService = $processService;
    }

    public function getProcessesTypeList(): Response
    {
        $response = Response::make();

        return $response->withData(ProcessInterpreter::labelsArray());
    }
    public function getInterpretersList(): Response
    {
        $response = Response::make();

        return $response->withData(ProcessInterpreter::labelsArray());
    }

    public function createProcess(ProcessCreateRequest $request): Response
    {
        $response = Response::make();

        try {
            $dto = ProcessDto::createFromRequest($request);
            $this->processService->create($dto, $request->file('archive'));
        } catch (\Throwable $throwable) {
            return $response->catch($throwable);
        }

        return $response->success();
    }

    public function getAllByUserDefaultProject(): Response
    {
        $response = Response::make();
        $user = request()->user();

        $data = $this->processService->getAllByProject($user->current_project);

        return $response->withData(ProcessResource::collection($data));
    }
/*    public function create(ImporterCreateRequest $request): Response
    {
        $response = new Response();

        if (!($appliance = Appliance::find($request->input('appliance_id')))) {
            return $response->withError(SymfonyResponse::HTTP_NOT_FOUND, trans('common.not_found'));
        }

        $dto = new ImporterDto($request->input('name'), $appliance, $request->input('description'));
        $this->importerService->create($dto);

        return $response->success();
    }

    public function delete(ImporterDeleteRequest $request): Response
    {
        $response = new Response();
        if (!($importer = Importer::find($request->input('id')))) {
            return $response->withError(SymfonyResponse::HTTP_NOT_FOUND, trans('common.not_found'));
        }

        $this->importerService->delete($importer);
        return $response->success();
    }

    public function import(ImporterImportRequest $request): Response
    {
        $response = new Response();

        if (!($importer = Importer::find($request->input('importer_id')))) {
            return $response->withError(SymfonyResponse::HTTP_NOT_FOUND, trans('common.not_found'));
        }

        if (!($record = Record::find($request->input('record_id')))) {
            return $response->withError(SymfonyResponse::HTTP_NOT_FOUND, trans('common.not_found'));
        }

        $this->importerService->import($importer, $record, $request->input('params'), []);
        return $response->success();
    }*/
}
