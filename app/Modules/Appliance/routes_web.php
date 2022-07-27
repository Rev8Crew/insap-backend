<?php

use App\Modules\Appliance\Controllers\ApplianceController;
use Illuminate\Support\Facades\Route;

/**
 *  Auth Routes
 */
Route::prefix('appliances')->middleware('auth:sanctum')->group(function () {
    Route::post('/', [ ApplianceController::class, 'index']);

    Route::post('/by-project-and-user', [ ApplianceController::class, 'getAppliancesByProject']);
});
