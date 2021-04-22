<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\UserInfo;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $root = User::create([
            'id' => 1,
            'name' => 'root',
            'email' => 'admin@admin.com',
            'password' => \Hash::make('rootadmin')
        ]);

        $root->assignRole('super-admin');

        $userInfo = UserInfo::create([
            'user_id' => $root->id
        ]);

        $test = User::create([
            'name' => 'test',
            'email' => 'test@test.com',
            'password' => \Hash::make('rootadmin')
        ]);
    }
}
