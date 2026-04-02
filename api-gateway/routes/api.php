<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TicketProxyController;

Route::get('/health', function () {
    return response()->json(['status' => 'ok', 'service' => 'api-gateway']);
});

// Proxy para ticket-service
Route::post('/tickets', [TicketProxyController::class, 'store']);
Route::get('/tickets', [TicketProxyController::class, 'index']);
Route::get('/tickets/{id}', [TicketProxyController::class, 'show']);
