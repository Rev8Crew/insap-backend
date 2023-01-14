<?php

namespace App\Modules\Plugins\Services;

use App\Enums\ActiveStatus;
use App\Modules\Plugins\Models\Plugin;
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

    public function getPluginService(?Plugin $plugin = null) : PluginServiceInterface
    {
        $class = $plugin ? $plugin->service_class : DefaultPluginService::class;
        $service = app($class);

        if ( ($service instanceof PluginServiceInterface) === false) {
            throw new \RuntimeException('Plugin class ' . $class . ' must be instance of PluginServiceInterface');
        }

        return $service;
    }
}
