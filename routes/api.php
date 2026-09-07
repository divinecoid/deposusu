<?php

use App\Http\Controllers\Api\PreparistController;
use App\Http\Controllers\Api\DriverController;
use App\Http\Controllers\Api\CashierApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;

Route::get('/migrate-db', function () {
    try {
        Artisan::call('migrate', ['--force' => true]);
        return response()->json([
            'success' => true,
            'message' => 'Database migrated successfully!',
            'output' => Artisan::output()
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Migration failed: ' . $e->getMessage()
        ], 500);
    }
});
Route::post('/login', [DriverController::class, 'login']);
Route::post('/driver/request-otp', [DriverController::class, 'requestOtp']);
Route::post('/driver/verify-otp', [DriverController::class, 'verifyOtp']);
Route::get('/orders/track/{order_number}', [DriverController::class, 'trackOrder']);
// Customer mobile app (Sanctum token auth)
Route::prefix('customer')->name('api.customer.')->group(function () {
    Route::post('/register', [\App\Http\Controllers\Api\Customer\AuthController::class, 'register']);
    Route::post('/login', [\App\Http\Controllers\Api\Customer\AuthController::class, 'login']);

    Route::get('/products', [\App\Http\Controllers\Api\Customer\ProductController::class, 'index']);
    Route::get('/products/{product}', [\App\Http\Controllers\Api\Customer\ProductController::class, 'show']);
    Route::get('/categories', [\App\Http\Controllers\Api\Customer\ProductController::class, 'categories']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [\App\Http\Controllers\Api\Customer\AuthController::class, 'logout']);
        Route::get('/me', [\App\Http\Controllers\Api\Customer\AuthController::class, 'me']);

        Route::get('/cart', [\App\Http\Controllers\Api\Customer\CartController::class, 'index']);
        Route::post('/cart/add', [\App\Http\Controllers\Api\Customer\CartController::class, 'add']);
        Route::patch('/cart/{item}', [\App\Http\Controllers\Api\Customer\CartController::class, 'update']);
        Route::delete('/cart/{item}', [\App\Http\Controllers\Api\Customer\CartController::class, 'remove']);

        Route::get('/payment-options', [\App\Http\Controllers\Api\Customer\CheckoutController::class, 'paymentOptions']);
        Route::post('/checkout', [\App\Http\Controllers\Api\Customer\CheckoutController::class, 'checkout']);

        Route::get('/orders', [\App\Http\Controllers\Api\Customer\OrderController::class, 'index']);
        Route::get('/orders/{order}', [\App\Http\Controllers\Api\Customer\OrderController::class, 'show']);
        Route::post('/orders/{order}/pay', [\App\Http\Controllers\Api\Customer\OrderController::class, 'pay']);
        Route::get('/payments/{payment}/status', [\App\Http\Controllers\Api\Customer\OrderController::class, 'paymentStatus'])->name('payments.status');

        Route::get('/notifications', [\App\Http\Controllers\Api\Customer\NotificationController::class, 'index']);
        Route::post('/notifications/{id}/read', [\App\Http\Controllers\Api\Customer\NotificationController::class, 'markRead']);
        Route::post('/fcm-token', [\App\Http\Controllers\Api\Customer\NotificationController::class, 'registerFcmToken']);
    });
});
Route::post('/cashier/login', [CashierApiController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::prefix('profile')->group(function () {
        Route::post('/update-basic', [\App\Http\Controllers\Api\ProfileController::class, 'updateBasicProfile']);
        Route::post('/request-otp', [\App\Http\Controllers\Api\ProfileController::class, 'requestOtp']);
        Route::post('/verify-otp', [\App\Http\Controllers\Api\ProfileController::class, 'verifyOtpAndUpdate']);
    });

    Route::prefix('preparist')->group(function () {
        Route::get('/dashboard', [PreparistController::class, 'dashboard']);
        Route::get('/orders', [PreparistController::class, 'index']);
        Route::post('/orders/{order}/start', [PreparistController::class, 'startPreparation']);
        Route::patch('/orders/{order}/sync', [PreparistController::class, 'syncOrder']);
        Route::post('/orders/{order}/cancel', [PreparistController::class, 'cancelPreparation']);

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

    Route::prefix('cashier')->group(function () {
        Route::get('/products', [CashierApiController::class, 'products']);
        Route::post('/checkout', [CashierApiController::class, 'checkout']);
        Route::get('/orders', [CashierApiController::class, 'orders']);
    });
});
