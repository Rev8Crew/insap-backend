<?php

namespace Tests\Unit;

use App\helpers\IsActiveHelper;
use App\Models\User;
use App\Models\UserInfo;
use App\Modules\Project\Models\Project;
use App\Modules\Project\Models\RecordData;
use App\Modules\Project\Services\ProjectService;
use App\Modules\Project\Services\RecordDataService;
use App\Modules\User\Services\UserService;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class RecordDataTest extends TestCase
{
    private ?RecordData $recordData = null;
    private ?RecordDataService $recordDataService = null;

    public function setUp(): void
    {
        parent::setUp();

        $this->recordDataService = $this->app->make(RecordDataService::class);
        $this->recordData = RecordData::find(RecordData::TEST_RECORD_DATA_ID);
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
        $this->assertEquals(IsActiveHelper::ACTIVE_ACTIVE, $this->recordData->is_active);
    }

    public function testBasicDelete() {
        $id = $this->recordData->id;
        // Delete
        $this->recordDataService->delete($this->recordData);

        $this->assertNull(RecordData::find($id));
    }

    public function testBasicActivate() {
        $this->recordDataService->activate($this->recordData);

        $this->assertEquals(IsActiveHelper::ACTIVE_ACTIVE, $this->recordData->is_active);
    }

    public function testBasicDeactivate() {
        $this->recordDataService->deactivate($this->recordData);

        $this->assertEquals(IsActiveHelper::ACTIVE_INACTIVE, $this->recordData->is_active);
    }
}
