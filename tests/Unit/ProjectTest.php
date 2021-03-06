<?php

namespace Tests\Unit;

use App\Enums\ActiveStatus;
use App\Models\User;
use App\Modules\Project\Models\Project;
use App\Modules\Project\Services\ProjectService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProjectTest extends TestCase
{
    private ?Project $project = null;
    private ?ProjectService $projectService = null;
    private ?User $_user = null;

    public function setUp(): void
    {
        parent::setUp();

        $this->projectService = $this->app->make(ProjectService::class);
        $this->project = Project::find(Project::TEST_PROJECT_ID);
        // Take test user from seeder
        $this->_user = User::find(User::TEST_USER_ID);
    }

    public function testBasicCreate()
    {
        $array = [
            'name' => 'TestTest',
            'description' => 'test',
        ];

        $this->project = $this->projectService->create($array);

        $this->assertTrue( $this->project instanceof Project);
        $this->assertNotEmpty($this->project);

        $this->assertEquals($array['name'], $this->project->name);
        $this->assertEquals($array['description'], $this->project->description);
        $this->assertEquals(ActiveStatus::ACTIVE, $this->project->is_active);
    }

    public function testBasicDelete() {
        $projectId = $this->project->id;
        // Delete
        $this->projectService->delete($this->project);

        $this->assertNull(Project::find($projectId));
    }

    public function testBasicAddUser() {
        // Do attach
        $this->projectService->addUserToProject($this->project, $this->_user);

        $this->assertEquals( 1, $this->project->users()->count());
    }

    public function testBasicRemoveUser() {
        // Do attach
        $this->projectService->removeUserFromProject($this->project, $this->_user);

        $this->assertEquals( 0, $this->project->users()->count());
    }

    public function testChangeImage() {
        $uploadedFile = UploadedFile::fake()->image('test_image.png', 100, 100);
        $image = $this->project->imageFile->path;

        $this->projectService->changeImage($this->project, $uploadedFile, $this->_user);

        Storage::disk('fileStore')->assertMissing($image);
        Storage::disk('fileStore')->assertExists($uploadedFile->hashName())->delete($uploadedFile->hashName());

        $this->assertNotNull($this->project->imageFile);
    }
}
