<?php

use App\Modules\Processing\Controllers\ProcessController;
use Illuminate\Support\Facades\Route;

/**
 *  Auth Routes
 */
Route::prefix('processes')->middleware('auth:sanctum')->group(function () {

    Route::post('create', [ ProcessController::class, 'createProcess' ])->name('process.create');
    Route::post('update', [ ProcessController::class, 'update' ])->name('process.update');
    Route::post('update-app', [ ProcessController::class, 'updateApp' ])->name('process.update-app');

    Route::post('get-all-by-user-default-project', [ ProcessController::class, 'getAllByUserDefaultProject' ])->name('process.get-all-by-user-default-project');
    Route::post('get-types-list', [ ProcessController::class, 'getTypesList' ])->name('process.get-types-list');
    Route::post('get-interpreters-list', [ ProcessController::class, 'getInterpretersList' ])->name('process.get-interpreters-list');
    Route::post('get-fields-by-process', [ ProcessController::class, 'getFieldsByProcess'])->name('process.get-fields-by-process');
});
