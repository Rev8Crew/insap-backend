<?php

namespace Database\Seeders;

use App\Models\User;
use App\Modules\Appliance\Models\Appliance;
use App\Modules\Appliance\Services\ApplianceService;
use App\Modules\Project\Models\Project;
use App\Modules\Project\Models\RecordData;
use App\Modules\Project\Services\ProjectService;
use Faker\Factory;
use Illuminate\Database\Seeder;

class StageSeeder extends Seeder
{
    public function run(ProjectService $projectService, ApplianceService $applianceService)
    {
        $appliance = Appliance::create(['id' => Appliance::APPLIANCE_TEST_ID, 'name' => 'ADCP']);

        $projectService->addUserToProject(Project::first(), User::first(), true);
        $applianceService->addApplianceToProject($appliance, Project::first());

        RecordData::factory()->count(1)->state([
            'project_id' => Project::TEST_PROJECT_ID
        ])->create();
    }
}
