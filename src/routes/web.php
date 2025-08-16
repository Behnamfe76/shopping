<?php

use Illuminate\Support\Facades\Route;
use Fereydooni\Shopping\app\Http\Controllers\ProductController;
use Fereydooni\Shopping\app\Http\Controllers\CategoryController;
use Fereydooni\Shopping\app\Http\Controllers\BrandController;
use Fereydooni\Shopping\app\Http\Controllers\OrderController;
use Fereydooni\Shopping\app\Http\Controllers\OrderItemController;
use Fereydooni\Shopping\app\Http\Controllers\OrderStatusHistoryController;
use Fereydooni\Shopping\app\Http\Controllers\CartController;
use Fereydooni\Shopping\app\Http\Controllers\AddressController;

Route::prefix('shopping')->name('shopping.')->group(function () {
    // Product routes
    Route::get('/products', [ProductController::class, 'index'])->name('products.index');
    Route::get('/products/{product:slug}', [ProductController::class, 'show'])->name('products.show');

    // Category routes (with policy authorization)
    Route::middleware(['auth'])->group(function () {
        Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
        Route::get('/categories/create', [CategoryController::class, 'create'])->name('categories.create');
        Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');
        Route::get('/categories/{category:slug}', [CategoryController::class, 'show'])->name('categories.show');
        Route::get('/categories/{category:slug}/edit', [CategoryController::class, 'edit'])->name('categories.edit');
        Route::put('/categories/{category:slug}', [CategoryController::class, 'update'])->name('categories.update');
        Route::delete('/categories/{category:slug}', [CategoryController::class, 'destroy'])->name('categories.destroy');
        Route::post('/categories/{category:slug}/default', [CategoryController::class, 'setDefault'])->name('categories.set-default');
        Route::get('/categories/search', [CategoryController::class, 'search'])->name('categories.search');
        Route::get('/categories/tree', [CategoryController::class, 'tree'])->name('categories.tree');
        Route::post('/categories/reorder', [CategoryController::class, 'reorder'])->name('categories.reorder');
        Route::post('/categories/{category:slug}/move', [CategoryController::class, 'move'])->name('categories.move');
        Route::get('/categories/{category:slug}/children', [CategoryController::class, 'children'])->name('categories.children');
        Route::get('/categories/{category:slug}/ancestors', [CategoryController::class, 'ancestors'])->name('categories.ancestors');
        Route::get('/categories/{category:slug}/descendants', [CategoryController::class, 'descendants'])->name('categories.descendants');
        Route::get('/categories/stats', [CategoryController::class, 'stats'])->name('categories.stats');
    });

    // Brand routes (with policy authorization)
    Route::middleware(['auth'])->group(function () {
        Route::get('/brands', [BrandController::class, 'index'])->name('brands.index');
        Route::get('/brands/create', [BrandController::class, 'create'])->name('brands.create');
        Route::post('/brands', [BrandController::class, 'store'])->name('brands.store');
        Route::get('/brands/{brand:slug}', [BrandController::class, 'show'])->name('brands.show');
        Route::get('/brands/{brand:slug}/edit', [BrandController::class, 'edit'])->name('brands.edit');
        Route::put('/brands/{brand:slug}', [BrandController::class, 'update'])->name('brands.update');
        Route::delete('/brands/{brand:slug}', [BrandController::class, 'destroy'])->name('brands.destroy');
        Route::post('/brands/{brand:slug}/toggle-active', [BrandController::class, 'toggleActive'])->name('brands.toggle-active');
        Route::post('/brands/{brand:slug}/toggle-featured', [BrandController::class, 'toggleFeatured'])->name('brands.toggle-featured');
        Route::get('/brands/search', [BrandController::class, 'search'])->name('brands.search');
        Route::get('/brands/active', [BrandController::class, 'active'])->name('brands.active');
        Route::get('/brands/featured', [BrandController::class, 'featured'])->name('brands.featured');
        Route::get('/brands/popular', [BrandController::class, 'popular'])->name('brands.popular');
        Route::get('/brands/alphabetical/{letter}', [BrandController::class, 'alphabetical'])->name('brands.alphabetical');
    });

    // Cart routes
    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
    Route::put('/cart/update', [CartController::class, 'update'])->name('cart.update');
    Route::delete('/cart/remove', [CartController::class, 'remove'])->name('cart.remove');
    Route::delete('/cart/clear', [CartController::class, 'clear'])->name('cart.clear');

    // Order routes (with policy authorization)
    Route::middleware(['auth'])->group(function () {
        Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
        Route::get('/orders/create', [OrderController::class, 'create'])->name('orders.create');
        Route::post('/orders', [OrderController::class, 'store'])->name('orders.store');
        Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
        Route::get('/orders/{order}/edit', [OrderController::class, 'edit'])->name('orders.edit');
        Route::put('/orders/{order}', [OrderController::class, 'update'])->name('orders.update');
        Route::delete('/orders/{order}', [OrderController::class, 'destroy'])->name('orders.destroy');
        Route::post('/orders/{order}/cancel', [OrderController::class, 'cancel'])->name('orders.cancel');
        Route::post('/orders/{order}/mark-paid', [OrderController::class, 'markPaid'])->name('orders.mark-paid');
        Route::post('/orders/{order}/mark-shipped', [OrderController::class, 'markShipped'])->name('orders.mark-shipped');
        Route::post('/orders/{order}/mark-completed', [OrderController::class, 'markCompleted'])->name('orders.mark-completed');
        Route::get('/orders/search', [OrderController::class, 'search'])->name('orders.search');
        Route::get('/orders/pending', [OrderController::class, 'pending'])->name('orders.pending');
        Route::get('/orders/shipped', [OrderController::class, 'shipped'])->name('orders.shipped');
        Route::get('/orders/completed', [OrderController::class, 'completed'])->name('orders.completed');
        Route::get('/orders/cancelled', [OrderController::class, 'cancelled'])->name('orders.cancelled');
        Route::post('/orders/{order}/notes', [OrderController::class, 'addNote'])->name('orders.add-note');
        Route::get('/orders/{order}/notes', [OrderController::class, 'getNotes'])->name('orders.notes');
    });

    // Address routes (with policy authorization)
    Route::middleware(['auth'])->group(function () {
        Route::get('/addresses', [AddressController::class, 'index'])->name('addresses.index');
        Route::get('/addresses/{address}', [AddressController::class, 'show'])->name('addresses.show');
        Route::post('/addresses', [AddressController::class, 'store'])->name('addresses.store');
        Route::put('/addresses/{address}', [AddressController::class, 'update'])->name('addresses.update');
        Route::delete('/addresses/{address}', [AddressController::class, 'destroy'])->name('addresses.destroy');
        Route::post('/addresses/{address}/default', [AddressController::class, 'setDefault'])->name('addresses.set-default');
        Route::get('/addresses/search', [AddressController::class, 'search'])->name('addresses.search');
    });

    // OrderItem routes (with policy authorization)
    Route::middleware(['auth'])->group(function () {
        Route::get('/order-items', [OrderItemController::class, 'index'])->name('order-items.index');
        Route::get('/order-items/create', [OrderItemController::class, 'create'])->name('order-items.create');
        Route::post('/order-items', [OrderItemController::class, 'store'])->name('order-items.store');
        Route::get('/order-items/{orderItem}', [OrderItemController::class, 'show'])->name('order-items.show');
        Route::get('/order-items/{orderItem}/edit', [OrderItemController::class, 'edit'])->name('order-items.edit');
        Route::put('/order-items/{orderItem}', [OrderItemController::class, 'update'])->name('order-items.update');
        Route::delete('/order-items/{orderItem}', [OrderItemController::class, 'destroy'])->name('order-items.destroy');

        // Shipping operations
        Route::post('/order-items/{orderItem}/mark-shipped', [OrderItemController::class, 'markShipped'])->name('order-items.mark-shipped');
        Route::post('/order-items/{orderItem}/mark-returned', [OrderItemController::class, 'markReturned'])->name('order-items.mark-returned');
        Route::post('/order-items/{orderItem}/process-refund', [OrderItemController::class, 'processRefund'])->name('order-items.process-refund');

        // Search and filtering
        Route::get('/order-items/search', [OrderItemController::class, 'search'])->name('order-items.search');
        Route::get('/order-items/shipped', [OrderItemController::class, 'shipped'])->name('order-items.shipped');
        Route::get('/order-items/unshipped', [OrderItemController::class, 'unshipped'])->name('order-items.unshipped');
        Route::get('/order-items/top-selling', [OrderItemController::class, 'topSelling'])->name('order-items.top-selling');
        Route::get('/order-items/low-stock', [OrderItemController::class, 'lowStock'])->name('order-items.low-stock');

        // Order-specific routes
        Route::get('/order-items/by-order/{order}', [OrderItemController::class, 'byOrder'])->name('order-items.by-order');
        Route::get('/order-items/by-product/{product}', [OrderItemController::class, 'byProduct'])->name('order-items.by-product');
    });

    // OrderStatusHistory routes (with policy authorization)
    Route::middleware(['auth'])->group(function () {
        Route::get('/order-status-history', [OrderStatusHistoryController::class, 'index'])->name('order-status-history.index');
        Route::get('/order-status-history/{history}', [OrderStatusHistoryController::class, 'show'])->name('order-status-history.show');
        Route::post('/order-status-history', [OrderStatusHistoryController::class, 'store'])->name('order-status-history.store');
        Route::put('/order-status-history/{history}', [OrderStatusHistoryController::class, 'update'])->name('order-status-history.update');
        Route::delete('/order-status-history/{history}', [OrderStatusHistoryController::class, 'destroy'])->name('order-status-history.destroy');
        Route::get('/order-status-history/search', [OrderStatusHistoryController::class, 'search'])->name('order-status-history.search');
        Route::get('/order-status-history/by-order/{order}', [OrderStatusHistoryController::class, 'byOrder'])->name('order-status-history.by-order');
        Route::get('/order-status-history/by-user/{user}', [OrderStatusHistoryController::class, 'byUser'])->name('order-status-history.by-user');
        Route::get('/order-status-history/by-status/{status}', [OrderStatusHistoryController::class, 'byStatus'])->name('order-status-history.by-status');
        Route::get('/order-status-history/timeline/{order}', [OrderStatusHistoryController::class, 'timeline'])->name('order-status-history.timeline');
        Route::get('/order-status-history/analytics', [OrderStatusHistoryController::class, 'analytics'])->name('order-status-history.analytics');
        Route::get('/order-status-history/reports', [OrderStatusHistoryController::class, 'reports'])->name('order-status-history.reports');
    });
});
