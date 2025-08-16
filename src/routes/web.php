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
use Fereydooni\Shopping\app\Http\Controllers\ProductAttributeController;
use Fereydooni\Shopping\app\Http\Controllers\ProductAttributeValueController;
use Fereydooni\Shopping\app\Http\Controllers\ProductDiscountController;
use Fereydooni\Shopping\app\Http\Controllers\ProductMetaController;
use Fereydooni\Shopping\app\Http\Controllers\ProductReviewController;
use Fereydooni\Shopping\app\Http\Controllers\ProductTagController;

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

    // ProductAttribute routes (with policy authorization)
    Route::middleware(['auth'])->group(function () {
        Route::get('/product-attributes', [ProductAttributeController::class, 'index'])->name('product-attributes.index');
        Route::get('/product-attributes/create', [ProductAttributeController::class, 'create'])->name('product-attributes.create');
        Route::post('/product-attributes', [ProductAttributeController::class, 'store'])->name('product-attributes.store');
        Route::get('/product-attributes/{attribute:slug}', [ProductAttributeController::class, 'show'])->name('product-attributes.show');
        Route::get('/product-attributes/{attribute:slug}/edit', [ProductAttributeController::class, 'edit'])->name('product-attributes.edit');
        Route::put('/product-attributes/{attribute:slug}', [ProductAttributeController::class, 'update'])->name('product-attributes.update');
        Route::delete('/product-attributes/{attribute:slug}', [ProductAttributeController::class, 'destroy'])->name('product-attributes.destroy');

        // Toggle operations
        Route::post('/product-attributes/{attribute:slug}/toggle-active', [ProductAttributeController::class, 'toggleActive'])->name('product-attributes.toggle-active');
        Route::post('/product-attributes/{attribute:slug}/toggle-required', [ProductAttributeController::class, 'toggleRequired'])->name('product-attributes.toggle-required');
        Route::post('/product-attributes/{attribute:slug}/toggle-searchable', [ProductAttributeController::class, 'toggleSearchable'])->name('product-attributes.toggle-searchable');
        Route::post('/product-attributes/{attribute:slug}/toggle-filterable', [ProductAttributeController::class, 'toggleFilterable'])->name('product-attributes.toggle-filterable');
        Route::post('/product-attributes/{attribute:slug}/toggle-comparable', [ProductAttributeController::class, 'toggleComparable'])->name('product-attributes.toggle-comparable');
        Route::post('/product-attributes/{attribute:slug}/toggle-visible', [ProductAttributeController::class, 'toggleVisible'])->name('product-attributes.toggle-visible');

        // Search and filtering
        Route::get('/product-attributes/search', [ProductAttributeController::class, 'search'])->name('product-attributes.search');
        Route::get('/product-attributes/required', [ProductAttributeController::class, 'required'])->name('product-attributes.required');
        Route::get('/product-attributes/searchable', [ProductAttributeController::class, 'searchable'])->name('product-attributes.searchable');
        Route::get('/product-attributes/filterable', [ProductAttributeController::class, 'filterable'])->name('product-attributes.filterable');
        Route::get('/product-attributes/comparable', [ProductAttributeController::class, 'comparable'])->name('product-attributes.comparable');
        Route::get('/product-attributes/visible', [ProductAttributeController::class, 'visible'])->name('product-attributes.visible');
        Route::get('/product-attributes/system', [ProductAttributeController::class, 'system'])->name('product-attributes.system');
        Route::get('/product-attributes/custom', [ProductAttributeController::class, 'custom'])->name('product-attributes.custom');
        Route::get('/product-attributes/by-type/{type}', [ProductAttributeController::class, 'byType'])->name('product-attributes.by-type');
        Route::get('/product-attributes/by-group/{group}', [ProductAttributeController::class, 'byGroup'])->name('product-attributes.by-group');
        Route::get('/product-attributes/by-input-type/{inputType}', [ProductAttributeController::class, 'byInputType'])->name('product-attributes.by-input-type');

        // Attribute values
        Route::get('/product-attributes/{attribute:slug}/values', [ProductAttributeController::class, 'getValues'])->name('product-attributes.values');
        Route::post('/product-attributes/{attribute:slug}/values', [ProductAttributeController::class, 'addValue'])->name('product-attributes.add-value');
        Route::put('/product-attributes/{attribute:slug}/values/{value}', [ProductAttributeController::class, 'updateValue'])->name('product-attributes.update-value');
        Route::delete('/product-attributes/{attribute:slug}/values/{value}', [ProductAttributeController::class, 'deleteValue'])->name('product-attributes.delete-value');
    });

    // ProductAttributeValue routes (with policy authorization)
    Route::middleware(['auth'])->group(function () {
        // Basic CRUD operations
        Route::get('/product-attribute-values', [ProductAttributeValueController::class, 'index'])->name('product-attribute-values.index');
        Route::get('/product-attribute-values/create', [ProductAttributeValueController::class, 'create'])->name('product-attribute-values.create');
        Route::post('/product-attribute-values', [ProductAttributeValueController::class, 'store'])->name('product-attribute-values.store');
        Route::get('/product-attribute-values/{value}', [ProductAttributeValueController::class, 'show'])->name('product-attribute-values.show');
        Route::get('/product-attribute-values/{value}/edit', [ProductAttributeValueController::class, 'edit'])->name('product-attribute-values.edit');
        Route::put('/product-attribute-values/{value}', [ProductAttributeValueController::class, 'update'])->name('product-attribute-values.update');
        Route::delete('/product-attribute-values/{value}', [ProductAttributeValueController::class, 'destroy'])->name('product-attribute-values.destroy');

        // Status management
        Route::post('/product-attribute-values/{value}/toggle-active', [ProductAttributeValueController::class, 'toggleActive'])->name('product-attribute-values.toggle-active');
        Route::post('/product-attribute-values/{value}/toggle-default', [ProductAttributeValueController::class, 'toggleDefault'])->name('product-attribute-values.toggle-default');
        Route::post('/product-attribute-values/{value}/set-default', [ProductAttributeValueController::class, 'setDefault'])->name('product-attribute-values.set-default');

        // Search and filtering
        Route::get('/product-attribute-values/search', [ProductAttributeValueController::class, 'search'])->name('product-attribute-values.search');
        Route::get('/product-attribute-values/active', [ProductAttributeValueController::class, 'active'])->name('product-attribute-values.active');
        Route::get('/product-attribute-values/default', [ProductAttributeValueController::class, 'default'])->name('product-attribute-values.default');
        Route::get('/product-attribute-values/most-used', [ProductAttributeValueController::class, 'mostUsed'])->name('product-attribute-values.most-used');
        Route::get('/product-attribute-values/least-used', [ProductAttributeValueController::class, 'leastUsed'])->name('product-attribute-values.least-used');
        Route::get('/product-attribute-values/unused', [ProductAttributeValueController::class, 'unused'])->name('product-attribute-values.unused');

        // Relationship queries
        Route::get('/product-attribute-values/by-attribute/{attribute}', [ProductAttributeValueController::class, 'byAttribute'])->name('product-attribute-values.by-attribute');
        Route::get('/product-attribute-values/by-variant/{variant}', [ProductAttributeValueController::class, 'byVariant'])->name('product-attribute-values.by-variant');
        Route::get('/product-attribute-values/by-product/{product}', [ProductAttributeValueController::class, 'byProduct'])->name('product-attribute-values.by-product');
        Route::get('/product-attribute-values/by-category/{category}', [ProductAttributeValueController::class, 'byCategory'])->name('product-attribute-values.by-category');
        Route::get('/product-attribute-values/by-brand/{brand}', [ProductAttributeValueController::class, 'byBrand'])->name('product-attribute-values.by-brand');

        // Relationship management
        Route::post('/product-attribute-values/{value}/assign-variant/{variant}', [ProductAttributeValueController::class, 'assignToVariant'])->name('product-attribute-values.assign-variant');
        Route::delete('/product-attribute-values/{value}/remove-variant/{variant}', [ProductAttributeValueController::class, 'removeFromVariant'])->name('product-attribute-values.remove-variant');
        Route::post('/product-attribute-values/{value}/assign-product/{product}', [ProductAttributeValueController::class, 'assignToProduct'])->name('product-attribute-values.assign-product');
        Route::delete('/product-attribute-values/{value}/remove-product/{product}', [ProductAttributeValueController::class, 'removeFromProduct'])->name('product-attribute-values.remove-product');
        Route::post('/product-attribute-values/{value}/assign-category/{category}', [ProductAttributeValueController::class, 'assignToCategory'])->name('product-attribute-values.assign-category');
        Route::delete('/product-attribute-values/{value}/remove-category/{category}', [ProductAttributeValueController::class, 'removeFromCategory'])->name('product-attribute-values.remove-category');
        Route::post('/product-attribute-values/{value}/assign-brand/{brand}', [ProductAttributeValueController::class, 'assignToBrand'])->name('product-attribute-values.assign-brand');
        Route::delete('/product-attribute-values/{value}/remove-brand/{brand}', [ProductAttributeValueController::class, 'removeFromBrand'])->name('product-attribute-values.remove-brand');
    });

    // ProductDiscount routes (with policy authorization)
    Route::middleware(['auth'])->group(function () {
        // Basic CRUD operations
        Route::get('/product-discounts', [ProductDiscountController::class, 'index'])->name('product-discounts.index');
        Route::get('/product-discounts/create', [ProductDiscountController::class, 'create'])->name('product-discounts.create');
        Route::post('/product-discounts', [ProductDiscountController::class, 'store'])->name('product-discounts.store');
        Route::get('/product-discounts/{discount}', [ProductDiscountController::class, 'show'])->name('product-discounts.show');
        Route::get('/product-discounts/{discount}/edit', [ProductDiscountController::class, 'edit'])->name('product-discounts.edit');
        Route::put('/product-discounts/{discount}', [ProductDiscountController::class, 'update'])->name('product-discounts.update');
        Route::delete('/product-discounts/{discount}', [ProductDiscountController::class, 'destroy'])->name('product-discounts.destroy');

        // Status management
        Route::post('/product-discounts/{discount}/toggle-active', [ProductDiscountController::class, 'toggleActive'])->name('product-discounts.toggle-active');
        Route::post('/product-discounts/{discount}/extend', [ProductDiscountController::class, 'extend'])->name('product-discounts.extend');
        Route::post('/product-discounts/{discount}/shorten', [ProductDiscountController::class, 'shorten'])->name('product-discounts.shorten');

        // Search and filtering
        Route::get('/product-discounts/search', [ProductDiscountController::class, 'search'])->name('product-discounts.search');
        Route::get('/product-discounts/active', [ProductDiscountController::class, 'active'])->name('product-discounts.active');
        Route::get('/product-discounts/expired', [ProductDiscountController::class, 'expired'])->name('product-discounts.expired');
        Route::get('/product-discounts/upcoming', [ProductDiscountController::class, 'upcoming'])->name('product-discounts.upcoming');
        Route::get('/product-discounts/current', [ProductDiscountController::class, 'current'])->name('product-discounts.current');
        Route::get('/product-discounts/by-product/{product}', [ProductDiscountController::class, 'byProduct'])->name('product-discounts.by-product');
        Route::get('/product-discounts/by-type/{type}', [ProductDiscountController::class, 'byType'])->name('product-discounts.by-type');

        // Calculation and application
        Route::post('/product-discounts/{discount}/calculate', [ProductDiscountController::class, 'calculate'])->name('product-discounts.calculate');
        Route::post('/product-discounts/{discount}/apply', [ProductDiscountController::class, 'apply'])->name('product-discounts.apply');
        Route::post('/product-discounts/{discount}/validate', [ProductDiscountController::class, 'validate'])->name('product-discounts.validate');

        // Analytics and reporting
        Route::get('/product-discounts/{discount}/analytics', [ProductDiscountController::class, 'analytics'])->name('product-discounts.analytics');
        Route::get('/product-discounts/{discount}/performance', [ProductDiscountController::class, 'performance'])->name('product-discounts.performance');
        Route::get('/product-discounts/{discount}/forecast', [ProductDiscountController::class, 'forecast'])->name('product-discounts.forecast');
        Route::get('/product-discounts/recommendations/{product}', [ProductDiscountController::class, 'recommendations'])->name('product-discounts.recommendations');
    });

    // Product routes (with policy authorization)
    Route::middleware(['auth'])->group(function () {
        // Basic CRUD operations
        Route::get('/products', [ProductController::class, 'index'])->name('products.index');
        Route::get('/products/create', [ProductController::class, 'create'])->name('products.create');
        Route::post('/products', [ProductController::class, 'store'])->name('products.store');
        Route::get('/products/{product}', [ProductController::class, 'show'])->name('products.show');
        Route::get('/products/{product}/edit', [ProductController::class, 'edit'])->name('products.edit');
        Route::put('/products/{product}', [ProductController::class, 'update'])->name('products.update');
        Route::delete('/products/{product}', [ProductController::class, 'destroy'])->name('products.destroy');

        // Status management
        Route::post('/products/{product}/toggle-active', [ProductController::class, 'toggleActive'])->name('products.toggle-active');
        Route::post('/products/{product}/toggle-featured', [ProductController::class, 'toggleFeatured'])->name('products.toggle-featured');
        Route::post('/products/{product}/publish', [ProductController::class, 'publish'])->name('products.publish');
        Route::post('/products/{product}/unpublish', [ProductController::class, 'unpublish'])->name('products.unpublish');
        Route::post('/products/{product}/archive', [ProductController::class, 'archive'])->name('products.archive');

        // Search and filtering
        Route::get('/products/search', [ProductController::class, 'search'])->name('products.search');
        Route::get('/products/active', [ProductController::class, 'active'])->name('products.active');
        Route::get('/products/featured', [ProductController::class, 'featured'])->name('products.featured');
        Route::get('/products/in-stock', [ProductController::class, 'inStock'])->name('products.in-stock');
        Route::get('/products/low-stock', [ProductController::class, 'lowStock'])->name('products.low-stock');
        Route::get('/products/out-of-stock', [ProductController::class, 'outOfStock'])->name('products.out-of-stock');
        Route::get('/products/top-selling', [ProductController::class, 'topSelling'])->name('products.top-selling');
        Route::get('/products/most-viewed', [ProductController::class, 'mostViewed'])->name('products.most-viewed');
        Route::get('/products/best-rated', [ProductController::class, 'bestRated'])->name('products.best-rated');
        Route::get('/products/new-arrivals', [ProductController::class, 'newArrivals'])->name('products.new-arrivals');
        Route::get('/products/on-sale', [ProductController::class, 'onSale'])->name('products.on-sale');

        // Category and brand filtering
        Route::get('/products/by-category/{category}', [ProductController::class, 'byCategory'])->name('products.by-category');
        Route::get('/products/by-brand/{brand}', [ProductController::class, 'byBrand'])->name('products.by-brand');
        Route::get('/products/related/{product}', [ProductController::class, 'related'])->name('products.related');

        // Media management
        Route::post('/products/{product}/media', [ProductController::class, 'uploadMedia'])->name('products.upload-media');
        Route::delete('/products/{product}/media/{media}', [ProductController::class, 'deleteMedia'])->name('products.delete-media');

        // Product operations
        Route::post('/products/{product}/duplicate', [ProductController::class, 'duplicate'])->name('products.duplicate');
    });

    // ProductMeta routes (with policy authorization)
    Route::middleware(['auth'])->group(function () {
        // Basic CRUD operations
        Route::get('/product-meta', [ProductMetaController::class, 'index'])->name('product-meta.index');
        Route::post('/product-meta', [ProductMetaController::class, 'store'])->name('product-meta.store');
        Route::get('/product-meta/{meta}', [ProductMetaController::class, 'show'])->name('product-meta.show');
        Route::put('/product-meta/{meta}', [ProductMetaController::class, 'update'])->name('product-meta.update');
        Route::delete('/product-meta/{meta}', [ProductMetaController::class, 'destroy'])->name('product-meta.destroy');

        // Status management
        Route::post('/product-meta/{meta}/toggle-public', [ProductMetaController::class, 'togglePublic'])->name('product-meta.toggle-public');
        Route::post('/product-meta/{meta}/toggle-searchable', [ProductMetaController::class, 'toggleSearchable'])->name('product-meta.toggle-searchable');
        Route::post('/product-meta/{meta}/toggle-filterable', [ProductMetaController::class, 'toggleFilterable'])->name('product-meta.toggle-filterable');

        // Search and filtering
        Route::get('/product-meta/search', [ProductMetaController::class, 'search'])->name('product-meta.search');
        Route::get('/product-meta/public', [ProductMetaController::class, 'public'])->name('product-meta.public');
        Route::get('/product-meta/private', [ProductMetaController::class, 'private'])->name('product-meta.private');
        Route::get('/product-meta/searchable', [ProductMetaController::class, 'searchable'])->name('product-meta.searchable');
        Route::get('/product-meta/filterable', [ProductMetaController::class, 'filterable'])->name('product-meta.filterable');

        // Relationship queries
        Route::get('/product-meta/by-product/{product}', [ProductMetaController::class, 'byProduct'])->name('product-meta.by-product');
        Route::get('/product-meta/by-key/{key}', [ProductMetaController::class, 'byKey'])->name('product-meta.by-key');
        Route::get('/product-meta/by-type/{type}', [ProductMetaController::class, 'byType'])->name('product-meta.by-type');

        // Analytics and reporting
        Route::get('/product-meta/keys', [ProductMetaController::class, 'getKeys'])->name('product-meta.keys');
        Route::get('/product-meta/types', [ProductMetaController::class, 'getTypes'])->name('product-meta.types');
        Route::get('/product-meta/values/{key}', [ProductMetaController::class, 'getValuesByKey'])->name('product-meta.values-by-key');

        // Bulk operations
        Route::post('/product-meta/bulk-create/{product}', [ProductMetaController::class, 'bulkCreate'])->name('product-meta.bulk-create');
        Route::put('/product-meta/bulk-update/{product}', [ProductMetaController::class, 'bulkUpdate'])->name('product-meta.bulk-update');
        Route::delete('/product-meta/bulk-delete/{product}', [ProductMetaController::class, 'bulkDelete'])->name('product-meta.bulk-delete');

        // Import/Export operations
        Route::post('/product-meta/import/{product}', [ProductMetaController::class, 'import'])->name('product-meta.import');
        Route::get('/product-meta/export/{product}', [ProductMetaController::class, 'export'])->name('product-meta.export');
        Route::post('/product-meta/sync/{product}', [ProductMetaController::class, 'sync'])->name('product-meta.sync');

        // Analytics
        Route::get('/product-meta/analytics/{key}', [ProductMetaController::class, 'analytics'])->name('product-meta.analytics');
    });

    // ProductReview routes (with policy authorization)
    Route::middleware(['auth'])->group(function () {
        // Basic CRUD operations
        Route::get('/product-reviews', [ProductReviewController::class, 'index'])->name('product-reviews.index');
        Route::get('/product-reviews/create', [ProductReviewController::class, 'create'])->name('product-reviews.create');
        Route::post('/product-reviews', [ProductReviewController::class, 'store'])->name('product-reviews.store');
        Route::get('/product-reviews/{review}', [ProductReviewController::class, 'show'])->name('product-reviews.show');
        Route::get('/product-reviews/{review}/edit', [ProductReviewController::class, 'edit'])->name('product-reviews.edit');
        Route::put('/product-reviews/{review}', [ProductReviewController::class, 'update'])->name('product-reviews.update');
        Route::delete('/product-reviews/{review}', [ProductReviewController::class, 'destroy'])->name('product-reviews.destroy');

        // Status management
        Route::post('/product-reviews/{review}/approve', [ProductReviewController::class, 'approve'])->name('product-reviews.approve');
        Route::post('/product-reviews/{review}/reject', [ProductReviewController::class, 'reject'])->name('product-reviews.reject');
        Route::post('/product-reviews/{review}/feature', [ProductReviewController::class, 'feature'])->name('product-reviews.feature');
        Route::post('/product-reviews/{review}/unfeature', [ProductReviewController::class, 'unfeature'])->name('product-reviews.unfeature');
        Route::post('/product-reviews/{review}/verify', [ProductReviewController::class, 'verify'])->name('product-reviews.verify');
        Route::post('/product-reviews/{review}/unverify', [ProductReviewController::class, 'unverify'])->name('product-reviews.unverify');

        // Vote management
        Route::post('/product-reviews/{review}/vote', [ProductReviewController::class, 'vote'])->name('product-reviews.vote');
        Route::post('/product-reviews/{review}/flag', [ProductReviewController::class, 'flag'])->name('product-reviews.flag');

        // Search and filtering
        Route::get('/product-reviews/search', [ProductReviewController::class, 'search'])->name('product-reviews.search');
        Route::get('/product-reviews/approved', [ProductReviewController::class, 'approved'])->name('product-reviews.approved');
        Route::get('/product-reviews/pending', [ProductReviewController::class, 'pending'])->name('product-reviews.pending');
        Route::get('/product-reviews/rejected', [ProductReviewController::class, 'rejected'])->name('product-reviews.rejected');
        Route::get('/product-reviews/featured', [ProductReviewController::class, 'featured'])->name('product-reviews.featured');
        Route::get('/product-reviews/verified', [ProductReviewController::class, 'verified'])->name('product-reviews.verified');

        // Relationship queries
        Route::get('/product-reviews/by-product/{product}', [ProductReviewController::class, 'byProduct'])->name('product-reviews.by-product');
        Route::get('/product-reviews/by-user/{user}', [ProductReviewController::class, 'byUser'])->name('product-reviews.by-user');
        Route::get('/product-reviews/by-rating/{rating}', [ProductReviewController::class, 'byRating'])->name('product-reviews.by-rating');

        // Time-based queries
        Route::get('/product-reviews/recent', [ProductReviewController::class, 'recent'])->name('product-reviews.recent');
        Route::get('/product-reviews/popular', [ProductReviewController::class, 'popular'])->name('product-reviews.popular');
        Route::get('/product-reviews/helpful', [ProductReviewController::class, 'helpful'])->name('product-reviews.helpful');

        // Sentiment-based queries
        Route::get('/product-reviews/positive', [ProductReviewController::class, 'positive'])->name('product-reviews.positive');
        Route::get('/product-reviews/negative', [ProductReviewController::class, 'negative'])->name('product-reviews.negative');
        Route::get('/product-reviews/neutral', [ProductReviewController::class, 'neutral'])->name('product-reviews.neutral');

        // Moderation
        Route::get('/product-reviews/flagged', [ProductReviewController::class, 'flagged'])->name('product-reviews.flagged');
        Route::get('/product-reviews/moderation-queue', [ProductReviewController::class, 'moderationQueue'])->name('product-reviews.moderation-queue');

        // Analytics and statistics
        Route::get('/product-reviews/stats/{product}', [ProductReviewController::class, 'stats'])->name('product-reviews.stats');
        Route::get('/product-reviews/rating-distribution/{product}', [ProductReviewController::class, 'ratingDistribution'])->name('product-reviews.rating-distribution');
        Route::get('/product-reviews/average-rating/{product}', [ProductReviewController::class, 'averageRating'])->name('product-reviews.average-rating');
        Route::get('/product-reviews/analytics/{review}', [ProductReviewController::class, 'analytics'])->name('product-reviews.analytics');
        Route::get('/product-reviews/analytics-by-product/{product}', [ProductReviewController::class, 'analyticsByProduct'])->name('product-reviews.analytics-by-product');
        Route::get('/product-reviews/analytics-by-user/{user}', [ProductReviewController::class, 'analyticsByUser'])->name('product-reviews.analytics-by-user');
    });

    // ProductTag routes (with policy authorization)
    Route::middleware(['auth'])->group(function () {
        // Basic CRUD operations
        Route::get('/product-tags', [ProductTagController::class, 'index'])->name('product-tags.index');
        Route::get('/product-tags/create', [ProductTagController::class, 'create'])->name('product-tags.create');
        Route::post('/product-tags', [ProductTagController::class, 'store'])->name('product-tags.store');
        Route::get('/product-tags/{tag:slug}', [ProductTagController::class, 'show'])->name('product-tags.show');
        Route::get('/product-tags/{tag:slug}/edit', [ProductTagController::class, 'edit'])->name('product-tags.edit');
        Route::put('/product-tags/{tag:slug}', [ProductTagController::class, 'update'])->name('product-tags.update');
        Route::delete('/product-tags/{tag:slug}', [ProductTagController::class, 'destroy'])->name('product-tags.destroy');

        // Status management
        Route::post('/product-tags/{tag:slug}/toggle-active', [ProductTagController::class, 'toggleActive'])->name('product-tags.toggle-active');
        Route::post('/product-tags/{tag:slug}/toggle-featured', [ProductTagController::class, 'toggleFeatured'])->name('product-tags.toggle-featured');

        // Search and filtering
        Route::get('/product-tags/search', [ProductTagController::class, 'search'])->name('product-tags.search');
        Route::get('/product-tags/active', [ProductTagController::class, 'active'])->name('product-tags.active');
        Route::get('/product-tags/featured', [ProductTagController::class, 'featured'])->name('product-tags.featured');
        Route::get('/product-tags/popular', [ProductTagController::class, 'popular'])->name('product-tags.popular');
        Route::get('/product-tags/recent', [ProductTagController::class, 'recent'])->name('product-tags.recent');
        Route::get('/product-tags/by-color/{color}', [ProductTagController::class, 'byColor'])->name('product-tags.by-color');
        Route::get('/product-tags/by-icon/{icon}', [ProductTagController::class, 'byIcon'])->name('product-tags.by-icon');
        Route::get('/product-tags/by-usage/{count}', [ProductTagController::class, 'byUsage'])->name('product-tags.by-usage');

        // List methods
        Route::get('/product-tags/names', [ProductTagController::class, 'getNames'])->name('product-tags.names');
        Route::get('/product-tags/slugs', [ProductTagController::class, 'getSlugs'])->name('product-tags.slugs');
        Route::get('/product-tags/colors', [ProductTagController::class, 'getColors'])->name('product-tags.colors');
        Route::get('/product-tags/icons', [ProductTagController::class, 'getIcons'])->name('product-tags.icons');

        // Bulk operations
        Route::post('/product-tags/bulk-create', [ProductTagController::class, 'bulkCreate'])->name('product-tags.bulk-create');
        Route::put('/product-tags/bulk-update', [ProductTagController::class, 'bulkUpdate'])->name('product-tags.bulk-update');
        Route::delete('/product-tags/bulk-delete', [ProductTagController::class, 'bulkDelete'])->name('product-tags.bulk-delete');

        // Import/Export operations
        Route::post('/product-tags/import', [ProductTagController::class, 'import'])->name('product-tags.import');
        Route::get('/product-tags/export', [ProductTagController::class, 'export'])->name('product-tags.export');

        // Tag management
        Route::post('/product-tags/sync/{product}', [ProductTagController::class, 'sync'])->name('product-tags.sync');
        Route::post('/product-tags/merge/{tag1}/{tag2}', [ProductTagController::class, 'merge'])->name('product-tags.merge');
        Route::post('/product-tags/split/{tag}', [ProductTagController::class, 'split'])->name('product-tags.split');

        // Suggestions and autocomplete
        Route::get('/product-tags/suggestions', [ProductTagController::class, 'suggestions'])->name('product-tags.suggestions');
        Route::get('/product-tags/autocomplete', [ProductTagController::class, 'autocomplete'])->name('product-tags.autocomplete');

        // Relationships
        Route::get('/product-tags/related/{tag}', [ProductTagController::class, 'related'])->name('product-tags.related');
        Route::get('/product-tags/synonyms/{tag}', [ProductTagController::class, 'synonyms'])->name('product-tags.synonyms');
        Route::get('/product-tags/hierarchy/{tag}', [ProductTagController::class, 'hierarchy'])->name('product-tags.hierarchy');
        Route::get('/product-tags/tree', [ProductTagController::class, 'tree'])->name('product-tags.tree');
        Route::get('/product-tags/cloud', [ProductTagController::class, 'cloud'])->name('product-tags.cloud');
        Route::get('/product-tags/stats', [ProductTagController::class, 'stats'])->name('product-tags.stats');

        // Analytics
        Route::get('/product-tags/analytics/{tag}', [ProductTagController::class, 'analytics'])->name('product-tags.analytics');
        Route::get('/product-tags/trends/{tag}', [ProductTagController::class, 'trends'])->name('product-tags.trends');
        Route::get('/product-tags/comparison/{tag1}/{tag2}', [ProductTagController::class, 'comparison'])->name('product-tags.comparison');
        Route::get('/product-tags/recommendations/{product}', [ProductTagController::class, 'recommendations'])->name('product-tags.recommendations');
        Route::get('/product-tags/forecast/{tag}', [ProductTagController::class, 'forecast'])->name('product-tags.forecast');
        Route::get('/product-tags/performance/{tag}', [ProductTagController::class, 'performance'])->name('product-tags.performance');
    });
});
