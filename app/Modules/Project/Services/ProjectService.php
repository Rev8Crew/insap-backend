<?php


namespace App\Modules\Project\Services;


use App\Models\User;
use App\Modules\Project\Models\Project;
use App\Services\File\FileService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\UploadedFile;

class ProjectService
{
    private FileService $fileService;
    public function __construct(FileService $fileService)
    {
        $this->fileService = $fileService;
    }

    public function create(array $params, ?UploadedFile $file = null, ?User $user = null): Project
    {
        if ($file) {
            $params['image_id'] = $this->fileService->createFromUploadedFile($file, $user)->id;
        }

        $project = Project::create(
            $params
        );

        return $project;
    }

    public function changeName(Project $project, string $name) : Project {
        $project->update(['name' => $name]);
        return $project;
    }

    public function addUserToProject(Project $project, User $user, bool $current = false): Project
    {
        $project->users()->attach($user, compact('current'));
        $project->save();

        return $project;
    }

    public function removeUserFromProject(Project $project, User $user): Project
    {
        $project->users()->detach($user);
        $project->save();

        return $project;
    }

    public function delete(Project $project): Project
    {
        $this->deleteImage($project);
        $project->delete();
        return $project;
    }

    public function changeImage(Project $project, UploadedFile $uploadedFile, User $user): bool
    {
        $this->deleteImage($project);

        $file = $this->fileService->createFromUploadedFile($uploadedFile, $user);
        return $project->update(['image_id' => $file->id]);
    }

    public function getProjectsByUser(User $user): Collection
    {
        return Project::whereHas('users', function (Builder $query) use ($user) {
            $query->where('user_id', $user->id);
        })->orderBy('order')->active()->get();
    }

    private function deleteImage(Project $project) {
        // If already exists
        if ($project->image_id) {
            $this->fileService->delete($project->imageFile);
        }
    }
}
