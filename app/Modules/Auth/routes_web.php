<?php

use App\Models\User;
use App\Modules\Auth\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

/**
 *  Auth Routes
 */
Route::prefix('auth')->group(function () {

    Route::post('login', [ AuthController::class, 'login' ])->name('login');
    Route::post('logout', [ AuthController::class, 'logout' ])->name('logout');

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/me', [ AuthController::class, 'me'])->name('auth.me');
    });
});
