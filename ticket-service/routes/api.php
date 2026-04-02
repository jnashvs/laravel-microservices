<?php

use Illuminate\Support\Facades\Route;
use Infrastructure\Http\Controllers\TicketController;

Route::get('/health', function () {
    return response()->json(['status' => 'ok']);
});

Route::post('/tickets', [TicketController::class, 'store']);
Route::get('/tickets', [TicketController::class, 'index']);
Route::get('/tickets/{id}', [TicketController::class, 'show']);
