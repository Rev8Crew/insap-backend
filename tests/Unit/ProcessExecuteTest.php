<?php


use App\Enums\ActiveStatus;
use App\Enums\Process\ProcessInterpreter;
use App\Enums\Process\ProcessType;
use App\Models\User;
use App\Modules\Appliance\Models\Appliance;
use App\Modules\Plugins\Models\Plugin;
use App\Modules\Processing\Factories\ProcessTypeFactory;
use App\Modules\Processing\Models\Dto\ProcessDto;
use App\Modules\Processing\Models\Dto\ProcessFileDto;
use App\Modules\Processing\Models\Dto\ProcessParamsDto;
use App\Modules\Processing\Models\Interpreter\InterpreterPhp;
use App\Modules\Processing\Models\Interpreter\InterpreterPython;
use App\Modules\Processing\Models\Process;
use App\Modules\Processing\Services\ProcessAppService;
use App\Modules\Processing\Services\ProcessServiceInterface;
use App\Modules\Project\Models\Project;
use App\Modules\Project\Models\Record;
use App\Modules\Project\Models\RecordData;
use App\Modules\Project\Services\RecordService;
use Carbon\Carbon;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class ProcessExecuteTest extends \Tests\TestCase
{
    private Record $record;
    private Appliance $appliance;

    private ?ProcessAppService $processService = null;
    private RecordService $recordService;

    private ProcessServiceInterface $importerService;
    private ProcessServiceInterface $exporterService;

    private ?Project $project = null;

    /**
     * @throws BindingResolutionException
     */
    public function setUp(): void
    {
        \Tests\TestCase::setUp();

        $this->processService = $this->app->make(ProcessAppService::class);
        $this->recordService = $this->app->make(RecordService::class);

        $this->importerService = $this->app->make(ProcessTypeFactory::class)->create(ProcessType::create(ProcessType::IMPORTER));
        $this->exporterService = $this->app->make(ProcessTypeFactory::class)->create(ProcessType::create(ProcessType::EXPORTER));


        $this->appliance = Appliance::create(['id' => Appliance::APPLIANCE_TEST_ID, 'name' => 'test']);

        $this->record = Record::create([
            'id' => Record::TEST_RECORD_ID,
            'name' => 'Test',
            'description' => 'test test',
            'record_data_id' => RecordData::TEST_RECORD_DATA_ID,
            'user_id' => User::TEST_USER_ID
        ]);

        $this->project = Project::find(Project::TEST_PROJECT_ID);
    }

    public function testBasic(): void
    {
        $process = $this->createWithInterpreter(InterpreterPython::class, 'importer_python_ctd.zip');

        $storage = Storage::disk('examples');

        $dataFileDto = $this->getProcessFileDto('data',
            $storage->path('ctd' . DIRECTORY_SEPARATOR . '1' . DIRECTORY_SEPARATOR . '1598429290.xlsx'),
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');

        $coordFileDto = $this->getProcessFileDto('coordinates',
            $storage->path('ctd' . DIRECTORY_SEPARATOR . '1' . DIRECTORY_SEPARATOR . 'Coordinates_1598429290.xlsx'),
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');

        $params = [
            'date_time' => Carbon::now()->format('Y-m-d H:i:s')
        ];

        $result = $this->importerService->executeProcess($process, $this->record, $params, [$dataFileDto, $coordFileDto]);

        $this->assertTrue($result);
        $this->assertTrue($this->recordService->getRecordInfo($this->record)->count() > 0);
        $this->recordService->deleteRecordsInfo($this->record);
    }

    private function createWithInterpreter(string $interpreter, string $filename, ?Plugin $plugin = null): Process
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
            ->setProcessInterpreter($array['interpreter'])
            ->setPluginId(optional($plugin)->id)
            ->setActiveStatus(ActiveStatus::create(ActiveStatus::ACTIVE));

        return $this->processService->createWithApp($processDto, $uploadedFile);
    }

    private function getProcessFileDto(string $alias, string $path, string $mime): ProcessFileDto
    {
        $uploadedFile = UploadedFile::fake()->createWithContent($alias, file_get_contents(
            $path
        ));
        $uploadedFile->mimeType($mime);

        return new ProcessFileDto($uploadedFile, $alias);
    }

    public function testPlugin(): void
    {
        $process = $this->createWithInterpreter(InterpreterPython::class, 'importer_python_adcp.zip', Plugin::first());

        $storage = Storage::disk('examples');

        $dataFileDto = $this->getProcessFileDto('data',
            $storage->path('adcp' . DIRECTORY_SEPARATOR . '1' . DIRECTORY_SEPARATOR . '1597842707_data.txt'),
            'text/plain');

        $refFileDto = $this->getProcessFileDto('ref',
            $storage->path('adcp' . DIRECTORY_SEPARATOR . '1' . DIRECTORY_SEPARATOR . '1597842707_ref.txt'),
            'text/plain');

        $params = [
            'date_time' => Carbon::now()->format('Y-m-d H:i:s'),
            'expedition_number' => 1
        ];

        $this->assertNotNull($process->plugin);

        $result = $this->importerService->executeProcess($process, $this->record, $params, [$dataFileDto, $refFileDto]);

        $this->assertTrue($result);
        $this->assertNotNull($this->record->process);
        $this->assertTrue($this->recordService->getRecordInfo($this->record)->count() > 0);
        $this->recordService->deleteRecordsInfo($this->record);
    }

    public function testExportBasic()
    {
        $importProcess = $this->createWithInterpreter(InterpreterPython::class, 'importer_python_adcp.zip', Plugin::first());

        $storage = Storage::disk('examples');

        $dataFileDto = $this->getProcessFileDto('data',
            $storage->path('adcp' . DIRECTORY_SEPARATOR . '1' . DIRECTORY_SEPARATOR . '1597842707_data.txt'),
            'text/plain');

        $refFileDto = $this->getProcessFileDto('ref',
            $storage->path('adcp' . DIRECTORY_SEPARATOR . '1' . DIRECTORY_SEPARATOR . '1597842707_ref.txt'),
            'text/plain');

        $params = [
            'date_time' => Carbon::now()->format('Y-m-d H:i:s'),
            'expedition_number' => 1
        ];

        $this->importerService->executeProcess($importProcess, $this->record, $params, [$dataFileDto, $refFileDto]);

        $exportProcess = $this->createWithInterpreter(InterpreterPhp::class, 'exporter_php_adcp.zip', Plugin::first());

        $params = [
            'speed' => 2,
            'average' => 50
        ];

        /** @var ProcessParamsDto $result */
        $result = $this->exporterService->executeProcess($exportProcess, $this->record, $params, []);

        $this->assertTrue($result instanceof ProcessParamsDto);
        $this->assertTrue($result->getFiles()->count() === 0);
        $this->assertTrue($result->getParams()->count() > 0);
        $this->assertTrue($result->getData()->count() > 0);

        $this->recordService->deleteRecordsInfo($this->record);
    }
}
