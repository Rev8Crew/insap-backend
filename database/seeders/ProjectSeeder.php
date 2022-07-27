<?php

namespace Database\Seeders;

use App\Models\File;
use App\Models\User;
use App\Modules\Project\Models\Project;
use App\Modules\Project\Services\ProjectService;
use App\Services\File\FileService;
use Illuminate\Database\Seeder;
use Illuminate\Http\UploadedFile;

class ProjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $project = Project::create([
            'id' => Project::TEST_PROJECT_ID,
            'name' => 'Test Project',
            'description' => 'description',
            'image_id' => File::first()->id
        ]);
    }
}
