<?php

use App\Modules\Processing\Controllers\ProcessController;
use Illuminate\Support\Facades\Route;

/**
 *  Auth Routes
 */
Route::prefix('processes')->middleware('auth:sanctum')->group(function () {
    Route::post('create', [ ProcessController::class, 'createProcess' ])->name('process.create');

    Route::post('get-all-by-user-default-project', [ ProcessController::class, 'getAllByUserDefaultProject' ])->name('process.get-all-by-user-default-project');

    Route::post('get-interpreters-list', [ ProcessController::class, 'getInterpretersList' ])->name('process.get-interpreters-list');
});
