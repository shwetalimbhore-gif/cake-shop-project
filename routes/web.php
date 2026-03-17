<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Front\HomeController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\AboutUsController;
use App\Http\Controllers\ProfileController as UserProfileController;
use App\Http\Controllers\Front\AccountController;
use App\Http\Controllers\Front\CheckoutController;
use App\Http\Controllers\Front\CartController;
use App\Http\Controllers\Front\TrackingController;
use App\Http\Controllers\Front\AboutController;
use App\Http\Controllers\RazorpayController;

/*
|--------------------------------------------------------------------------
| PUBLIC FRONTEND ROUTES
|--------------------------------------------------------------------------
*/
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/shop', [HomeController::class, 'shop'])->name('shop');
Route::get('/product/{slug}', [HomeController::class, 'productDetails'])->name('product.details');
Route::get('/about', [AboutController::class, 'index'])->name('about');
Route::get('/contact', [HomeController::class, 'contact'])->name('contact');
Route::post('/contact', [HomeController::class, 'submitContact'])->name('contact.submit');

/*
|--------------------------------------------------------------------------
| CART ROUTES
|--------------------------------------------------------------------------
*/
Route::prefix('cart')->name('cart.')->group(function () {
    Route::get('/', [CartController::class, 'index'])->name('index');
    Route::post('/add/{product}', [CartController::class, 'add'])->name('add');
    Route::post('/update/{item}', [CartController::class, 'update'])->name('update');
    Route::delete('/remove/{item}', [CartController::class, 'remove'])->name('remove');
    Route::delete('/clear', [CartController::class, 'clear'])->name('clear');
    Route::get('/count', [CartController::class, 'getCount'])->name('count');
});


/*
|--------------------------------------------------------------------------
| TRACKING ROUTES
|--------------------------------------------------------------------------
*/
Route::prefix('tracking')->name('tracking.')->group(function () {
    Route::get('/', [TrackingController::class, 'index'])->name('index');
    Route::post('/track', [TrackingController::class, 'track'])->name('track');
    Route::get('/status/{order}', [TrackingController::class, 'getTrackingStatus'])->name('status');
});

