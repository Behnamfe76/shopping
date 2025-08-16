<?php

use Illuminate\Support\Facades\Route;
use Fereydooni\Shopping\app\Http\Controllers\Api\V1\AddressController as ApiAddressController;
use Fereydooni\Shopping\app\Http\Controllers\Api\V1\CategoryController as ApiCategoryController;
use Fereydooni\Shopping\app\Http\Controllers\Api\V1\BrandController as ApiBrandController;
use Fereydooni\Shopping\app\Http\Controllers\Api\V1\OrderController as ApiOrderController;
use Fereydooni\Shopping\app\Http\Controllers\Api\V1\OrderItemController as ApiOrderItemController;

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

    // Brand API routes
    Route::prefix('brands')->name('brands.')->group(function () {
        // List brands
        Route::get('/', [ApiBrandController::class, 'index'])->name('index');

        // Get brand count
        Route::get('/count', [ApiBrandController::class, 'getCount'])->name('count');

        // Search brands
        Route::get('/search', [ApiBrandController::class, 'search'])->name('search');

        // Get active brands
        Route::get('/active', [ApiBrandController::class, 'active'])->name('active');

        // Get featured brands
        Route::get('/featured', [ApiBrandController::class, 'featured'])->name('featured');

        // Get popular brands
        Route::get('/popular', [ApiBrandController::class, 'popular'])->name('popular');

        // Get brands by first letter
        Route::get('/alphabetical/{letter}', [ApiBrandController::class, 'alphabetical'])->name('alphabetical');

        // Get brands with products
        Route::get('/with-products', [ApiBrandController::class, 'getWithProducts'])->name('with-products');

        // Create brand
        Route::post('/', [ApiBrandController::class, 'store'])->name('store');

        // Brand-specific routes
        Route::prefix('{brand:slug}')->group(function () {
            // Show brand
            Route::get('/', [ApiBrandController::class, 'show'])->name('show');

            // Update brand (full update)
            Route::put('/', [ApiBrandController::class, 'update'])->name('update');

            // Update brand (partial update)
            Route::patch('/', [ApiBrandController::class, 'update'])->name('update.partial');

            // Delete brand
            Route::delete('/', [ApiBrandController::class, 'destroy'])->name('destroy');

            // Toggle active status
            Route::post('/toggle-active', [ApiBrandController::class, 'toggleActive'])->name('toggle-active');

            // Toggle featured status
            Route::post('/toggle-featured', [ApiBrandController::class, 'toggleFeatured'])->name('toggle-featured');

            // Upload brand media
            Route::post('/media', [ApiBrandController::class, 'uploadMedia'])->name('media.upload');

            // Delete brand media
            Route::delete('/media/{media}', [ApiBrandController::class, 'deleteMedia'])->name('media.delete');
        });
    });

    // Order API routes
    Route::prefix('orders')->name('orders.')->group(function () {
        // List orders
        Route::get('/', [ApiOrderController::class, 'index'])->name('index');

        // Get order count
        Route::get('/count', [ApiOrderController::class, 'getCount'])->name('count');

        // Get total revenue
        Route::get('/revenue', [ApiOrderController::class, 'getRevenue'])->name('revenue');

        // Search orders
        Route::get('/search', [ApiOrderController::class, 'search'])->name('search');

        // Get orders by status
        Route::get('/pending', [ApiOrderController::class, 'pending'])->name('pending');
        Route::get('/shipped', [ApiOrderController::class, 'shipped'])->name('shipped');
        Route::get('/completed', [ApiOrderController::class, 'completed'])->name('completed');
        Route::get('/cancelled', [ApiOrderController::class, 'cancelled'])->name('cancelled');

        // Create order
        Route::post('/', [ApiOrderController::class, 'store'])->name('store');

        // Order-specific routes
        Route::prefix('{order}')->group(function () {
            // Show order
            Route::get('/', [ApiOrderController::class, 'show'])->name('show');

            // Update order (full update)
            Route::put('/', [ApiOrderController::class, 'update'])->name('update');

            // Update order (partial update)
            Route::patch('/', [ApiOrderController::class, 'update'])->name('update.partial');

            // Delete order
            Route::delete('/', [ApiOrderController::class, 'destroy'])->name('destroy');

            // Order status management
            Route::post('/cancel', [ApiOrderController::class, 'cancel'])->name('cancel');
            Route::post('/mark-paid', [ApiOrderController::class, 'markPaid'])->name('mark-paid');
            Route::post('/mark-shipped', [ApiOrderController::class, 'markShipped'])->name('mark-shipped');
            Route::post('/mark-completed', [ApiOrderController::class, 'markCompleted'])->name('mark-completed');

            // Order notes
            Route::get('/notes', [ApiOrderController::class, 'getNotes'])->name('notes');
            Route::post('/notes', [ApiOrderController::class, 'addNote'])->name('add-note');

            // Process refund
            Route::post('/refund', [ApiOrderController::class, 'processRefund'])->name('refund');
        });
    });

    // OrderItem API routes
    Route::prefix('order-items')->name('order-items.')->group(function () {
        // List order items
        Route::get('/', [ApiOrderItemController::class, 'index'])->name('index');

        // Get order item count
        Route::get('/count', [ApiOrderItemController::class, 'getCount'])->name('count');

        // Get total revenue
        Route::get('/revenue', [ApiOrderItemController::class, 'getRevenue'])->name('revenue');

        // Search order items
        Route::get('/search', [ApiOrderItemController::class, 'search'])->name('search');

        // Get items by status
        Route::get('/shipped', [ApiOrderItemController::class, 'shipped'])->name('shipped');
        Route::get('/unshipped', [ApiOrderItemController::class, 'unshipped'])->name('unshipped');

        // Analytics
        Route::get('/top-selling', [ApiOrderItemController::class, 'topSelling'])->name('top-selling');
        Route::get('/low-stock', [ApiOrderItemController::class, 'lowStock'])->name('low-stock');

        // Get items by order/product
        Route::get('/by-order/{order}', [ApiOrderItemController::class, 'byOrder'])->name('by-order');
        Route::get('/by-product/{product}', [ApiOrderItemController::class, 'byProduct'])->name('by-product');

        // Inventory management
        Route::get('/inventory/{product}', [ApiOrderItemController::class, 'getInventoryLevel'])->name('inventory.level');
        Route::post('/inventory/reserve', [ApiOrderItemController::class, 'reserveInventory'])->name('inventory.reserve');
        Route::post('/inventory/release', [ApiOrderItemController::class, 'releaseInventory'])->name('inventory.release');

        // Create order item
        Route::post('/', [ApiOrderItemController::class, 'store'])->name('store');

        // OrderItem-specific routes
        Route::prefix('{orderItem}')->group(function () {
            // Show order item
            Route::get('/', [ApiOrderItemController::class, 'show'])->name('show');

            // Update order item (full update)
            Route::put('/', [ApiOrderItemController::class, 'update'])->name('update');

            // Update order item (partial update)
            Route::patch('/', [ApiOrderItemController::class, 'update'])->name('update.partial');

            // Delete order item
            Route::delete('/', [ApiOrderItemController::class, 'destroy'])->name('destroy');

            // Shipping operations
            Route::post('/mark-shipped', [ApiOrderItemController::class, 'markShipped'])->name('mark-shipped');
            Route::post('/mark-returned', [ApiOrderItemController::class, 'markReturned'])->name('mark-returned');
            Route::post('/process-refund', [ApiOrderItemController::class, 'processRefund'])->name('process-refund');

            // Get item status
            Route::get('/status', [ApiOrderItemController::class, 'getStatus'])->name('status');
        });
    });
});
