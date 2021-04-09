<?php

namespace App\Service\RouterService;

use Illuminate\Support\Facades\Route;

class RouterServiceV1 extends AbstractRouterService
{
    /** @var string route prefix */
    const ROUTE_VERSION_PREFIX = 'v1';

    /**
     * @inheritDoc
     */
    public function registerRoutes()
    {
        $path = $this->getBasePath();
        $api = static::API_PREFIX . '/' . static::ROUTE_VERSION_PREFIX;
        $web = static::BACKEND_PREFIX . '/' . static::ROUTE_VERSION_PREFIX;

        Route::prefix($api)
            ->middleware('api')
            ->namespace($this->namespace)
            ->group(base_path($path . 'api.php'));

        Route::prefix($web)
            ->middleware('web')
            ->namespace($this->namespace)
            ->group(base_path($path . 'web.php'));
    }
}