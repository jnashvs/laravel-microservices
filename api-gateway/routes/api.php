<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TicketProxyController;
use App\Http\Controllers\NotificationProxyController;
use App\Http\Controllers\HealthController;

// Public
Route::get('/health', HealthController::class);

// Protected
Route::middleware(['auth.apikey', 'rate.limit'])->group(function () {
    Route::post('/tickets', [TicketProxyController::class, 'store']);
    Route::get('/tickets', [TicketProxyController::class, 'index']);
    Route::get('/tickets/{id}', [TicketProxyController::class, 'show']);

    Route::get('/notifications', [NotificationProxyController::class, 'index']);
});
