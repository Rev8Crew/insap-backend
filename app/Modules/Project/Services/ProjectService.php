<?php


namespace App\Modules\Project\Services;


use App\helpers\IsActiveHelper;
use App\Models\User;
use App\Modules\Project\Models\Project;
use App\Modules\Project\Models\RecordData;

class ProjectService
{
    /**
     * @param array $params
     * @param string $imagePath
     * @return Project
     */
    public function create(array $params, string $imagePath = ''): Project
    {
        $params['image'] = $imagePath ? base64_encode(file_get_contents($imagePath)) : '';

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
        $project->update(['is_active' => IsActiveHelper::ACTIVE_INACTIVE]);
        return $project;
    }

    /**
     * @param Project $project
     * @return Project
     */
    public function activate(Project $project) : Project {
        $project->update(['is_active' => IsActiveHelper::ACTIVE_ACTIVE]);
        return $project;
    }

    /**
     * @param Project $project
     * @param string  $name
     *
     * @return Project
     */
    public function changeName(Project $project, string $name) : Project {
        $project->update(['name' => $name]);
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

    /**
     * @param Project $project
     * @param User    $user
     *
     * @return Project
     */
    public function removeUserFromProject(Project $project, User $user): Project
    {
        $project->users()->detach($user);

        return $project;
    }

    /**
     * @param Project $project
     *
     * @return Project
     * @throws \Exception
     */
    public function delete(Project $project): Project
    {
        $project->delete();
        return $project;
    }
}
