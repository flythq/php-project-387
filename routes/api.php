<?php

use App\Http\Controllers\Api\AvailabilityController;
use App\Http\Controllers\Api\BookingsController;
use Illuminate\Support\Facades\Route;

Route::prefix('/')->group(function (): void {
    Route::get('/availability', [AvailabilityController::class, 'index']);
    Route::post('/bookings', [BookingsController::class, 'store']);
    Route::get('/bookings/{id}', [BookingsController::class, 'show']);
});
