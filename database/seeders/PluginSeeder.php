<?php

namespace Database\Seeders;

use App\Enums\ActiveStatus;
use App\Modules\Plugins\Models\Plugin;
use App\Modules\Plugins\Services\PluginService;
use Illuminate\Database\Seeder;
use Plugins\adcp\Services\ProcessingService;

class PluginSeeder extends Seeder
{
    public function run(PluginService $pluginService): void
    {
        $plugin = $pluginService->create(
            'ADCP',
            Plugin::TEST_PLUGIN_SLUG,
            ProcessingService::class,
            ActiveStatus::create(ActiveStatus::ACTIVE));


    }
}
