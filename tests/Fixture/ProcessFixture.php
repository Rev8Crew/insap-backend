<?php
declare(strict_types=1);

namespace Tests\Fixture;

use App\Enums\ActiveStatus;
use App\Enums\Process\ProcessInterpreter;
use App\Enums\Process\ProcessType;
use App\Modules\Appliance\Models\Appliance;
use App\Modules\Plugins\Models\Plugin;
use App\Modules\Processing\Models\Dto\ProcessDto;
use App\Modules\Processing\Models\Dto\ProcessFileDto;
use App\Modules\Processing\Models\Process;
use App\Modules\Processing\Services\ProcessAppService;
use App\Modules\Project\Models\Project;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Storage;

class ProcessFixture
{
    use WithFaker;

    private ProcessAppService $processAppService;
    private ApplianceFixture $applianceFixture;

    public function __construct(ProcessAppService $processAppService, ApplianceFixture $applianceFixture)
    {
        $this->processAppService = $processAppService;
        $this->applianceFixture = $applianceFixture;
    }

    public function createWithInterpreter(
        string $interpreter,
        string $filename,
        ?Plugin $plugin = null,
        ?Appliance $appliance,
        ?Project $project
    ): Process
    {
        $appliance = $appliance ?? $this->applianceFixture->create('test');

        $storage = Storage::disk('examples');
        $uploadedFile = UploadedFile::fake()->createWithContent($filename, file_get_contents($storage->path($filename)));

        $array = [
            'name' => 'test',
            'type' => ProcessType::create(ProcessType::IMPORTER),
            'interpreter' => ProcessInterpreter::create($interpreter)
        ];

        // Create Entities
        $processDto = ProcessDto::make(
            $array['type'],
            $array['interpreter'],
            $project->id
        )
            ->setName($array['name'])
            ->setApplianceId($appliance->id)
            ->setProcessInterpreter($array['interpreter'])
            ->setPluginId(optional($plugin)->id)
            ->setActiveStatus(ActiveStatus::create(ActiveStatus::ACTIVE));

        return $this->processAppService->createWithApp($processDto, $uploadedFile);
    }

    public function getProcessFileDto(string $alias, string $path, string $mime): ProcessFileDto
    {
        $uploadedFile = UploadedFile::fake()->createWithContent($alias, file_get_contents(
            $path
        ));
        $uploadedFile->mimeType($mime);

        return new ProcessFileDto($uploadedFile, $alias);
    }
}
