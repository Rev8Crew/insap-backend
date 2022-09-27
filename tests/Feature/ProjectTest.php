<?php

namespace Tests\Feature;

use App\Enums\ResponseStatus;
use App\Models\User;
use App\Modules\Project\Models\Project;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class ProjectTest extends TestCase
{

    public function testCreate()
    {
        $user = User::find(User::TEST_USER_ID);
        $file = UploadedFile::fake()->image('test_image.png', 300, 300);

        $testArray = [
            'name' => 'TestProject Feature',
            'description' => 'null',
            'image' => $file
        ];

        $response = $this->actingAs($user)->postJson('web/projects/create', $testArray);

        $response->assertStatus(200);
        $response->assertJson(fn(AssertableJson $json) =>
        $json->where('status', ResponseStatus::SUCCESS)
            ->etc()
        );

        $this->assertDatabaseHas('files', [ 'name' => $file->getClientOriginalName()]);
        Storage::disk('fileStore')->assertExists($file->hashName())->delete($file->hashName());
    }

    public function testDelete()
    {
        $user = User::find(User::TEST_USER_ID);
        $project = Project::find(Project::TEST_PROJECT_ID);
        $image = $project->imageFile->path;

        $response = $this->actingAs($user)->postJson('web/projects/delete/' . $project->id);

        $response->assertStatus(200);
        $response->assertJson(fn(AssertableJson $json) =>
        $json->where('status', ResponseStatus::SUCCESS)
            ->etc()
        );

        $this->assertNull(Project::whereId(Project::TEST_PROJECT_ID)->first());
        Storage::disk('fileStore')->assertMissing($image);
    }
}
