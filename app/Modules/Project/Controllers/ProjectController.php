<?php

namespace App\Modules\Project\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Response\Response;
use App\Models\Response\ResponseStatus;
use App\Modules\Project\Models\Project;
use App\Modules\Project\Resources\ProjectResource;
use App\Modules\Project\Services\ProjectService;

/**
 * Class ProjectController
 * @package App\Modules\Project\Controllers
 */
class ProjectController extends Controller
{
    /**
     * @return Response
     */
    public function get(): Response
    {
        $response = new Response();
        $resource = ProjectResource::collection( Project::all() );

        return $response->withData( $resource->toArray(request()) );
    }

    /**
     * @return Response
     */
    public function create(): Response
    {
        $response = new Response();

        /** @var ProjectService $projectService */
        $projectService = app(ProjectService::class);
        $projectService->create(request()->all());

        return $response->withStatus(ResponseStatus::STATUS_OK);
    }

    /**
     * @param Project $project
     * @return Response
     */
    public function view(Project $project): Response
    {
        $response = new Response();
        $resource = new ProjectResource($project);

        return $response->withData($resource);
    }

    /**
     * @param Project $project
     * @return Response
     * @throws \Exception
     */
    public function delete(Project $project): Response
    {
        $response = new Response();

        /** @var ProjectService $projectService */
        $projectService = app(ProjectService::class);
        $projectService->delete($project);

        return $response->withStatus(ResponseStatus::STATUS_OK);
    }
}
