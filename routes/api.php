<?php

use App\Http\Controllers\Api\PreparistController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::prefix('preparist')->group(function () {
        Route::get('/dashboard', [PreparistController::class, 'dashboard']);
        Route::get('/orders', [PreparistController::class, 'index']);
        Route::post('/orders/{order}/start', [PreparistController::class, 'startPreparation']);
        Route::post('/orders/{order}/finish', [PreparistController::class, 'finishPreparation']);
    });
});
