<?php

namespace Tests\Unit;

use App\helpers\IsActiveHelper;
use App\Models\User;
use App\Models\UserInfo;
use App\Modules\Project\Models\Project;
use App\Modules\Project\Services\ProjectService;
use App\Modules\User\Services\UserService;
use Illuminate\Support\Facades\Hash;
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
        $this->assertEquals(IsActiveHelper::ACTIVE_ACTIVE, $this->project->is_active);
    }

    public function testBasicDelete() {
        $projectId = $this->project->id;
        // Delete
        $this->projectService->delete($this->project);

        $this->assertNull(Project::find($projectId));
    }

    public function testBasicActivate() {
        $this->projectService->activate($this->project);

        $this->assertTrue(IsActiveHelper::ACTIVE_ACTIVE, $this->project->is_active);
    }

    public function testBasicDeactivate() {
        $this->projectService->deactivate($this->project);

        $this->assertTrue(IsActiveHelper::ACTIVE_INACTIVE, $this->project->is_active);
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
}
