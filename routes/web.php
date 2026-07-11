<?php

use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;
use Livewire\Volt\Volt;

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\InvoiceController;
use App\Http\Controllers\Admin\PaymentController;
use App\Http\Controllers\Admin\SupplierController;
use App\Http\Controllers\Admin\StockOpnameController;
use App\Http\Controllers\Admin\WarehouseController;
use App\Http\Controllers\Admin\RackController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\DeliveryController;
use App\Http\Controllers\Admin\ReportsController;
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
        Route::resource('branches', \App\Http\Controllers\Admin\BranchController::class)->except(['create', 'edit']);
        Route::post('branches/{branch}/toggle-status', [\App\Http\Controllers\Admin\BranchController::class, 'toggleStatus'])->name('branches.toggle-status');
        Route::post('areas/{area}/toggle-status', [\App\Http\Controllers\Admin\AreaController::class, 'toggleStatus'])->name('areas.toggle-status');
        // Area Delivery Schedules
        Route::post('areas/{area}/schedules', [\App\Http\Controllers\Admin\AreaController::class, 'storeSchedule'])->name('areas.schedules.store');
        Route::put('areas/{area}/schedules/{schedule}', [\App\Http\Controllers\Admin\AreaController::class, 'updateSchedule'])->name('areas.schedules.update');
        Route::delete('areas/{area}/schedules/{schedule}', [\App\Http\Controllers\Admin\AreaController::class, 'deleteSchedule'])->name('areas.schedules.destroy');
    });

    // Orders
    Route::prefix('orders')->name('orders.')->group(function () {
        Route::get('/', [OrderController::class, 'index'])->name('index');
        Route::get('/{order}', [OrderController::class, 'show'])->name('show');
        Route::put('/{order}/status', [OrderController::class, 'updateStatus'])->name('updateStatus');
    });

    // Sales Order (manual / WhatsApp orders)
    Route::prefix('sales-order')->name('sales-order.')->group(function () {
        Route::get('/', [OrderController::class, 'salesOrderIndex'])->name('index');
        Route::get('/create', [OrderController::class, 'create'])->name('create');
        Route::post('/', [OrderController::class, 'store'])->name('store');
        Route::get('/search-customers', [OrderController::class, 'searchCustomers'])->name('search-customers');
    });

    // Invoices
    Route::prefix('invoices')->name('invoices.')->group(function () {
        Route::get('/', [InvoiceController::class, 'index'])->name('index');
        Route::get('/{invoice}', [InvoiceController::class, 'show'])->name('show');
        Route::get('/{invoice}/pdf', [InvoiceController::class, 'downloadPdf'])->name('pdf');
        Route::put('/{invoice}/status', [InvoiceController::class, 'updateStatus'])->name('updateStatus');
    });

    // Payment Management
    // Kuitansi Management
    Route::prefix('kuitansi')->name('kuitansi.')->group(function () {
        Route::get('/', [App\Http\Controllers\Admin\KuitansiController::class, 'index'])->name('index');
    });

    Route::prefix('payments')->name('payments.')->group(function () {
        Route::get('/', [PaymentController::class, 'index'])->name('index');
        Route::get('/create', [PaymentController::class, 'create'])->name('create');
        Route::post('/', [PaymentController::class, 'store'])->name('store');
        Route::get('/{payment}', [PaymentController::class, 'show'])->name('show');
        Route::post('/{payment}/approve', [PaymentController::class, 'approve'])->name('approve');
        Route::post('/{payment}/reject', [PaymentController::class, 'reject'])->name('reject');
        Route::get('/{payment}/receipt', [PaymentController::class, 'receipt'])->name('receipt');
        Route::get('/{payment}/receipt-form', [PaymentController::class, 'receiptForm'])->name('receipt-form');
        Route::post('/{payment}/receipt-form', [PaymentController::class, 'receiptStore'])->name('receipt-store');
    });

    // Supplier Management
    Route::prefix('suppliers')->name('suppliers.')->group(function () {
        Route::get('/', [SupplierController::class, 'index'])->name('index');
        Route::get('/create', [SupplierController::class, 'create'])->name('create');
        Route::post('/', [SupplierController::class, 'store'])->name('store');
        Route::get('/{supplier}', [SupplierController::class, 'show'])->name('show');
        Route::get('/{supplier}/edit', [SupplierController::class, 'edit'])->name('edit');
        Route::put('/{supplier}', [SupplierController::class, 'update'])->name('update');
        Route::delete('/{supplier}', [SupplierController::class, 'destroy'])->name('destroy');
        // Purchase Orders
        Route::get('/po/list', [SupplierController::class, 'purchaseOrders'])->name('po.index');
        Route::get('/po/create', [SupplierController::class, 'purchaseOrderCreate'])->name('po.create');
        Route::post('/po', [SupplierController::class, 'purchaseOrderStore'])->name('po.store');
        Route::get('/po/{purchaseOrder}', [SupplierController::class, 'purchaseOrderShow'])->name('po.show');
        Route::put('/po/{purchaseOrder}/status', [SupplierController::class, 'purchaseOrderUpdateStatus'])->name('po.updateStatus');
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
        Route::get('/expired', function () {
            return view('admin.warehouse.expired');
        })->name('expired');
    });

    // Live Chat
    Route::prefix('live-chat')->name('live-chat.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\LiveChatController::class, 'index'])->name('index');
        Route::post('/send', [\App\Http\Controllers\Admin\LiveChatController::class, 'sendMessage'])->name('send');
        Route::get('/customer-details', [\App\Http\Controllers\Admin\LiveChatController::class, 'getCustomerDetails'])->name('customer-details');
        Route::post('/action', [\App\Http\Controllers\Admin\LiveChatController::class, 'quickAction'])->name('action');
    });

    // Kasir POS
    Route::prefix('kasir-pos')->name('kasir.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\KasirController::class, 'index'])->name('index');
        Route::post('/checkout', [\App\Http\Controllers\Admin\KasirController::class, 'checkout'])->name('checkout');
        Route::get('/shift', function () {
            return view('admin.kasir.shift');
        })->name('shift');
    });

    // Marketplace Integration
    Route::prefix('marketplace')->name('marketplace.')->group(function () {
        // Connection
        Route::get('/connection', [\App\Http\Controllers\Admin\MarketplaceConnectionController::class, 'index'])->name('connection');
        Route::post('/connection/{id}/connect', [\App\Http\Controllers\Admin\MarketplaceConnectionController::class, 'connect'])->name('connect');
        Route::post('/connection/{id}/disconnect', [\App\Http\Controllers\Admin\MarketplaceConnectionController::class, 'disconnect'])->name('disconnect');
        
        // Sync
        Route::get('/sync/products', [\App\Http\Controllers\Admin\MarketplaceSyncController::class, 'products'])->name('sync.products');
        Route::get('/sync/inventory', [\App\Http\Controllers\Admin\MarketplaceSyncController::class, 'inventory'])->name('sync.inventory');
        
        // Orders
        Route::get('/orders', [\App\Http\Controllers\Admin\MarketplaceOrderController::class, 'index'])->name('orders');
        
        // Customers
        Route::get('/customers', [\App\Http\Controllers\Admin\MarketplaceCustomerController::class, 'index'])->name('customers');
        
        // Payments
        Route::get('/payments', [\App\Http\Controllers\Admin\MarketplacePaymentController::class, 'index'])->name('payments');
        
        // Reports
        Route::get('/reports', [\App\Http\Controllers\Admin\MarketplaceReportController::class, 'index'])->name('reports');
    });

    // Finance
    Route::prefix('finance')->name('finance.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\FinanceController::class, 'index'])->name('index');
        Route::post('/expense', [\App\Http\Controllers\Admin\FinanceController::class, 'storeExpense'])->name('store-expense');
    });

    // Delivery Management
    Route::prefix('deliveries')->name('deliveries.')->group(function () {
        Route::get('/', [DeliveryController::class, 'index'])->name('index');
        Route::post('/assign-bulk', [DeliveryController::class, 'assignBulk'])->name('assign-bulk');
        Route::post('/{order}/update', [DeliveryController::class, 'updateDelivery'])->name('update');
    });

    // Reports Dashboard
    Route::get('/reports', [ReportsController::class, 'index'])->name('reports.index');
    Route::get('/reports/export', [ReportsController::class, 'exportPdf'])->name('reports.export');

    // Employee Performance Points (Internal - Admin/Owner only)
    Route::prefix('performance')->name('performance.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\PerformancePointController::class, 'index'])->name('index');
        Route::get('/{user}', [\App\Http\Controllers\Admin\PerformancePointController::class, 'show'])->name('show');
        Route::post('/manual', [\App\Http\Controllers\Admin\PerformancePointController::class, 'storeManual'])->name('store-manual');
    });

    // Membership / VIP
    Route::get('/membership', function () {
        return view('admin.membership.index');
    })->name('membership');
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
Route::get('/test-layout', function() { return view('admin.reports.index'); });
Route::get('/test-layout-2', function() { return view('test-layout'); });
