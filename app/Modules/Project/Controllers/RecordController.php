<?php

namespace App\Modules\Project\Controllers;

use App\Models\Common\Response;
use App\Modules\Project\Models\RecordData;
use App\Modules\Project\Requests\GetRecordsByRecordData;
use App\Modules\Project\Resources\RecordResource;
use App\Modules\Project\Services\RecordDataService;
use App\Modules\Project\Services\RecordService;

class RecordController
{
    private RecordService $recordService;
    private RecordDataService $recordDataService;

    public function __construct(RecordService $recordService, RecordDataService $recordDataService)
    {
        $this->recordService = $recordService;
        $this->recordDataService = $recordDataService;
    }

    public function getRecordsByRecordData(GetRecordsByRecordData $request) : Response {
        $response = Response::make();

        $recordData = $this->recordDataService->getRecordDataById($request->input('record_data_id'));

        $records = $this->recordService->getRecordsByRecordData($recordData);
        $records->load(['user']);

        return $response->withData(RecordResource::collection($records));
    }
}
