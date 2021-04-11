<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\Request;

class AcceptLanguage
{
    /**
     * @var Application
     */
    private Application $app;

    public function __construct(Application $application)
    {
        $this->app = $application;
    }

    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $this->app->setLocale($this->parseHttpLocale($request));

        return $next($request);
    }

    /**
     * @param Request $request
     *
     * @return string
     */
    private function parseHttpLocale(Request $request): string
    {
        $list = explode(',', $request->server('HTTP_ACCEPT_LANGUAGE'));

        $locales = collect($list)->map(function ($locale) {
            $parts = explode(';', $locale);

            $mapping['locale'] = trim($parts[0]);

            if (isset($parts[1])) {
                $factorParts = explode('=', $parts[1]);

                $mapping['factor'] = $factorParts[1];
            } else {
                $mapping['factor'] = 1;
            }

            return $mapping;
        })->sortByDesc(function ($locale) {
            return $locale['factor'];
        });

        return $locales->first()['locale'];
    }
}
