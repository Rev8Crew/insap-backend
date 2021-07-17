<?php

use App\Modules\Importer\Controllers\ImporterController;
use Illuminate\Support\Facades\Route;

/**
 *  Auth Routes
 */
Route::prefix('importer')->middleware('auth:sanctum')->group(function () {
    Route::post('/create', [ImporterController::class, 'create'])->name('importer.create');
    Route::post('/delete', [ImporterController::class, 'delete'])->name('importer.delete');
    Route::post('/import', [ImporterController::class, 'import'])->name('importer.import');
});
