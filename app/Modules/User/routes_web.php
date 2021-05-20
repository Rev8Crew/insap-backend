<?php

use App\helpers\RouteHelper;
use App\Modules\User\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/**
 *  User Routes
 */
Route::prefix('users')->middleware('auth:sanctum')->group(function () {
    Route::post('/', [UserController::class, 'get']);
    Route::post('create', [UserController::class, 'create']);
    Route::post('delete/{user}', [UserController::class, 'delete']);
    Route::post('view/{user}', [UserController::class, 'view']);

    Route::post('activate/{user}', [ UserController::class, 'activate']);
    Route::post('deactivate/{user}', [ UserController::class, 'deactivate']);
});
