<?php

namespace App\Modules\Processing\Controllers;

use App\Models\Common\Response;
use App\Modules\Appliance\Models\Appliance;
use App\Modules\Importer\Models\Importer\Importer;
use App\Modules\Importer\Models\Importer\ImporterDto;
use App\Modules\Importer\Requests\ImporterCreateRequest;
use App\Modules\Importer\Requests\ImporterDeleteRequest;
use App\Modules\Importer\Requests\ImporterImportRequest;
use App\Modules\Project\Models\Record;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

class ProcessController
{
    /**
     * @param ImporterCreateRequest $request
     * @return Response
     */
    public function create(ImporterCreateRequest $request): Response
    {
        $response = new Response();

        if (!($appliance = Appliance::find($request->input('appliance_id')))) {
            return $response->withError(SymfonyResponse::HTTP_NOT_FOUND, trans('common.not_found'));
        }

        $dto = new ImporterDto($request->input('name'), $appliance, $request->input('description'));
        $this->importerService->create($dto);

        return $response->success();
    }

    /**
     * @param ImporterDeleteRequest $request
     * @return Response
     */
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
    }
}
