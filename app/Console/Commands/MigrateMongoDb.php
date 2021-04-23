<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

/**
 * Class MigrateMongoDb
 * @package App\Console\Commands
 */
class MigrateMongoDb extends Command
{
    protected $signature = 'migrate:mongodb {--type=migrate}';

    protected $description = 'Apply migrations to mongodb';

    /**
     *
     */
    public function handle()
    {
        $type = $this->option('type');

        switch ($type) {
                case 'reset': \Artisan::call('migrate:reset', [
                '--path' => 'database/migrations/mongodb'
            ]);
                break;
            default: \Artisan::call('migrate', [
                '--path' => 'database/migrations/mongodb'
            ]);
        }
    }
}
