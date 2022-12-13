<?php

namespace App\Modules\Processing\Controllers;

use App\Enums\Process\ProcessInterpreter;
use App\Enums\Process\ProcessType;
use App\Http\Controllers\Controller;
use App\Models\Common\Response;
use App\Modules\Plugins\Models\Plugin;
use App\Modules\Processing\Models\Dto\ProcessDto;
use App\Modules\Processing\Models\Process;
use App\Modules\Processing\Requests\ProcessCreateRequest;
use App\Modules\Processing\Requests\ProcessGetFieldsRequest;
use App\Modules\Processing\Requests\ProcessUpdateArchiveRequest;
use App\Modules\Processing\Requests\ProcessUpdateRequest;
use App\Modules\Processing\Resources\ProcessResource;
use App\Modules\Processing\Services\ProcessAppService;

class ProcessController extends Controller
{
    private ProcessAppService $processService;

    public function __construct(ProcessAppService $processService)
    {
        $this->processService = $processService;
    }

    public function getInterpretersList(): Response
    {
        $response = Response::make();

        return $response->withData(ProcessInterpreter::labelsArray());
    }

    public function getTypesList(): Response
    {
        $response = Response::make();

        $data = collect(ProcessType::labelsArray())->map(function (array $type) {
           return [
               'text' => trans( 'process.' . $type['text']),
               'value' => $type['value'],
           ];
        });

        return $response->withData($data);
    }

    public function createProcess(ProcessCreateRequest $request): Response
    {
        $response = Response::make();

        try {
            $dto = ProcessDto::createFromRequest($request);
            $this->processService->createWithApp($dto, $request->file('archive'));
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
        $data->load(['user', 'appliance', 'fields', 'plugin', 'archiveFile']);

        return $response->withData(ProcessResource::collection($data));
    }

    public function update(ProcessUpdateRequest $request): Response
    {
        $response = Response::make();

        $process = Process::find($request->input('process_id'));
        $plugin = $request->input('plugin_id') ? Plugin::findOrFail($request->input('plugin_id')) : null;

        try {
            $this->processService->update($process, $request->input('name'), $request->input('description'), $plugin);
        } catch (\Throwable $throwable) {
            return $response->catch($throwable);
        }


        return $response->success();
    }

    public function updateApp(ProcessUpdateArchiveRequest $request): Response
    {
        $response = Response::make();

        $process = Process::find($request->input('process_id'));
        $archive = $request->file('archive');

        try {
            $this->processService->updateApp($process, $archive);
        } catch (\Throwable $throwable) {
            return $response->catch($throwable);
        }

        return $response->success();
    }

    public function getFieldsByProcess(ProcessGetFieldsRequest $request): Response
    {
        $response = Response::make();

        $process = Process::find($request->input('process_id'));
        $data = $this->processService->getFieldsByProcess($process);

        return $response->withData($data);
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
