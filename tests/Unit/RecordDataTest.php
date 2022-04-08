<?php

namespace Tests\Unit;

use App\Enums\ActiveStatus;
use App\Models\User;
use App\Modules\Project\Models\Project;
use App\Modules\Project\Models\RecordData;
use App\Modules\Project\Services\RecordDataService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class RecordDataTest extends TestCase
{
    private ?RecordData $recordData = null;
    private ?RecordDataService $recordDataService = null;
    private ?User $_user = null;

    public function setUp(): void
    {
        parent::setUp();

        $this->recordDataService = $this->app->make(RecordDataService::class);
        $this->recordData = RecordData::find(RecordData::TEST_RECORD_DATA_ID);
        $this->_user = User::find(User::TEST_USER_ID);
    }

    public function testBasicCreate()
    {
        $array = [
            'name' => 'TestTest',
            'description' => 'test',
            'project_id' => Project::TEST_PROJECT_ID
        ];

        $this->recordData = $this->recordDataService->create($array);

        $this->assertTrue( $this->recordData instanceof RecordData);
        $this->assertNotEmpty($this->recordData);

        $this->assertEquals($array['name'], $this->recordData->name);
        $this->assertEquals($array['description'], $this->recordData->description);
        $this->assertEquals(ActiveStatus::ACTIVE, $this->recordData->is_active);
    }

    public function testBasicDelete() {
        $id = $this->recordData->id;
        // Delete
        $this->recordDataService->delete($this->recordData);

        $this->assertNull(RecordData::find($id));
    }

    public function testChangeImage() {
        $uploadedFile = UploadedFile::fake()->image('test_image.png', 100, 100);

        $this->recordDataService->changeImage($this->recordData, $uploadedFile, $this->_user);

        Storage::disk('fileStore')->assertExists($uploadedFile->hashName())->delete($uploadedFile->hashName());
        $this->assertNotNull($this->recordData->imageFile);
    }
}
