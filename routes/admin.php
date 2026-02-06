<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\ProductImageController;
use App\Http\Controllers\Admin\ProductOptionController;
use App\Http\Controllers\Admin\CategoryController;

// Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
//     Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
// });


// Route::prefix('admin')->name('admin.')->group(function () {
//     Route::resource('categories', CategoryController::class);
// });


// Route::resource('products', ProductController::class);

// Route::post('products/{product}/images', [ProductImageController::class, 'store'])
//     ->name('products.images.store');


// Route::post('products/{product}/options', [ProductOptionController::class, 'store'])
//     ->name('products.options.store');


Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::resource('categories', CategoryController::class);
    Route::resource('products', ProductController::class);

    Route::post('products/{product}/images', [ProductImageController::class, 'store'])
        ->name('products.images.store');

    Route::post('products/{product}/options', [ProductOptionController::class, 'store'])
        ->name('products.options.store');
});
