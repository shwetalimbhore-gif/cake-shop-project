<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Front\HomeController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\ProfileController as UserProfileController;
use App\Http\Controllers\Front\AccountController;
use App\Http\Controllers\Front\CheckoutController;
use App\Http\Controllers\Front\CartController;
use App\Http\Controllers\Admin\AboutUsController;
use App\Http\Controllers\Front\TrackingController;
use App\Http\Controllers\Front\AboutController;


Route::get('/', [HomeController::class, 'index'])->name('home');
// Route::get('/about', [HomeController::class, 'about'])->name('about');
Route::get('/contact', [HomeController::class, 'contact'])->name('contact');

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::resource('categories', CategoryController::class);
    Route::post('categories/{category}/toggle-status', [CategoryController::class, 'toggleStatus'])->name('categories.toggle-status');

    Route::resource('products', ProductController::class);
    Route::post('products/bulk-delete', [ProductController::class, 'bulkDelete'])->name('products.bulk-delete');
    Route::post('products/{product}/toggle-status', [ProductController::class, 'toggleStatus'])->name('products.toggle-status');
    Route::post('products/{product}/toggle-featured', [ProductController::class, 'toggleFeatured'])->name('products.toggle-featured');

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

        Route::post('orders/{order}/tracking', [OrderController::class, 'updateTracking'])->name('orders.tracking');
        Route::post('orders/{order}/driver-location', [OrderController::class, 'updateDriverLocation'])->name('orders.driver-location');

});
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [ProfileController::class, 'index'])->name('index');
        Route::put('/', [ProfileController::class, 'update'])->name('update');
        Route::put('/password', [ProfileController::class, 'changePassword'])->name('password');
        Route::get('/settings', [ProfileController::class, 'showSettings'])->name('settings');
        Route::put('/settings', [ProfileController::class, 'settings'])->name('settings.update');
    });

    Route::prefix('settings')->name('settings.')->group(function () {
        Route::get('/', [SettingsController::class, 'index'])->name('index');
        Route::put('/', [SettingsController::class, 'update'])->name('update');
        Route::post('/upload-logo', [SettingsController::class, 'uploadLogo'])->name('upload-logo');
    });

    Route::prefix('about')->name('about.')->group(function () {
        Route::get('/', [AboutUsController::class, 'index'])->name('index');
        Route::put('/', [AboutUsController::class, 'update'])->name('update');

    });

    // Admin route (inside admin group)
    Route::resource('about', AboutUsController::class);
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', function () {
        if (auth()->user() && auth()->user()->is_admin) {
            return redirect()->route('admin.dashboard');
        }
        return redirect()->route('home');
    })->name('dashboard');
});

Route::middleware('auth')->group(function () {
    Route::get('/user/profile', [UserProfileController::class, 'edit'])->name('user.profile.edit');
    Route::patch('/user/profile', [UserProfileController::class, 'update'])->name('user.profile.update');
    Route::delete('/user/profile', [UserProfileController::class, 'destroy'])->name('user.profile.destroy');
});


// ============= PUBLIC FRONTEND ROUTES =============
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/shop', [HomeController::class, 'shop'])->name('shop');
Route::get('/product/{slug}', [HomeController::class, 'productDetails'])->name('product.details');
// Route::get('/about', [HomeController::class, 'about'])->name('about');
Route::get('/contact', [HomeController::class, 'contact'])->name('contact');
Route::post('/contact', [HomeController::class, 'submitContact'])->name('contact.submit');

// Frontend route
Route::get('/about', [AboutController::class, 'index'])->name('about');

// ============= CART ROUTES =============
// Cart routes
Route::prefix('cart')->name('cart.')->group(function () {
    Route::get('/', [App\Http\Controllers\Front\CartController::class, 'index'])->name('index');
    Route::post('/add/{product}', [App\Http\Controllers\Front\CartController::class, 'add'])->name('add');
    Route::post('/update/{item}', [App\Http\Controllers\Front\CartController::class, 'update'])->name('update');
    Route::delete('/remove/{item}', [App\Http\Controllers\Front\CartController::class, 'remove'])->name('remove');
    Route::delete('/clear', [App\Http\Controllers\Front\CartController::class, 'clear'])->name('clear');
    Route::get('/count', [App\Http\Controllers\Front\CartController::class, 'getCount'])->name('count');
});

Route::prefix('tracking')->name('tracking.')->group(function () {
    Route::get('/', [TrackingController::class, 'index'])->name('index');
    Route::post('/track', [TrackingController::class, 'track'])->name('track');
    Route::get('/status/{order}', [TrackingController::class, 'getTrackingStatus'])->name('status');
});

// ============= CHECKOUT ROUTES (Require Login) =============
Route::middleware(['auth'])->prefix('checkout')->name('checkout.')->group(function () {
    Route::get('/', [CheckoutController::class, 'index'])->name('index');
    Route::post('/process', [CheckoutController::class, 'process'])->name('process');
    Route::get('/success/{order}', [CheckoutController::class, 'success'])->name('success');
});

// ============= USER ACCOUNT ROUTES (Require Login) =============

Route::middleware(['auth'])->prefix('account')->name('account.')->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\Front\AccountController::class, 'dashboard'])->name('dashboard');
    Route::get('/profile', [App\Http\Controllers\Front\AccountController::class, 'profile'])->name('profile');
    Route::put('/profile', [App\Http\Controllers\Front\AccountController::class, 'updateProfile'])->name('profile.update');
    Route::get('/password', [App\Http\Controllers\Front\AccountController::class, 'password'])->name('password');
    Route::put('/password', [App\Http\Controllers\Front\AccountController::class, 'updatePassword'])->name('password.update');
    Route::get('/orders', [App\Http\Controllers\Front\AccountController::class, 'orders'])->name('orders');
    Route::get('/orders/{order}', [App\Http\Controllers\Front\AccountController::class, 'orderDetails'])->name('order.details');
});


require __DIR__.'/auth.php';
