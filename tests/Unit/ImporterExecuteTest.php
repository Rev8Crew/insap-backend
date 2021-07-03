<?php

namespace Tests\Unit;

use App\Models\User;
use App\Modules\Appliance\Models\Appliance;
use App\Modules\Importer\Models\Importer\Importer;
use App\Modules\Importer\Models\Importer\ImporterDto;
use App\Modules\Importer\Models\ImporterEvents\ImporterEvent;
use App\Modules\Importer\Models\ImporterEvents\ImporterEventDto;
use App\Modules\Importer\Models\ImporterEvents\ImporterEventEvent;
use App\Modules\Importer\Models\ImporterEvents\ImporterEventFile;
use App\Modules\Importer\Models\ImporterEvents\ImporterEventInterpreter;
use App\Modules\Importer\Models\ImporterInterpreter\ImporterInterpreterPython;
use App\Modules\Importer\Services\ImporterEventService;
use App\Modules\Importer\Services\ImporterExecuteService;
use App\Modules\Importer\Services\ImporterService;
use App\Modules\Project\Models\Project;
use App\Modules\Project\Models\Record;
use App\Modules\Project\Models\RecordData;
use Carbon\Carbon;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ImporterExecuteTest extends TestCase
{
    private Appliance $appliance;
    private Importer $importer;
    private ImporterEvent $importerEvent;
    private Record $record;
    private RecordData $recordData;

    private ImporterService $importerService;
    private ImporterEventService $importerEventService;
    private ImporterExecuteService $importerExecuteService;

    /**
     * @throws BindingResolutionException
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->importerService = $this->app->make(ImporterService::class);
        $this->importerEventService = $this->app->make(ImporterEventService::class);
        $this->importerExecuteService = $this->app->make(ImporterExecuteService::class);

        $this->appliance = Appliance::create(['id' => Appliance::APPLIANCE_TEST_ID, 'name' => 'test']);
        $this->recordData = RecordData::create([
            'id' => RecordData::TEST_RECORD_DATA_ID,
            'name' => 'Test Journal',
            'description' => 'description',
            'project_id' => Project::TEST_PROJECT_ID
        ]);

        $this->record = Record::create([
            'id' => Record::TEST_RECORD_ID,
            'name' => 'Test',
            'description' => 'test test',
            'record_data_id' => RecordData::TEST_RECORD_DATA_ID,
            'user_id' => User::TEST_USER_ID
        ]);

        $this->importer = $this->importerService->create(new ImporterDto('testImporter', $this->appliance));
        $this->importerEvent = $this->createBasicImporterEvent(ImporterEventEvent::EVENT_IMPORT, ImporterInterpreterPython::class, 'test', 'importer_python_ctd.zip');
    }

    private function createBasicImporterEvent(int $event, string $interpreter, string $name = null, string $filename = 'importer_php.zip')
    {
        $storage = Storage::disk('examples');
        $uploadedFile = UploadedFile::fake()->createWithContent($filename, file_get_contents($storage->path($filename)));

        $eventDto = new ImporterEventDto(
            $this->importer,
            new ImporterEventEvent($event),
            new ImporterEventInterpreter($interpreter)
        );

        $eventDto->setName($name);

        return $this->importerEventService->create(
            $eventDto,
            $uploadedFile
        );
    }

    public function testBasic()
    {
        $storage = Storage::disk('examples');

        $uploadedDataFile = UploadedFile::fake()->createWithContent('data', file_get_contents(
            $storage->path('ctd' . DIRECTORY_SEPARATOR . '1' . DIRECTORY_SEPARATOR . '065668_20190801_1916.xlsx')
        ));

        $dataFile = new ImporterEventFile($uploadedDataFile, 'data');

        $uploadedCoordinatesFile = UploadedFile::fake()->createWithContent('data', file_get_contents(
            $storage->path('ctd' . DIRECTORY_SEPARATOR . '1' . DIRECTORY_SEPARATOR . 'Coordinates_065668_20190801_1916.xlsx')
        ));

        $uploadedDataFile->mimeType('application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $uploadedCoordinatesFile->mimeType('application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');

        $coordFile = new ImporterEventFile($uploadedCoordinatesFile, 'coordinates');

        $params = [
            'date_time' => Carbon::now()->format('Y-m-d H:i:s')
        ];

        $result = $this->importerService->import($this->importer, $this->record, $params, [$dataFile, $coordFile]);

        $this->assertTrue($result);
        $this->assertTrue($this->record->records_info->count() > 0);
    }
}
