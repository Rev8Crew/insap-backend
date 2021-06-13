<?php

namespace Tests\Unit;

use App\Models\User;
use App\Modules\Appliance\Models\Appliance;
use App\Modules\Importer\Models\Importer\Importer;
use App\Modules\Importer\Models\ImporterInterpreter\ImporterInterpreterPhp;
use App\Modules\Importer\Service\ImporterService;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class ImporterTest extends TestCase
{
    private ?Importer $importer = null;
    private ?ImporterService $importerService = null;
    private ?User $user = null;

    public function setUp(): void
    {
        parent::setUp(); // TODO: Change the autogenerated stub

        $this->importerService = $this->app->make(ImporterService::class);
        $this->user = User::find(User::TEST_USER_ID);
    }

    private function createBasicImporter($array)
    {
        $storage = \Storage::disk('examples');

        $uploadedFile = UploadedFile::fake()->createWithContent('importer_php.zip', file_get_contents($storage->path('importer_php.zip')));
        $appliance = Appliance::create(['id' => Appliance::APPLIANCE_TEST_ID, 'name' => 'test']);

        return $this->importerService->create(
            $array['name'],
            $array['description'],
            $array['interpreter_class'],
            $appliance,
            $this->user,
            $uploadedFile
        );
    }

    public function testCreate()
    {
        $array = [
            'id' => Importer::TEST_IMPORTER_ID,
            'name' => 'testImporter',
            'description' => 'testImporterDescription',
            'appliance_id' => Appliance::APPLIANCE_TEST_ID,
            'user_id' => $this->user->id,
            'interpreter_class' => ImporterInterpreterPhp::class
        ];

        $this->importer = $this->createBasicImporter($array);

        $this->assertTrue($this->importer instanceof Importer);

        $this->assertEquals($array['id'], $this->importer->id);
        $this->assertEquals($array['name'], $this->importer->name);
        $this->assertEquals($array['description'], $this->importer->description);
        $this->assertEquals($array['interpreter_class'], $this->importer->interpreter_class);
        $this->assertEquals($array['user_id'], $this->importer->user->id);
        $this->assertEquals($array['appliance_id'], $this->importer->appliance->id);

        \Storage::disk('import')->assertExists($this->importer->id)->deleteDirectory($this->importer->id);
    }

    public function testUpdateApp()
    {
        $array = [
            'id' => Importer::TEST_IMPORTER_ID,
            'name' => 'testImporter',
            'description' => 'testImporterDescription',
            'appliance_id' => Appliance::APPLIANCE_TEST_ID,
            'user_id' => $this->user->id,
            'interpreter_class' => ImporterInterpreterPhp::class
        ];

        $this->importer = $this->createBasicImporter($array);
        $uploadedFile = UploadedFile::fake()->createWithContent('importer_php.zip', file_get_contents(\Storage::disk('examples')->path('importer_php.zip')));

        $this->importerService->updateApp($this->importer, $uploadedFile);
        \Storage::disk('import')->assertExists($this->importer->id)->deleteDirectory($this->importer->id);
    }

    public function testDelete()
    {
        $array = [
            'id' => Importer::TEST_IMPORTER_ID,
            'name' => 'testImporter',
            'description' => 'testImporterDescription',
            'appliance_id' => Appliance::APPLIANCE_TEST_ID,
            'user_id' => $this->user->id,
            'interpreter_class' => ImporterInterpreterPhp::class
        ];

        $this->importer = $this->createBasicImporter($array);

        $this->importerService->delete($this->importer);
        $this->assertNull(Importer::find(Importer::TEST_IMPORTER_ID));
        \Storage::disk('import')->assertMissing($this->importer->id);
    }
}
