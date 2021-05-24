<?php

namespace Database\Seeders;

use App\Models\User;
use App\Modules\Project\Models\Record;
use App\Modules\Project\Models\RecordData;
use Illuminate\Database\Seeder;

class RecordSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $record = Record::create([
            'id' => Record::TEST_RECORD_ID,
           'name' => 'Test',
           'description' => 'test test',
           'record_data_id' => RecordData::TEST_RECORD_DATA_ID,
           'user_id' => User::TEST_USER_ID
        ]);
    }
}
