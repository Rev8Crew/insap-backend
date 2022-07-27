<?php

namespace Database\Seeders;

use App\Models\User;
use App\Modules\Appliance\Models\Appliance;
use App\Modules\Appliance\Services\ApplianceService;
use App\Modules\Project\Models\Project;
use App\Modules\Project\Services\ProjectService;
use Illuminate\Database\Seeder;

class StageSeeder extends Seeder
{
    public function run(ProjectService $projectService, ApplianceService $applianceService)
    {
        $appliance = Appliance::create(['id' => Appliance::APPLIANCE_TEST_ID, 'name' => 'test']);

        $projectService->addUserToProject(Project::first(), User::first());
        $applianceService->addApplianceToProject($appliance, Project::first());
    }
}
