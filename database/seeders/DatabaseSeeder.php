<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // Если запускаем сидеры для теста
        // то используем другую бд для монги
        if (DB::getDefaultConnection() === 'mysql_test') {
            config()->set('database.mongodb_test_connection', 'mongodb_test');
        }

        $this->call([
            RolePermissionSeeder::class,
            UserSeeder::class,
            FileSeeder::class,
            ProjectSeeder::class,
            RecordDataSeeder::class,
            RecordSeeder::class,
            PluginSeeder::class,

        ]);

        // Только для дева\прода сидеры
        if (DB::getDefaultConnection() !== 'mysql_test') {
            $this->call(StageSeeder::class);
        }

    }
}
