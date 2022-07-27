<?php

use App\Models\User;
use App\Modules\Auth\Controllers\AuthController;
use App\Modules\Project\Controllers\ProjectController;
use Illuminate\Support\Facades\Route;

/**
 *  Project Routes
 */
Route::prefix('projects')->group(function () {
    Route::post('/', [ProjectController::class, 'get']);

    Route::post('/by-user', [ProjectController::class, 'getProjectsByUser']);

    Route::post('create', [ProjectController::class, 'create']);
    Route::post('delete/{project}', [ProjectController::class, 'delete']);
    Route::post('view/{project}', [ProjectController::class, 'view']);

    Route::post('delete/{project}', [ProjectController::class, 'delete']);
});
