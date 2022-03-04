<?php

use Illuminate\Console\Command;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of your Closure based console
| commands. Each Closure is bound to a command instance allowing a
| simple approach to interacting with each command's IO methods.
|
*/

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('app:cache:clear', function () {
    /** @var Command $this */
    $this->call(\Illuminate\Foundation\Console\EventClearCommand::class);
    $this->call(\Illuminate\Foundation\Console\ViewClearCommand::class);
    $this->call(\Illuminate\Cache\Console\ClearCommand::class);
    $this->call(\Illuminate\Foundation\Console\RouteClearCommand::class);
    $this->call(\Illuminate\Foundation\Console\ConfigClearCommand::class);
    $this->call(\Illuminate\Foundation\Console\ClearCompiledCommand::class);
})->describe('Clear all available caches for application.');

Artisan::command('app:cache:rebuild', function () {
    /** @var Command $this */
    $this->call('app:cache:clear');

    $this->call(\Illuminate\Foundation\Console\ViewCacheCommand::class);
    $this->call(\Illuminate\Foundation\Console\ConfigCacheCommand::class);
    $this->call(\Illuminate\Foundation\Console\EventCacheCommand::class);
    $this->call(\Illuminate\Foundation\Console\RouteCacheCommand::class);
})->describe('Rebuild all available caches for application.');
