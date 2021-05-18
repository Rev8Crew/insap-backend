<?php

namespace Database\Seeders;

use App\Modules\Project\Models\Project;
use Illuminate\Database\Seeder;

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
            'description' => 'description'
        ]);

    }
}
