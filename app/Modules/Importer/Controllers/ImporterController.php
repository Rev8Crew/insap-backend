<?php

namespace App\Modules\Importer\Controllers;


use App\Http\Controllers\Controller;
use App\Models\Response\Response;
use App\Models\Response\ResponseErrorStatus;
use App\Models\Response\ResponseStatus;
use App\Modules\Appliance\Models\Appliance;
use App\Modules\Importer\Models\Importer\Importer;
use App\Modules\Importer\Models\Importer\ImporterDto;
use App\Modules\Importer\Requests\ImporterCreateRequest;
use App\Modules\Importer\Requests\ImporterDeleteRequest;
use App\Modules\Importer\Requests\ImporterImportRequest;
use App\Modules\Importer\Services\ImporterService;
use App\Modules\Project\Models\Record;

/**
 * Class AuthController
 *
 * @package App\Http\Controllers\v1\Auth
 */
class ImporterController extends Controller
{
    private ImporterService $importerService;

    /**
     * ImporterController constructor.
     * @param ImporterService $importerService
     */
    public function __construct(ImporterService $importerService)
    {

        $this->importerService = $importerService;
    }

    /**
     * @param ImporterCreateRequest $request
     * @return Response
     */
    public function create(ImporterCreateRequest $request): Response
    {
        $response = new Response();

        if (!($appliance = Appliance::find($request->input('appliance_id')))) {
            return $response->withError(ResponseErrorStatus::ERROR_NOT_FOUND, trans('common.not_found'));
        }

        $dto = new ImporterDto($request->input('name'), $appliance, $request->input('description'));
        $this->importerService->create($dto);

        return $response->withStatus(ResponseStatus::STATUS_OK);
    }

    /**
     * @param ImporterDeleteRequest $request
     * @return Response
     */
    public function delete(ImporterDeleteRequest $request): Response
    {
        $response = new Response();
        if (!($importer = Importer::find($request->input('id')))) {
            return $response->withError(ResponseErrorStatus::ERROR_NOT_FOUND, trans('common.not_found'));
        }

        $this->importerService->delete($importer);
        return $response->withStatus(ResponseStatus::STATUS_OK);
    }

    public function import(ImporterImportRequest $request): Response
    {
        $response = new Response();

        if (!($importer = Importer::find($request->input('importer_id')))) {
            return $response->withError(ResponseErrorStatus::ERROR_NOT_FOUND, trans('common.not_found'));
        }

        if (!($record = Record::find($request->input('record_id')))) {
            return $response->withError(ResponseErrorStatus::ERROR_NOT_FOUND, trans('common.not_found'));
        }

        $this->importerService->import($importer, $record, $request->input('params'), []);
        return $response->withStatus(ResponseStatus::STATUS_OK);
    }
}
