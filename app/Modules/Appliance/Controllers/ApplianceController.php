<?php

namespace App\Modules\Appliance\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Common\Response;
use App\Modules\Appliance\Requests\GetByProjectRequest;
use App\Modules\Appliance\Resources\ApplianceResource;
use App\Modules\Appliance\Services\ApplianceService;
use App\Modules\Project\Models\Project;

class ApplianceController extends Controller
{
    private ApplianceService $applianceService;

    public function __construct(ApplianceService $applianceService)
    {
        $this->applianceService = $applianceService;
    }

    /**
     * @OA\Post (
     *     path="web/appliances",
     *     tags={"appliances", "web"},
     *     summary="Приборы",
     *     description="Получения списка всех приборов",
     *     @OA\Response(
     *          response=200,
     *          description="Успех",
     *          @OA\JsonContent(ref="#/components/schemas/ApplianceResource")
     *     ),
     *     @OA\Response(
     *          response=403,
     *          description="Ошибка в правах"
     *     )
     * )
     * @return Response
     */
    public function index(): Response
    {
        $response = new Response();
        $appliances = $this->applianceService->getAllAppliances();

        return $response->withData( ApplianceResource::collection($appliances) );
    }

    /**
     * @OA\Post (
     *     path="web/appliances/by-project-and-user",
     *     tags={"appliances", "web"},
     *     summary="Приборы",
     *     description="Получения списка всех приборов для проекта",
     *     @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(ref="#/components/schemas/ApplianceByProjectRequest")
     *     ),
     *     @OA\Response(
     *          response=200,
     *          description="Успех",
     *          @OA\JsonContent(ref="#/components/schemas/ApplianceResource")
     *     ),
     *     @OA\Response(
     *          response=403,
     *          description="Ошибка в правах"
     *     )
     * )
     * @param GetByProjectRequest $request
     * @return Response
     */
    public function getAppliancesByProject(GetByProjectRequest $request): Response
    {
        $response = new Response();
        $project = Project::find($request->input('project_id'));
        $appliances = $this->applianceService->getAllAppliancesByProjectAndUser($project, request()->user());

        return $response->withData( ApplianceResource::collection($appliances) );
    }
}
