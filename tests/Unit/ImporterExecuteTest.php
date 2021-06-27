<?php

namespace Tests\Unit;

use App\Modules\Appliance\Models\Appliance;
use App\Modules\Importer\Models\Importer\Importer;
use App\Modules\Importer\Models\Importer\ImporterDto;
use App\Modules\Importer\Models\ImporterEvents\ImporterEvent;
use App\Modules\Importer\Models\ImporterEvents\ImporterEventDto;
use App\Modules\Importer\Models\ImporterEvents\ImporterEventEvent;
use App\Modules\Importer\Models\ImporterEvents\ImporterEventInterpreter;
use App\Modules\Importer\Models\ImporterInterpreter\ImporterInterpreterPython;
use App\Modules\Importer\Services\ImporterEventService;
use App\Modules\Importer\Services\ImporterExecuteService;
use App\Modules\Importer\Services\ImporterService;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ImporterExecuteTest extends TestCase
{
    private Appliance $appliance;
    private Importer $importer;
    private ImporterEvent $importerEvent;

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

        $this->importer = $this->importerService->create(new ImporterDto('testImporter', $this->appliance));
        $this->importerEvent = $this->createBasicImporterEvent(ImporterEventEvent::EVENT_IMPORT, ImporterInterpreterPython::class, 'test', 'importer_python_adcp.zip');
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

    }
}
