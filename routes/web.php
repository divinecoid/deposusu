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

// Cart Routes
Route::prefix('cart')->name('cart.')->group(function () {
    Route::get('/', [CartController::class, 'index'])->name('index');
    Route::post('/add', [CartController::class, 'add'])->name('add');
    Route::patch('/update/{cartItem}', [CartController::class, 'update'])->name('update');
    Route::delete('/remove/{cartItem}', [CartController::class, 'remove'])->name('remove');
    Route::patch('/update-routine/{cartItem}', [CartController::class, 'updateRoutine'])->name('update-routine');
    Route::delete('/clear', [CartController::class, 'clear'])->name('clear');
    Route::get('/data', [CartController::class, 'getCartData'])->name('data');
    Route::post('/checkout', [CartController::class, 'checkout'])->name('checkout');
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

    // Stock Opname
    Route::prefix('stock')->name('stock.')->group(function () {
        Route::get('/', [StockOpnameController::class, 'index'])->name('index');
        Route::post('/adjust', [StockOpnameController::class, 'adjust'])->name('adjust');
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
