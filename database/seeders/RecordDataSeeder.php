<?php

namespace Database\Seeders;

use App\Modules\Project\Models\Project;
use App\Modules\Project\Models\RecordData;
use Illuminate\Database\Seeder;

class RecordDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $recordData = RecordData::create([
            'id' => RecordData::TEST_RECORD_DATA_ID,
            'name' => 'Test Journal',
            'description' => 'description',
            'project_id' => Project::TEST_PROJECT_ID
        ]);


    }
}
