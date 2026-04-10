<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TicketProxyController;

/*
|--------------------------------------------------------------------------
| Public
|--------------------------------------------------------------------------
*/

Route::post('/login', [AuthController::class, 'login']);
Route::post('/refresh', [AuthController::class, 'refresh']);

/*
|--------------------------------------------------------------------------
| Protected
|--------------------------------------------------------------------------
*/

Route::middleware('auth.service')->group(function () {

    // Auth
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);

    // Tickets
    Route::prefix('tickets')->group(function () {
        Route::get('/{id}', [TicketProxyController::class, 'show']);
        Route::get('/', [TicketProxyController::class, 'index']);
        Route::post('/', [TicketProxyController::class, 'store']);
    });

});
