<?php
declare(strict_types=1);

namespace Tests\Fixture;

use App\Models\File;
use App\Modules\Project\Models\Project;
use App\Modules\Project\Services\ProjectService;
use Illuminate\Foundation\Testing\WithFaker;

class ProjectFixture
{
    use WithFaker;

    private ProjectService $projectService;

    public function __construct(ProjectService $projectService)
    {
        $this->projectService = $projectService;
    }

    public function create(string $name): Project
    {
        $array = [
            'name' => $name,
            'description' => 'Fixture',
            'image_id' => File::first()->id
        ];

        return $this->projectService->create($array);
    }
}
