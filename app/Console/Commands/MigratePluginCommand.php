<?php

namespace App\Console\Commands;

use App\Modules\Plugins\Models\Plugin;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class MigratePluginCommand extends Command
{
    protected $signature = 'migrate:plugin {slug}';

    protected $description = 'Exec migrations for plugin';

    public function handle()
    {
        $slug = $this->argument('slug');

        $path = 'plugins' . DIRECTORY_SEPARATOR . $slug . DIRECTORY_SEPARATOR . 'migrations';
        $migrationDir = base_path('plugins') . DIRECTORY_SEPARATOR . $slug . DIRECTORY_SEPARATOR . 'migrations';

        if ( \File::isDirectory($migrationDir) === false) {
            throw new \RuntimeException('Invalid migration folder name: ' . $migrationDir);
        }

        ob_start();
        Artisan::call('migrate', [ '--path' => $path]);
        $this->warn(ob_get_clean());
    }
}
