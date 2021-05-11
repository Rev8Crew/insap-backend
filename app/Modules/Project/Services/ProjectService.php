<?php


namespace App\Modules\Project\Services;


use App\helpers\IsActiveHelper;
use App\Models\User;
use App\Modules\Project\Models\Project;

class ProjectService
{
    /**
     * @param array $params
     * @param string $imagePath
     * @return Project
     */
    public function create(array $params, string $imagePath): Project
    {
        $params['image'] = base64_encode(file_get_contents($imagePath));

        $project = Project::create(
            $params
        );

        return $project;
    }

    /**
     * @param Project $project
     * @return Project
     */
    public function deactivate(Project $project) : Project {
        $project->fill([
            'is_active' => IsActiveHelper::ACTIVE_INACTIVE
        ]);
        $project->save();

        return $project;
    }

    /**
     * @param Project $project
     * @param User $user
     * @return Project
     */
    public function addUserToProject(Project $project, User $user): Project
    {
        $project->users()->attach($user);

        return $project;
    }

    public function RemoveUserFromProject(Project $project, User $user): Project
    {
        $project->users()->detach($user);

        return $project;
    }
}
