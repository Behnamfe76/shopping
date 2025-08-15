<?php

use Illuminate\Support\Facades\Route;
use Fereydooni\Shopping\app\Http\Controllers\Api\V1\AddressController as ApiAddressController;
use Fereydooni\Shopping\app\Http\Controllers\Api\V1\CategoryController as ApiCategoryController;

Route::prefix('api/v1')->name('api.v1.')->middleware(['auth:sanctum', 'throttle:60,1'])->group(function () {
    // Address API routes
    Route::prefix('addresses')->name('addresses.')->group(function () {
        // List addresses
        Route::get('/', [ApiAddressController::class, 'index'])->name('index');

        // Get address count
        Route::get('/count', [ApiAddressController::class, 'getCount'])->name('count');

        // Get default address by type
        Route::get('/default/{type}', [ApiAddressController::class, 'getDefault'])->name('default');

        // Search addresses
        Route::get('/search', [ApiAddressController::class, 'search'])->name('search');

        // Create address
        Route::post('/', [ApiAddressController::class, 'store'])->name('store');

        // Address-specific routes
        Route::prefix('{address}')->group(function () {
            // Show address
            Route::get('/', [ApiAddressController::class, 'show'])->name('show');

            // Update address (full update)
            Route::put('/', [ApiAddressController::class, 'update'])->name('update');

            // Update address (partial update)
            Route::patch('/', [ApiAddressController::class, 'update'])->name('update.partial');

            // Delete address
            Route::delete('/', [ApiAddressController::class, 'destroy'])->name('destroy');

            // Set as default
            Route::post('/default', [ApiAddressController::class, 'setDefault'])->name('set-default');
        });
    });

    // Category API routes
    Route::prefix('categories')->name('categories.')->group(function () {
        // List categories
        Route::get('/', [ApiCategoryController::class, 'index'])->name('index');

        // Get category count
        Route::get('/count', [ApiCategoryController::class, 'getCount'])->name('count');

        // Get root categories
        Route::get('/root', [ApiCategoryController::class, 'getRoot'])->name('root');

        // Get category tree
        Route::get('/tree', [ApiCategoryController::class, 'tree'])->name('tree');

        // Search categories
        Route::get('/search', [ApiCategoryController::class, 'search'])->name('search');

        // Get category statistics
        Route::get('/stats', [ApiCategoryController::class, 'getStats'])->name('stats');

        // Create category
        Route::post('/', [ApiCategoryController::class, 'store'])->name('store');

        // Reorder categories
        Route::post('/reorder', [ApiCategoryController::class, 'reorder'])->name('reorder');

        // Category-specific routes
        Route::prefix('{category:slug}')->group(function () {
            // Show category
            Route::get('/', [ApiCategoryController::class, 'show'])->name('show');

            // Update category (full update)
            Route::put('/', [ApiCategoryController::class, 'update'])->name('update');

            // Update category (partial update)
            Route::patch('/', [ApiCategoryController::class, 'update'])->name('update.partial');

            // Delete category
            Route::delete('/', [ApiCategoryController::class, 'destroy'])->name('destroy');

            // Set as default
            Route::post('/default', [ApiCategoryController::class, 'setDefault'])->name('set-default');

            // Move category
            Route::post('/move', [ApiCategoryController::class, 'move'])->name('move');

            // Get category children
            Route::get('/children', [ApiCategoryController::class, 'getChildren'])->name('children');

            // Get category ancestors
            Route::get('/ancestors', [ApiCategoryController::class, 'getAncestors'])->name('ancestors');

            // Get category descendants
            Route::get('/descendants', [ApiCategoryController::class, 'getDescendants'])->name('descendants');
        });
    });
});
