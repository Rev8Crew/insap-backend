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

        Route::prefix(static::API_PREFIX)
            ->prefix(static::ROUTE_VERSION_PREFIX)
            ->middleware('api')
            ->namespace($this->namespace)
            ->group(base_path($path . 'api.php'));

        Route::prefix(static::BACKEND_PREFIX)
            ->prefix(static::ROUTE_VERSION_PREFIX)
            ->middleware('web')
            ->namespace($this->namespace)
            ->group(base_path($path . 'web.php'));
    }
}