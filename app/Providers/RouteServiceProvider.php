<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to the "home" route for your application.
     *
     * This is used by Laravel authentication to redirect users after login.
     *
     * @var string
     */
    public const HOME = '/';
    /**
     * The controller namespace for the application.
     *
     * When present, controller route declarations will automatically be prefixed with this namespace.
     *
     * @var string|null
     */
    // protected $namespace = 'App\\Http\\Controllers';

    /**
     * The Version of application api.
     *
     * @var string
     */
    public const WEB_PREFIX = 'web';

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot()
    {
        $this->configureRateLimiting();

        $this->routes( function () {
            $this->addModulesRoutes();

            Route::middleware('web')
                ->namespace($this->namespace)
                ->group(base_path('routes/common.php'));
        });
    }

    /**
     * Define the "modules" routes for the application.
     *
     * These routes are typically stateless.
     *
     * @return void
     */
    protected function addModulesRoutes()
    {
        $modules_folder = app_path('Modules');
        $modules = $this->getModulesList($modules_folder);

        foreach ($modules as $module) {
            $routesPathWeb   = $modules_folder . DIRECTORY_SEPARATOR . $module . DIRECTORY_SEPARATOR . 'routes_web.php';

            if (file_exists($routesPathWeb)) {
                Route::prefix(self::WEB_PREFIX)
                    ->middleware('web')
                    ->namespace($this->namespace/*"\\App\\Modules\\$module\Controllers"*/)
                    ->group($routesPathWeb);
            }
        }
    }

    /**
     * @param string $modules_folder
     * @return array
     */
    private function getModulesList(string $modules_folder): array
    {
        return
            array_values(
                array_filter(
                    scandir($modules_folder),
                    function ($item) use($modules_folder) {
                        return is_dir($modules_folder . DIRECTORY_SEPARATOR . $item) && !in_array($item, [".",".."]);
                    }
                )
            );
    }

    /**
     * Configure the rate limiters for the application.
     *
     * @return void
     */
    protected function configureRateLimiting()
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by(optional($request->user())->id ?: $request->ip());
        });
    }
}
