<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\TokenController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Internal (only gateway)
|--------------------------------------------------------------------------
*/

Route::middleware('internal')->group(function () {

    /*
    |--------------------------------------------------------------------------
    | Public
    |--------------------------------------------------------------------------
    */
    Route::post('/login', [AuthController::class, 'login'])
        ->middleware('throttle:login')
        ->name('auth.login');

    Route::post('/refresh', [AuthController::class, 'refresh'])
        ->middleware('throttle:refresh')
        ->name('auth.refresh');

    /*
    |--------------------------------------------------------------------------
    | Token Introspection
    |--------------------------------------------------------------------------
    */
    Route::get('/token/validate', [AuthController::class, 'validateToken'])
        ->middleware('auth:api')
        ->name('auth.token.validate');

    /*
    |--------------------------------------------------------------------------
    | Protected
    |--------------------------------------------------------------------------
    */
    Route::middleware('auth:api')->group(function () {
        Route::get('/me', [AuthController::class, 'me'])->name('auth.me');
        Route::post('/logout', [AuthController::class, 'logout'])->name('auth.logout');
        Route::post('/logout-all', [AuthController::class, 'logoutAll'])->name('auth.logout-all');
    });

});
