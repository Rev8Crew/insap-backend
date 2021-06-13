<?php

use App\Http\Controllers\FrontendController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Common Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

/**
 *  Common Routes
 */

/** Log viewer */
Route::get('logs', '\Rap2hpoutre\LaravelLogViewer\LogViewerController@index');

// For public application
Route::any('/{any}', [\App\Http\Controllers\CommonController::class, 'app'])->where('any', '^(?!api).*$');
