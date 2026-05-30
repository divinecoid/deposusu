<?php

use App\Http\Controllers\Api\PreparistController;
use App\Http\Controllers\Api\DriverController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/login', [DriverController::class, 'login']);
Route::post('/driver/request-otp', [DriverController::class, 'requestOtp']);
Route::post('/driver/verify-otp', [DriverController::class, 'verifyOtp']);
Route::get('/orders/track/{order_number}', [DriverController::class, 'trackOrder']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::prefix('preparist')->group(function () {
        Route::get('/dashboard', [PreparistController::class, 'dashboard']);
        Route::get('/orders', [PreparistController::class, 'index']);
        Route::post('/orders/{order}/start', [PreparistController::class, 'startPreparation']);
        Route::patch('/orders/{order}/sync', [PreparistController::class, 'syncOrder']);
        Route::post('/orders/{order}/finish', [PreparistController::class, 'finishPreparation']);
    });

    Route::prefix('driver')->group(function () {
        Route::get('/dashboard', [DriverController::class, 'dashboard']);
        Route::post('/check-in', [DriverController::class, 'checkIn']);
        Route::post('/check-out', [DriverController::class, 'checkOut']);
        Route::get('/orders', [DriverController::class, 'orders']);
        Route::get('/orders/{order}', [DriverController::class, 'showOrder']);
        Route::post('/orders/{order}/pickup', [DriverController::class, 'pickupOrder']);
        Route::post('/orders/{order}/finish', [DriverController::class, 'finishOrder']);
    });
});
