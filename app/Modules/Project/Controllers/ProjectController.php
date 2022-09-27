<?php

namespace App\Modules\Project\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Common\Response;
use App\Modules\Project\Models\Project;
use App\Modules\Project\Requests\ProjectCreateRequest;
use App\Modules\Project\Resources\ProjectResource;
use App\Modules\Project\Services\ProjectService;

/**
 * Class ProjectController
 * @package App\Modules\Project\Controllers
 */
class ProjectController extends Controller
{
    private ProjectService $projectService;

    public function __construct(ProjectService $projectService)
    {
        $this->projectService = $projectService;
    }

    /**
     * @return Response
     */
    public function get(): Response
    {
        $response = new Response();
        $resource = ProjectResource::collection( Project::all() );

        return $response->withData( $resource->toArray(request()) );
    }

    public function getProjectsByUser(): Response
    {
        $response = new Response();
        $projects = $this->projectService->getProjectsByUser(request()->user());
        $projects->load(['recordsData', 'recordsData.records', 'recordsData.creatorUser']);

        return $response->withData( ProjectResource::collection($projects) );
    }

    /**
     * @param ProjectCreateRequest $request
     * @return Response
     */
    public function create(ProjectCreateRequest $request): Response
    {
        $response = new Response();

        /** @var ProjectService $projectService */
        $projectService = app(ProjectService::class);
        $projectService->create($request->except(['image']), $request->file('image'), $request->user());

        return $response->success();
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

        return $response->success();
    }
}