/*
|--------------------------------------------------------------------------
| CHECKOUT ROUTES (Require Login)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->prefix('checkout')->name('checkout.')->group(function () {
    Route::get('/', [CheckoutController::class, 'index'])->name('index');
    Route::post('/process', [CheckoutController::class, 'process'])->name('process');
    Route::get('/success/{order}', [CheckoutController::class, 'success'])->name('success');
});

/*
|--------------------------------------------------------------------------
| USER ACCOUNT ROUTES (Require Login)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->prefix('account')->name('account.')->group(function () {
    Route::get('/dashboard', [AccountController::class, 'dashboard'])->name('dashboard');
    Route::get('/profile', [AccountController::class, 'profile'])->name('profile');
    Route::put('/profile', [AccountController::class, 'updateProfile'])->name('profile.update');
    Route::get('/password', [AccountController::class, 'password'])->name('password');
    Route::put('/password', [AccountController::class, 'updatePassword'])->name('password.update');
    Route::get('/orders', [AccountController::class, 'orders'])->name('orders');
    Route::get('/orders/{order}', [AccountController::class, 'orderDetails'])->name('order.details');
});

/*
|--------------------------------------------------------------------------
| PAYMENT ROUTES (Require Login)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {
    Route::get('/payment/checkout/{order}', [RazorpayController::class, 'checkout'])->name('razorpay.checkout');
    Route::post('/payment/success', [RazorpayController::class, 'success'])->name('razorpay.success');
    Route::post('/payment/failure', [RazorpayController::class, 'failure'])->name('razorpay.failure');
});

/*
|--------------------------------------------------------------------------
| ADMIN ROUTES (Require Auth + Admin)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Categories
    Route::resource('categories', CategoryController::class);
    Route::post('categories/{category}/toggle-status', [CategoryController::class, 'toggleStatus'])->name('categories.toggle-status');

    // Products
    Route::resource('products', ProductController::class);
    Route::post('products/bulk-delete', [ProductController::class, 'bulkDelete'])->name('products.bulk-delete');
    Route::post('products/{product}/toggle-status', [ProductController::class, 'toggleStatus'])->name('products.toggle-status');
    Route::post('products/{product}/toggle-featured', [ProductController::class, 'toggleFeatured'])->name('products.toggle-featured');

    // Orders
    Route::prefix('orders')->name('orders.')->group(function () {
        Route::get('/', [OrderController::class, 'index'])->name('index');
        Route::get('/{order}', [OrderController::class, 'show'])->name('show');
        Route::get('/{order}/edit', [OrderController::class, 'edit'])->name('edit');
        Route::put('/{order}', [OrderController::class, 'update'])->name('update');
        Route::delete('/{order}', [OrderController::class, 'destroy'])->name('destroy');
        Route::post('/{order}/status', [OrderController::class, 'updateStatus'])->name('update-status');
        Route::post('/{order}/payment-status', [OrderController::class, 'updatePaymentStatus'])->name('update-payment-status');
        Route::get('/{order}/invoice', [OrderController::class, 'printInvoice'])->name('invoice');
        Route::get('/export/csv', [OrderController::class, 'export'])->name('export');
        Route::post('/{order}/tracking', [OrderController::class, 'updateTracking'])->name('tracking');
    });

    // Walk-in Order Routes
    Route::prefix('orders')->name('orders.')->group(function () {
        Route::get('/walkin/create', [App\Http\Controllers\Admin\WalkinOrderController::class, 'create'])->name('walkin.create');
        Route::post('/walkin/store', [App\Http\Controllers\Admin\WalkinOrderController::class, 'store'])->name('walkin.store');
        Route::get('/walkin/receipt/{order}', [App\Http\Controllers\Admin\WalkinOrderController::class, 'receipt'])->name('walkin.receipt');
        Route::get('/walkin/product/{product}', [App\Http\Controllers\Admin\WalkinOrderController::class, 'getProductDetails'])->name('walkin.product');
    });

    // Admin Profile
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [ProfileController::class, 'index'])->name('index');
        Route::put('/', [ProfileController::class, 'update'])->name('update');
        Route::put('/password', [ProfileController::class, 'changePassword'])->name('password');
        Route::get('/settings', [ProfileController::class, 'showSettings'])->name('settings');
        Route::put('/settings', [ProfileController::class, 'settings'])->name('settings.update');
    });

    // Site Settings
    Route::prefix('settings')->name('settings.')->group(function () {
        Route::get('/', [SettingsController::class, 'index'])->name('index');
        Route::put('/', [SettingsController::class, 'update'])->name('update');
        Route::post('/upload-logo', [SettingsController::class, 'uploadLogo'])->name('upload-logo');
    });

    // About Us (SINGLE VERSION - REMOVED DUPLICATES)
    Route::prefix('about')->name('about.')->group(function () {
        Route::get('/', [AboutUsController::class, 'index'])->name('index');
        Route::put('/', [AboutUsController::class, 'update'])->name('update');
    });

    // Reports Routes
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/', [App\Http\Controllers\Admin\ReportController::class, 'index'])->name('index');
        Route::get('/daily-sales', [App\Http\Controllers\Admin\ReportController::class, 'dailySales'])->name('daily-sales');
        Route::get('/monthly-sales', [App\Http\Controllers\Admin\ReportController::class, 'monthlySales'])->name('monthly-sales');
        Route::get('/product-sales', [App\Http\Controllers\Admin\ReportController::class, 'productSales'])->name('product-sales');
        Route::get('/category-sales', [App\Http\Controllers\Admin\ReportController::class, 'categorySales'])->name('category-sales');
        Route::get('/top-selling', [App\Http\Controllers\Admin\ReportController::class, 'topSelling'])->name('top-selling');
        Route::get('/low-selling', [App\Http\Controllers\Admin\ReportController::class, 'lowSelling'])->name('low-selling');
        Route::get('/order-summary', [App\Http\Controllers\Admin\ReportController::class, 'orderSummary'])->name('order-summary');
        Route::get('/custom-cake-orders', [App\Http\Controllers\Admin\ReportController::class, 'customCakeOrders'])->name('custom-cake-orders');
        Route::get('/delivery-vs-pickup', [App\Http\Controllers\Admin\ReportController::class, 'deliveryVsPickup'])->name('delivery-vs-pickup');
        Route::get('/top-customers', [App\Http\Controllers\Admin\ReportController::class, 'topCustomers'])->name('top-customers');
        Route::get('/customer-frequency', [App\Http\Controllers\Admin\ReportController::class, 'customerFrequency'])->name('customer-frequency');
        Route::get('/low-stock', [App\Http\Controllers\Admin\ReportController::class, 'lowStock'])->name('low-stock');
        Route::get('/payment-methods', [App\Http\Controllers\Admin\ReportController::class, 'paymentMethods'])->name('payment-methods');
        Route::get('/walkin', [App\Http\Controllers\Admin\WalkinOrderController::class, 'report'])->name('walkin');

    });

});

/*
|--------------------------------------------------------------------------
| DASHBOARD REDIRECT
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', function () {
        if (auth()->user() && auth()->user()->is_admin) {
            return redirect()->route('admin.dashboard');
        }
        return redirect()->route('home');
    })->name('dashboard');

    Route::post('/buy-now', [CartController::class, 'buyNow'])->name('buy.now');
});

/*
|--------------------------------------------------------------------------
| USER PROFILE ROUTES (Breeze)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::get('/user/profile', [UserProfileController::class, 'edit'])->name('user.profile.edit');
    Route::patch('/user/profile', [UserProfileController::class, 'update'])->name('user.profile.update');
    Route::delete('/user/profile', [UserProfileController::class, 'destroy'])->name('user.profile.destroy');
});


// // Webhook route (NO CSRF token - Razorpay sends without it)
// Route::post('/api/razorpay/webhook', [RazorpayWebhookController::class, 'handleWebhook'])
//     ->name('razorpay.webhook')
//     ->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);

/*
|--------------------------------------------------------------------------
| AUTH ROUTES (Breeze)
|--------------------------------------------------------------------------
*/
require __DIR__.'/auth.php';
