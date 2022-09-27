<?php

namespace App\Modules\Plugins\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Common\Response;
use App\Modules\Plugins\Resources\PluginResource;
use App\Modules\Plugins\Services\PluginService;

class PluginController  extends Controller
{
    private PluginService $pluginService;

    public function __construct(PluginService $pluginService)
    {
        $this->pluginService = $pluginService;
    }

    public function getAll(): Response
    {
        $response = Response::make();
        $plugins = $this->pluginService->getAll();

        return $response->withData( PluginResource::collection($plugins));
    }
}
