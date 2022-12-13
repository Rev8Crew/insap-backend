<?php

namespace App\Modules\Project\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Common\Response;
use App\Modules\Project\Requests\RecordDataCreateRequest;
use App\Modules\Project\Requests\RecordDataRequest;
use App\Modules\Project\Resources\RecordDataResource;
use App\Modules\Project\Services\RecordDataService;

class RecordDataController extends Controller
{
    private RecordDataService $recordDataService;

    public function __construct(RecordDataService $recordDataService)
    {
        $this->recordDataService = $recordDataService;
    }
    public function getRecordDataById(RecordDataRequest $request): Response
    {
        $response = Response::make();

        $recordData = $this->recordDataService->getRecordDataById($request->input('record_data_id'));

        return $response->withData(RecordDataResource::make($recordData));
    }

    public function createRecordData(RecordDataCreateRequest $request): Response
    {
        $response = Response::make();

        try {
            $this->recordDataService->create($request->only(['project_id', 'name', 'description', 'date_start', 'date_end']), $request->file('image'), $request->user());
        } catch (\Throwable $e) {
            return $response->catch($e);
        }

        return $response->success();
    }
}
