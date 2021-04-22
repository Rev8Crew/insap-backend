<?php

use App\helpers\RouteHelper;
use App\Modules\User\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/**
 *  User Routes
 */
Route::prefix('user')->middleware('auth:sanctum')->group(function () {
    Route::post(RouteHelper::ROUTE_CREATE, [UserController::class, 'create']);
    Route::post(RouteHelper::ROUTE_UPDATE, [UserController::class, 'update']);
    Route::post(RouteHelper::ROUTE_DELETE, [UserController::class, 'delete']);

    Route::post(RouteHelper::ROUTE_INDEX, [UserController::class, 'get']);
    Route::post(RouteHelper::ROUTE_VIEW, [UserController::class, 'view']);
});
