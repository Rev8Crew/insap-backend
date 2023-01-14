<?php

namespace Database\Seeders;

use App\Enums\ActiveStatus;
use App\Enums\Process\ProcessInterpreter;
use App\Enums\Process\ProcessType;
use App\Models\User;
use App\Modules\Appliance\Models\Appliance;
use App\Modules\Appliance\Services\ApplianceService;
use App\Modules\Processing\Models\Dto\ProcessDto;
use App\Modules\Processing\Services\ProcessAppService;
use App\Modules\Project\Models\Project;
use App\Modules\Project\Models\RecordData;
use App\Modules\Project\Services\ProjectService;
use Faker\Factory;
use Illuminate\Database\Seeder;
use Illuminate\Http\UploadedFile;
use Storage;

class StageSeeder extends Seeder
{
    public function run(ProjectService $projectService, ApplianceService $applianceService, ProcessAppService $processAppService)
    {
        $defaultAppliance = Appliance::create(['id' => Appliance::APPLIANCE_TEST_ID, 'name' => 'Default Appliance']);
        $appliance = Appliance::create(['id' => Appliance::APPLIANCE_ADCP_ID, 'name' => 'ADCP']);
        $project = Project::firstOrFail();
        $user = User::firstOrFail();

        $projectService->addUserToProject($project, $user, true);
        $applianceService->addApplianceToProject($defaultAppliance, $project);
        $applianceService->addApplianceToProject($appliance, $project);

        RecordData::factory()->count(1)->state([
            'project_id' => Project::TEST_PROJECT_ID
        ])->create();

        // Создаем дефолтный экспортер и импортер
        $storage = Storage::disk('examples');
        $uploadedFile = UploadedFile::fake()->createWithContent('default_importer.zip', file_get_contents($storage->path('default_importer.zip')));

        $processDto = ProcessDto::make(
            ProcessType::create(ProcessType::IMPORTER),
            ProcessInterpreter::create(ProcessInterpreter::PHP),
            $project->id
        )
            ->setName('Default Importer')
            ->setApplianceId($defaultAppliance->id)
            ->setActiveStatus(ActiveStatus::create(ActiveStatus::ACTIVE));

        $process = $processAppService->createWithApp($processDto, $uploadedFile);
    }
}
