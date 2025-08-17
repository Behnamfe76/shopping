<?php

use Illuminate\Support\Facades\Route;
use Fereydooni\Shopping\app\Http\Controllers\Web\ProductController as WebProductController;
use Fereydooni\Shopping\app\Http\Controllers\Web\CategoryController as WebCategoryController;
use Fereydooni\Shopping\app\Http\Controllers\Web\OrderController as WebOrderController;
use Fereydooni\Shopping\app\Http\Controllers\Web\CartController as WebCartController;

Route::prefix('shopping')->name('shopping.')->middleware(['web'])->group(function () {
    // Product routes
    Route::prefix('products')->name('products.')->group(function () {
        Route::get('/', [WebProductController::class, 'index'])->name('index');
        Route::get('/{product:slug}', [WebProductController::class, 'show'])->name('show');
        Route::get('/category/{category:slug}', [WebProductController::class, 'byCategory'])->name('by-category');
        Route::get('/brand/{brand:slug}', [WebProductController::class, 'byBrand'])->name('by-brand');
        Route::get('/search', [WebProductController::class, 'search'])->name('search');
    });

    // Category routes
    Route::prefix('categories')->name('categories.')->group(function () {
        Route::get('/', [WebCategoryController::class, 'index'])->name('index');
        Route::get('/{category:slug}', [WebCategoryController::class, 'show'])->name('show');
        Route::get('/tree', [WebCategoryController::class, 'tree'])->name('tree');
    });

    // Cart routes
    Route::prefix('cart')->name('cart.')->group(function () {
        Route::get('/', [WebCartController::class, 'index'])->name('index');
        Route::post('/add', [WebCartController::class, 'add'])->name('add');
        Route::put('/update/{item}', [WebCartController::class, 'update'])->name('update');
        Route::delete('/remove/{item}', [WebCartController::class, 'remove'])->name('remove');
        Route::post('/clear', [WebCartController::class, 'clear'])->name('clear');
        Route::post('/checkout', [WebCartController::class, 'checkout'])->name('checkout');
    });

    // Order routes (authenticated)
    Route::prefix('orders')->name('orders.')->middleware(['auth'])->group(function () {
        Route::get('/', [WebOrderController::class, 'index'])->name('index');
        Route::get('/{order}', [WebOrderController::class, 'show'])->name('show');
        Route::post('/', [WebOrderController::class, 'store'])->name('store');
        Route::post('/{order}/cancel', [WebOrderController::class, 'cancel'])->name('cancel');
    });

    // Dashboard routes (authenticated)
    Route::prefix('dashboard')->name('dashboard.')->middleware(['auth'])->group(function () {
        Route::get('/', function () {
            return view('shopping::dashboard.index');
        })->name('index');

        Route::get('/profile', function () {
            return view('shopping::dashboard.profile');
        })->name('profile');

        Route::get('/addresses', function () {
            return view('shopping::dashboard.addresses');
        })->name('addresses');
    });
});
