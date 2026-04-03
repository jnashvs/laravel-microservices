<?php

use Illuminate\Support\Facades\Route;
use Infrastructure\Http\Controllers\NotificationController;

Route::get('/health', function () {
    return response()->json(['status' => 'ok', 'service' => 'notification-service']);
});

Route::get('/notifications', [NotificationController::class, 'index']);
