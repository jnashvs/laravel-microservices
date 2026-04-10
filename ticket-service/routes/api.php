<?php

use Illuminate\Support\Facades\Route;
use Infrastructure\Http\Controllers\TicketController;

/*
|--------------------------------------------------------------------------
| Health
|--------------------------------------------------------------------------
*/
Route::get('/health', fn () => response()->json(['status' => 'ok']));

/*
|--------------------------------------------------------------------------
| Tickets
|--------------------------------------------------------------------------
*/
Route::prefix('tickets')->group(function () {
    Route::get('/{id}', [TicketController::class, 'show']);
    Route::post('/', [TicketController::class, 'store']);
    Route::get('/', [TicketController::class, 'index']);
});
