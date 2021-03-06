<?php


namespace App\Modules\Project\Services;


use App\Models\User;
use App\Modules\Project\Models\Project;
use App\Services\File\FileService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;

class ProjectService
{
    private FileService $fileService;
    public function __construct(FileService $fileService)
    {
        $this->fileService = $fileService;
    }

    /**
     * @param array $params
     * @param ?UploadedFile $file
     * @param User|null $user
     * @return Project
     */
    public function create(array $params, ?UploadedFile $file = null, ?User $user = null): Project
    {
        if ($file) {
            $params['image_id'] = $this->fileService->buildFromUploadedFile($file, $user)->id;
        }

        $project = Project::create(
            $params
        );

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
        $this->deleteImage($project);
        $project->delete();
        return $project;
    }

    /**
     * @param Project $project
     * @param UploadedFile $uploadedFile
     * @param User $user
     * @return bool
     * @throws \Exception
     */
    public function changeImage(Project $project, UploadedFile $uploadedFile, User $user): bool
    {
        $this->deleteImage($project);

        $file = $this->fileService->buildFromUploadedFile($uploadedFile, $user);
        return $project->update(['image_id' => $file->id]);
    }

    public function getProjectsByUser(User $user): Collection
    {
        return Project::whereHas('users', function (Builder $query) use ($user) {
            $query->where('user_id', $user->id);
        })->orderBy('order')->active()->get();
    }

    /**
     * @param Project $project
     * @throws \Exception
     */
    private function deleteImage(Project $project) {
        // If already exists
        if ($project->image_id) {
            $this->fileService->delete($project->imageFile);
        }
    }
}
