<?php

use Illuminate\Support\Facades\Route;
use Modules\Landing\Http\Controllers\LandingController;

/*
 *--------------------------------------------------------------------------
 * API Routes
 *--------------------------------------------------------------------------
 *
 * Here is where you can register API routes for your application. These
 * routes are loaded by the RouteServiceProvider within a group which
 * is assigned the "api" middleware group. Enjoy building your API!
 *
*/

Route::prefix('v1')->group(function () {
    Route::controller(LandingController::class)->name('landing.')->prefix('landing')->group(function () {

        Route::get('/', 'index')->name('index');

        Route::get('ecommerce', 'ecommerce')->name('ecommerce');

        Route::get('search', 'search')->name('search');
    });
});
