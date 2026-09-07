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

// Role-specific login pages (admin/kasir/driver/preparist) — separate
// from the customer login at /login, see RoleLoginController. Not
// guarded by the 'guest' middleware on purpose: that middleware's
// default redirect target is the unrelated Fortify placeholder page,
// so an already-authenticated visit is instead handled inside the
// controller, sending them to wherever their own role actually goes.
foreach (['admin', 'kasir', 'driver', 'preparist'] as $role) {
    Route::get("/{$role}/login", [\App\Http\Controllers\Auth\RoleLoginController::class, 'show'])
        ->defaults('role', $role)
        ->name("{$role}.login");

    Route::post("/{$role}/login", [\App\Http\Controllers\Auth\RoleLoginController::class, 'store'])
        ->defaults('role', $role)
        ->name("{$role}.login.store");
}

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
    Route::get('/checkout/payments/{payment}/status', [\App\Http\Controllers\Customer\XenditPaymentController::class, 'status'])->name('checkout.payment.status');
    Route::post('/transactions/{order}/pay', [\App\Http\Controllers\Customer\XenditPaymentController::class, 'retry'])->name('transactions.pay');
});

// Xendit webhook — public, verified via the x-callback-token header instead
// of a browser session, so it must stay outside the 'auth' group and be
// exempt from CSRF (see bootstrap/app.php).
Route::post('/webhooks/xendit', [\App\Http\Controllers\Customer\XenditPaymentController::class, 'webhook'])->name('webhooks.xendit');

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

// Customer Notifications
Route::prefix('notifications')->name('notifications.')->middleware(['auth'])->group(function () {
    Route::get('/', [\App\Http\Controllers\Customer\NotificationController::class, 'index'])->name('index');
    Route::post('/{id}/read', [\App\Http\Controllers\Customer\NotificationController::class, 'markRead'])->name('read');
    Route::post('/read-all', [\App\Http\Controllers\Customer\NotificationController::class, 'markAllRead'])->name('read-all');
    Route::post('/fcm-token', [\App\Http\Controllers\Customer\NotificationController::class, 'registerFcmToken'])->name('fcm-token');
});

// Customer Complaints
Route::prefix('complaints')->name('complaints.')->middleware(['auth'])->group(function () {
    Route::get('/', [\App\Http\Controllers\Customer\ComplaintController::class, 'index'])->name('index');
    Route::get('/orders/{order}/create', [\App\Http\Controllers\Customer\ComplaintController::class, 'create'])->name('create');
    Route::post('/orders/{order}', [\App\Http\Controllers\Customer\ComplaintController::class, 'store'])->name('store');
});

// Customer Chat ("Deposusu Care")
Route::prefix('chat')->name('chat.')->middleware(['auth'])->group(function () {
    Route::get('/', [\App\Http\Controllers\Customer\ChatController::class, 'index'])->name('index');
    Route::get('/poll', [\App\Http\Controllers\Customer\ChatController::class, 'poll'])->name('poll');
    Route::post('/send', [\App\Http\Controllers\Customer\ChatController::class, 'send'])->name('send');
});

