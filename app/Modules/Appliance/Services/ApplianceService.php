<?php

namespace App\Modules\Appliance\Services;

use App\Enums\ActiveStatus;
use App\Enums\Permission;
use App\Models\User;
use App\Modules\Appliance\Models\Appliance;
use App\Modules\Project\Models\Project;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class ApplianceService
{
    public function create(string $name, string $description, ActiveStatus $activeStatus): Appliance
    {
        return Appliance::create(['name' => $name, 'description' => $description, 'is_active' => $activeStatus->getValue()]);
    }

    public function getAllAppliances(): Collection
    {
        return Appliance::whereIsActive(ActiveStatus::ACTIVE)->get();
    }

    public function getAllAppliancesByProjectAndUser(Project $project, User $user): Collection
    {
        return Appliance::whereIsActive(ActiveStatus::ACTIVE)
            ->whereHas('projects', function (Builder $query) use ($project) {
            $query->where('project_id', $project->id);
        })->whereHas('projects.users', function (Builder $query) use ($project) {
            $query->where('user_id', $project->id);
        })->get();
    }

    public function addApplianceToProject(Appliance $appliance, Project $project): Model
    {
        return $appliance->projects()->save($project);
    }
}
