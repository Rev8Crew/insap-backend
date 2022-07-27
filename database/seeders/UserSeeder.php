<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\UserInfo;
use App\Modules\User\Services\UserService;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(UserService $userService)
    {
        $root = $userService->create([
            'id' => User::ROOT_USER_ID,
            'name' => 'root',
            'email' => 'admin@admin.com',
            'password' => 'katawa'
        ]);

        $root->assignRole('super-admin');

        $test = $userService->create([
            'id' => User::TEST_USER_ID,
            'name' => 'test',
            'email' => 'test@test.com',
            'password' => 'test'
        ]);
    }
}
