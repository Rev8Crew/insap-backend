<?php

use App\Models\User;
use App\Modules\Auth\Controllers\AuthController;
use App\Modules\Project\Controllers\ProjectController;
use App\Modules\Project\Controllers\RecordController;
use App\Modules\Project\Controllers\RecordDataController;
use Illuminate\Support\Facades\Route;

/**
 *  Project Routes
 */
Route::prefix('projects')->middleware('auth:sanctum')->group(function () {
    Route::post('/', [ProjectController::class, 'get']);

    Route::post('/by-user', [ProjectController::class, 'getProjectsByUser']);

    Route::prefix('records-data')->group(function () {
        Route::post('create', [ RecordDataController::class, 'createRecordData' ])->name('records-data.create');

        Route::post('get-by-id', [RecordDataController::class, 'getRecordDataById'])->name('records-data.get-by-id');

            Route::prefix('records')->group(function () {
            Route::post('create', [RecordController::class, 'create']);
            Route::post('update', [RecordController::class, 'update']);

            Route::post('import', [RecordController::class, 'import']);
            Route::post('delete-records-in-record', [RecordController::class, 'deleteRecordsInRecord']);

            Route::post('get-by-id', [RecordController::class, 'getById']);
            Route::post('get-records-by-record-data', [RecordController::class, 'getRecordsByRecordData']);
        });
    });
});
