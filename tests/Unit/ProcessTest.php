<?php

namespace Tests\Unit;

use App\Enums\ActiveStatus;
use App\Enums\Process\ProcessInterpreter;
use App\Enums\Process\ProcessType;
use App\Modules\Appliance\Models\Appliance;
use App\Modules\Processing\Models\Dto\ProcessDto;
use App\Modules\Processing\Models\Interpreter\InterpreterGo;
use App\Modules\Processing\Models\Interpreter\InterpreterPhp;
use App\Modules\Processing\Models\Interpreter\InterpreterPython;
use App\Modules\Processing\Models\Process;
use App\Modules\Processing\Services\ProcessAppService;
use App\Modules\Project\Models\Project;
use Illuminate\Http\UploadedFile;
use Storage;
use Tests\TestCase;

class ProcessTest extends TestCase
{
    private ?ProcessAppService $processService = null;
    private ?Appliance $appliance = null;

    private ?Project $project = null;

    public function setUp(): void
    {
        parent::setUp();

        // TODO: change to ApplianceService
        $this->appliance = Appliance::create(['id' => Appliance::APPLIANCE_TEST_ID, 'name' => 'test']);

        // Create all services
        $this->processService = $this->app->make(ProcessAppService::class);

        $this->project = Project::find(Project::TEST_PROJECT_ID);
    }

    private function createWithInterpreter(string $interpreter = ProcessInterpreter::PHP, string $filename = 'importer_php.zip'): bool
    {
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
            $this->project->id
        )
            ->setName($array['name'])
            ->setApplianceId($this->appliance->id)
            ->setActiveStatus(ActiveStatus::create(ActiveStatus::ACTIVE));

        $process = $this->processService->createWithApp($processDto, $uploadedFile);

        $this->assertInstanceOf(Process::class, $process);

        $this->assertEquals($array['name'], $process->name);
        $this->assertEquals($array['type'], ProcessType::create($process->type));
        $this->assertEquals($array['interpreter'], ProcessInterpreter::create($process->interpreter));
        $this->assertEquals(ActiveStatus::ACTIVE, $process->is_active);

        Storage::disk('process')->assertExists($process->id)->deleteDirectory($process->id);
        return true;
    }

    public function testCreate()
    {
        $this->assertTrue($this->createWithInterpreter(InterpreterPhp::class, 'importer_php.zip'));
        $this->assertTrue($this->createWithInterpreter(InterpreterPython::class, 'importer_python.zip'));
        $this->assertTrue($this->createWithInterpreter(InterpreterGo::class, 'importer_go.zip'));
    }

    public function testDelete()
    {
        $storage = Storage::disk('examples');
        $uploadedFile = UploadedFile::fake()->createWithContent('importer_php.zip', file_get_contents($storage->path('importer_php.zip')));

        $array = [
            'name' => 'test',
            'type' => ProcessType::create(ProcessType::IMPORTER),
            'interpreter' => ProcessInterpreter::create(InterpreterPhp::class)
        ];

        // Create Entities
        $processDto = ProcessDto::make(
            $array['type'],
            $array['interpreter'],
            $this->project->id
        )
            ->setName($array['name'])
            ->setApplianceId($this->appliance->id)
            ->setActiveStatus(ActiveStatus::create(ActiveStatus::ACTIVE));

        $process = $this->processService->createWithApp($processDto, $uploadedFile);

        $id = $process->id;
        $this->processService->delete($process);

        $this->assertNull(Process::find($id));
        Storage::disk('process')->assertMissing($id);
    }
}