// Customer Subscriptions ("Rutin")
Route::prefix('subscriptions')->name('subscriptions.')->middleware(['auth'])->group(function () {
    Route::get('/', [\App\Http\Controllers\Customer\SubscriptionController::class, 'index'])->name('index');
    Route::post('/{subscription}/toggle', [\App\Http\Controllers\Customer\SubscriptionController::class, 'toggle'])->name('toggle');
    Route::delete('/{subscription}', [\App\Http\Controllers\Customer\SubscriptionController::class, 'destroy'])->name('destroy');
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
        Route::get('promos', [\App\Http\Controllers\Admin\CategoryPromoController::class, 'index'])->name('promos.index');
        Route::post('promos', [\App\Http\Controllers\Admin\CategoryPromoController::class, 'store'])->name('promos.store');
        Route::patch('promos/{discount}/toggle', [\App\Http\Controllers\Admin\CategoryPromoController::class, 'toggle'])->name('promos.toggle');
        Route::delete('promos/{discount}', [\App\Http\Controllers\Admin\CategoryPromoController::class, 'destroy'])->name('promos.destroy');
        Route::resource('warehouses', WarehouseController::class)->except(['show', 'edit', 'create']);
        Route::resource('racks', RackController::class)->only(['store', 'destroy']);
        Route::get('customers', [UserController::class, 'indexCustomers'])->name('customers.index');
        Route::put('customers/{customer}', [UserController::class, 'updateCustomer'])->name('customers.update');
        Route::delete('customers/{customer}', [UserController::class, 'destroyCustomer'])->name('customers.destroy');
        Route::patch('customers/{customer}/toggle-verified', [UserController::class, 'toggleCustomerVerification'])->name('customers.toggle-verified');
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

    // Driver Cash Reconciliation ("Setoran Kurir")
    Route::prefix('driver-cash')->name('driver-cash.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\DriverCashController::class, 'index'])->name('index');
        Route::post('/{collection}/confirm', [\App\Http\Controllers\Admin\DriverCashController::class, 'confirm'])->name('confirm');
        Route::post('/drivers/{driver}/confirm-all', [\App\Http\Controllers\Admin\DriverCashController::class, 'confirmAllForDriver'])->name('confirm-all');
    });

    // Customer Complaints
    Route::prefix('complaints')->name('complaints.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\ComplaintController::class, 'index'])->name('index');
        Route::put('/{complaint}', [\App\Http\Controllers\Admin\ComplaintController::class, 'update'])->name('update');
    });

    // Membership / VIP
    Route::get('/membership', function () {
        return view('admin.membership.index');
    })->name('membership');

    // Access Control Matrix
    Route::get('/acm', [\App\Http\Controllers\Admin\AcmController::class, 'index'])->name('acm.index');
    Route::post('/acm', [\App\Http\Controllers\Admin\AcmController::class, 'update'])->name('acm.update');
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

// Preparist Web Portal
Route::prefix('preparist')->name('preparist.')->middleware(['auth'])->group(function () {
    Route::get('/', [\App\Http\Controllers\Preparist\DashboardController::class, 'index'])->name('dashboard');
    Route::get('/orders', [\App\Http\Controllers\Preparist\OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [\App\Http\Controllers\Preparist\OrderController::class, 'show'])->name('orders.show');
    Route::post('/orders/{order}/start', [\App\Http\Controllers\Preparist\OrderController::class, 'start'])->name('orders.start');
    Route::post('/orders/{order}/cancel', [\App\Http\Controllers\Preparist\OrderController::class, 'cancel'])->name('orders.cancel');
    Route::post('/orders/{order}/finish', [\App\Http\Controllers\Preparist\OrderController::class, 'finish'])->name('orders.finish');
});

// Driver Web Portal
Route::prefix('driver')->name('driver.')->middleware(['auth'])->group(function () {
    Route::get('/', [\App\Http\Controllers\Driver\DashboardController::class, 'index'])->name('dashboard');
    Route::post('/check-in', [\App\Http\Controllers\Driver\DashboardController::class, 'checkIn'])->name('check-in');
    Route::post('/check-out', [\App\Http\Controllers\Driver\DashboardController::class, 'checkOut'])->name('check-out');
    Route::get('/orders', [\App\Http\Controllers\Driver\OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [\App\Http\Controllers\Driver\OrderController::class, 'show'])->name('orders.show');
    Route::post('/orders/{order}/pickup', [\App\Http\Controllers\Driver\OrderController::class, 'pickup'])->name('orders.pickup');
    Route::post('/orders/{order}/finish', [\App\Http\Controllers\Driver\OrderController::class, 'finish'])->name('orders.finish');
    Route::get('/cash', [\App\Http\Controllers\Driver\CashController::class, 'index'])->name('cash.index');
});

require base_path('routes/debug_images.php');
Route::get('/test-layout', function() { return view('admin.reports.index'); });
Route::get('/test-layout-2', function() { return view('test-layout'); });
