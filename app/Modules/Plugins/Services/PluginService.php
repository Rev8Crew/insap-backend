<?php

namespace App\Modules\Plugins\Services;

use App\Enums\ActiveStatus;
use App\Modules\Plugins\Models\Plugin;
use App\Modules\Plugins\Models\PluginServiceInterface;
use Illuminate\Database\Eloquent\Collection;

class PluginService
{
    public function create(
        string       $name,
        string       $slug,
        string       $serviceClass,
        ActiveStatus $activeStatus,
        array        $settings = []
    ) : Plugin {
        return Plugin::create([
            'name' => $name,
            'slug' => $slug,
            'service_class' => $serviceClass,
            'is_active' => $activeStatus->getValue(),
            'settings' => $settings
        ]);
    }

    public function getAll(): Collection
    {
        return Plugin::where('is_active', ActiveStatus::ACTIVE)->get();
    }

    public function getPluginService(Plugin $plugin) : PluginServiceInterface
    {
        $service = app($plugin->service_class);

        if ( ($service instanceof PluginServiceInterface) === false) {
            throw new \RuntimeException('Plugin class ' . $plugin->service_class . ' must be instance of PreProcessingInterface');
        }

        return $service;
    }
}
