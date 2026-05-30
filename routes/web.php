<?php

use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;
use Livewire\Volt\Volt;

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\InvoiceController;
use App\Http\Controllers\Admin\StockOpnameController;
use App\Http\Controllers\Admin\WarehouseController;
use App\Http\Controllers\Admin\RackController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Customer\HomeController;
use App\Http\Controllers\Customer\CartController;
use App\Http\Controllers\Customer\WishlistController;
use App\Http\Controllers\Customer\TransactionsController;

// Customer Routes
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/products/search', [HomeController::class, 'search'])->name('products.search');
Route::get('/products/suggest', [HomeController::class, 'suggest'])->name('products.suggest');
Route::get('/products/{product}', [HomeController::class, 'show'])->name('products.show');

// Cart & Checkout Routes
Route::prefix('cart')->name('cart.')->group(function () {
    Route::get('/', [CartController::class, 'index'])->name('index');
    Route::post('/add', [CartController::class, 'add'])->name('add');
    Route::patch('/update/{cartItem}', [CartController::class, 'update'])->name('update');
    Route::delete('/remove/{cartItem}', [CartController::class, 'remove'])->name('remove');
    Route::patch('/update-routine/{cartItem}', [CartController::class, 'updateRoutine'])->name('update-routine');
    Route::delete('/clear', [CartController::class, 'clear'])->name('clear');
    Route::get('/data', [CartController::class, 'getCartData'])->name('data');
});

// Checkout Routes
Route::middleware(['auth'])->group(function () {
    Route::get('/checkout', [\App\Http\Controllers\Customer\CheckoutController::class, 'index'])->name('checkout.index');
    Route::post('/checkout/process', [CartController::class, 'checkout'])->name('checkout.process');
});

// Wishlist Routes
Route::prefix('wishlist')->name('wishlist.')->middleware(['auth'])->group(function () {
    Route::get('/', [WishlistController::class, 'index'])->name('index');
    Route::post('/toggle', [WishlistController::class, 'toggle'])->name('toggle');
});

// Customer Transactions
Route::prefix('transactions')->name('transactions.')->middleware(['auth'])->group(function () {
    Route::get('/', [TransactionsController::class, 'index'])->name('index');
    Route::get('/{order}', [TransactionsController::class, 'show'])->name('show');
    Route::post('/{order}/reorder', [TransactionsController::class, 'reorder'])->name('reorder');
    Route::get('/{order}/invoice', [TransactionsController::class, 'invoice'])->name('invoice');
    Route::post('/{order}/invoice/log-print', [TransactionsController::class, 'logPrint'])->name('invoice.logPrint');
});

// Account Route
Route::middleware(['auth'])->group(function () {
    Route::get('/account', [\App\Http\Controllers\Customer\AccountController::class, 'index'])->name('account.index');
});

// Admin Routes
Route::prefix('admin')->name('admin.')->middleware(['auth'])->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Master Data
    Route::prefix('master')->name('master.')->group(function () {
        Route::resource('categories', CategoryController::class)->except(['show']);
        Route::resource('products', ProductController::class)->except(['show']);
        Route::post('products/{product}/discount', [ProductController::class, 'storeDiscount'])->name('products.discount.store');
        Route::patch('discounts/{discount}/toggle', [ProductController::class, 'toggleDiscountStatus'])->name('products.discount.toggle');
        Route::put('discounts/{discount}', [ProductController::class, 'updateDiscount'])->name('products.discount.update');
        Route::delete('discounts/{discount}', [ProductController::class, 'destroyDiscount'])->name('products.discount.destroy');
        Route::resource('warehouses', WarehouseController::class)->except(['show', 'edit', 'create']);
        Route::resource('racks', RackController::class)->only(['store', 'destroy']);
        Route::get('customers', [UserController::class, 'indexCustomers'])->name('customers.index');
        Route::put('customers/{customer}', [UserController::class, 'updateCustomer'])->name('customers.update');
        Route::delete('customers/{customer}', [UserController::class, 'destroyCustomer'])->name('customers.destroy');
        Route::get('drivers', [UserController::class, 'indexDrivers'])->name('drivers.index');
        Route::post('drivers', [UserController::class, 'storeDriver'])->name('drivers.store');
        Route::put('drivers/{driver}', [UserController::class, 'updateDriver'])->name('drivers.update');
        Route::delete('drivers/{driver}', [UserController::class, 'destroyDriver'])->name('drivers.destroy');
        Route::resource('areas', \App\Http\Controllers\Admin\AreaController::class)->except(['show', 'create', 'edit']);
        Route::resource('hero-slides', \App\Http\Controllers\Admin\HeroSlideController::class)->except(['show', 'create', 'edit']);
        Route::post('hero-slides/reorder', [\App\Http\Controllers\Admin\HeroSlideController::class, 'updateOrder'])->name('hero-slides.reorder');
        Route::resource('branches', \App\Http\Controllers\Admin\BranchController::class)->except(['show', 'create', 'edit']);
    });

    // Orders
    Route::prefix('orders')->name('orders.')->group(function () {
        Route::get('/', [OrderController::class, 'index'])->name('index');
        Route::get('/create', [OrderController::class, 'create'])->name('create');
        Route::post('/', [OrderController::class, 'store'])->name('store');
        Route::get('/{order}', [OrderController::class, 'show'])->name('show');
        Route::put('/{order}/status', [OrderController::class, 'updateStatus'])->name('updateStatus');
    });

    // Invoices
    Route::prefix('invoices')->name('invoices.')->group(function () {
        Route::get('/', [InvoiceController::class, 'index'])->name('index');
        Route::put('/{invoice}/status', [InvoiceController::class, 'updateStatus'])->name('updateStatus');
    });

    // Stock Opname (legacy)
    Route::prefix('stock')->name('stock.')->group(function () {
        Route::get('/', [StockOpnameController::class, 'index'])->name('index');
        Route::post('/adjust', [StockOpnameController::class, 'adjust'])->name('adjust');
    });

    // WMS - Warehouse Management System
    Route::prefix('warehouse')->name('warehouse.')->group(function () {
        Route::get('/stock', [\App\Http\Controllers\Admin\StockMovementController::class, 'stock'])->name('stock');
        Route::get('/movements', [\App\Http\Controllers\Admin\StockMovementController::class, 'movements'])->name('movements');
        Route::get('/receive', [\App\Http\Controllers\Admin\StockMovementController::class, 'receiveForm'])->name('receive');
        Route::post('/receive', [\App\Http\Controllers\Admin\StockMovementController::class, 'receiveStore'])->name('receive.store');
        Route::get('/transfer', [\App\Http\Controllers\Admin\StockMovementController::class, 'transferForm'])->name('transfer');
        Route::post('/transfer', [\App\Http\Controllers\Admin\StockMovementController::class, 'transferStore'])->name('transfer.store');
    });
});

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Volt::route('settings/profile', 'settings.profile')->name('profile.edit');
    Volt::route('settings/password', 'settings.password')->name('user-password.edit');
    Volt::route('settings/appearance', 'settings.appearance')->name('appearance.edit');

    Volt::route('settings/two-factor', 'settings.two-factor')
        ->middleware(
            when(
                Features::canManageTwoFactorAuthentication()
                && Features::optionEnabled(Features::twoFactorAuthentication(), 'confirmPassword'),
                ['password.confirm'],
                [],
            ),
        )
        ->name('two-factor.show');
});

require base_path('routes/debug_images.php');
