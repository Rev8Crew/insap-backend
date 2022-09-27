<?php

use App\Modules\Plugins\Controllers\PluginController;
use App\Modules\Processing\Controllers\ProcessController;
use Illuminate\Support\Facades\Route;

/**
 *  Auth Routes
 */
Route::prefix('plugins')->middleware('auth:sanctum')->group(function () {

    Route::post('/', [ PluginController::class, 'getAll' ])->name('plugins.get-all');
});
