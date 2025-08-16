<?php

use Illuminate\Support\Facades\Route;
use Fereydooni\Shopping\app\Http\Controllers\Api\V1\AddressController as ApiAddressController;
use Fereydooni\Shopping\app\Http\Controllers\Api\V1\CategoryController as ApiCategoryController;
use Fereydooni\Shopping\app\Http\Controllers\Api\V1\BrandController as ApiBrandController;
use Fereydooni\Shopping\app\Http\Controllers\Api\V1\OrderController as ApiOrderController;
use Fereydooni\Shopping\app\Http\Controllers\Api\V1\OrderItemController as ApiOrderItemController;
use Fereydooni\Shopping\app\Http\Controllers\Api\V1\OrderStatusHistoryController as ApiOrderStatusHistoryController;
use Fereydooni\Shopping\app\Http\Controllers\Api\V1\ProductAttributeController as ApiProductAttributeController;
use Fereydooni\Shopping\app\Http\Controllers\Api\V1\ProductAttributeValueController as ApiProductAttributeValueController;
use Fereydooni\Shopping\app\Http\Controllers\Api\V1\ProductDiscountController as ApiProductDiscountController;

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

    // OrderStatusHistory API routes
    Route::prefix('order-status-history')->name('order-status-history.')->group(function () {
        // List status history
        Route::get('/', [ApiOrderStatusHistoryController::class, 'index'])->name('index');

        // Get history count
        Route::get('/count', [ApiOrderStatusHistoryController::class, 'getCount'])->name('count');

        // Search status history
        Route::get('/search', [ApiOrderStatusHistoryController::class, 'search'])->name('search');

        // Get status change frequency
        Route::get('/frequency', [ApiOrderStatusHistoryController::class, 'getFrequency'])->name('frequency');

        // Get history by order/user/status
        Route::get('/by-order/{order}', [ApiOrderStatusHistoryController::class, 'byOrder'])->name('by-order');
        Route::get('/by-user/{user}', [ApiOrderStatusHistoryController::class, 'byUser'])->name('by-user');
        Route::get('/by-status/{status}', [ApiOrderStatusHistoryController::class, 'byStatus'])->name('by-status');

        // Get order timeline
        Route::get('/timeline/{order}', [ApiOrderStatusHistoryController::class, 'timeline'])->name('timeline');

        // Analytics and reports
        Route::get('/analytics', [ApiOrderStatusHistoryController::class, 'analytics'])->name('analytics');
        Route::get('/reports', [ApiOrderStatusHistoryController::class, 'reports'])->name('reports');

        // Create status history
        Route::post('/', [ApiOrderStatusHistoryController::class, 'store'])->name('store');

        // StatusHistory-specific routes
        Route::prefix('{history}')->group(function () {
            // Show status history
            Route::get('/', [ApiOrderStatusHistoryController::class, 'show'])->name('show');

            // Update status history (full update)
            Route::put('/', [ApiOrderStatusHistoryController::class, 'update'])->name('update');

            // Update status history (partial update)
            Route::patch('/', [ApiOrderStatusHistoryController::class, 'update'])->name('update.partial');

            // Delete status history
            Route::delete('/', [ApiOrderStatusHistoryController::class, 'destroy'])->name('destroy');
        });
    });

    // ProductAttribute API routes
    Route::prefix('product-attributes')->name('product-attributes.')->group(function () {
        // List product attributes
        Route::get('/', [ApiProductAttributeController::class, 'index'])->name('index');

        // Get attribute count
        Route::get('/count', [ApiProductAttributeController::class, 'getCount'])->name('count');

        // Get attribute groups
        Route::get('/groups', [ApiProductAttributeController::class, 'getGroups'])->name('groups');

        // Get attribute types
        Route::get('/types', [ApiProductAttributeController::class, 'getTypes'])->name('types');

        // Get input types
        Route::get('/input-types', [ApiProductAttributeController::class, 'getInputTypes'])->name('input-types');

        // Search product attributes
        Route::get('/search', [ApiProductAttributeController::class, 'search'])->name('search');

        // Filter by functionality
        Route::get('/required', [ApiProductAttributeController::class, 'required'])->name('required');
        Route::get('/searchable', [ApiProductAttributeController::class, 'searchable'])->name('searchable');
        Route::get('/filterable', [ApiProductAttributeController::class, 'filterable'])->name('filterable');
        Route::get('/comparable', [ApiProductAttributeController::class, 'comparable'])->name('comparable');
        Route::get('/visible', [ApiProductAttributeController::class, 'visible'])->name('visible');
        Route::get('/system', [ApiProductAttributeController::class, 'system'])->name('system');
        Route::get('/custom', [ApiProductAttributeController::class, 'custom'])->name('custom');

        // Filter by type/group/input type
        Route::get('/by-type/{type}', [ApiProductAttributeController::class, 'byType'])->name('by-type');
        Route::get('/by-group/{group}', [ApiProductAttributeController::class, 'byGroup'])->name('by-group');
        Route::get('/by-input-type/{inputType}', [ApiProductAttributeController::class, 'byInputType'])->name('by-input-type');

        // Create product attribute
        Route::post('/', [ApiProductAttributeController::class, 'store'])->name('store');

        // ProductAttribute-specific routes
        Route::prefix('{attribute:slug}')->group(function () {
            // Show product attribute
            Route::get('/', [ApiProductAttributeController::class, 'show'])->name('show');

            // Update product attribute (full update)
            Route::put('/', [ApiProductAttributeController::class, 'update'])->name('update');

            // Update product attribute (partial update)
            Route::patch('/', [ApiProductAttributeController::class, 'update'])->name('update.partial');

            // Delete product attribute
            Route::delete('/', [ApiProductAttributeController::class, 'destroy'])->name('destroy');

            // Toggle operations
            Route::post('/toggle-active', [ApiProductAttributeController::class, 'toggleActive'])->name('toggle-active');
            Route::post('/toggle-required', [ApiProductAttributeController::class, 'toggleRequired'])->name('toggle-required');
            Route::post('/toggle-searchable', [ApiProductAttributeController::class, 'toggleSearchable'])->name('toggle-searchable');
            Route::post('/toggle-filterable', [ApiProductAttributeController::class, 'toggleFilterable'])->name('toggle-filterable');
            Route::post('/toggle-comparable', [ApiProductAttributeController::class, 'toggleComparable'])->name('toggle-comparable');
            Route::post('/toggle-visible', [ApiProductAttributeController::class, 'toggleVisible'])->name('toggle-visible');

            // Analytics
            Route::get('/analytics', [ApiProductAttributeController::class, 'getAnalytics'])->name('analytics');
            Route::get('/usage', [ApiProductAttributeController::class, 'getUsage'])->name('usage');

            // Attribute values
            Route::prefix('values')->name('values.')->group(function () {
                Route::get('/', [ApiProductAttributeController::class, 'getValues'])->name('index');
                Route::post('/', [ApiProductAttributeController::class, 'addValue'])->name('store');
                Route::put('/{value}', [ApiProductAttributeController::class, 'updateValue'])->name('update');
                Route::delete('/{value}', [ApiProductAttributeController::class, 'deleteValue'])->name('destroy');
            });
        });
    });

    // ProductAttributeValue API routes
    Route::prefix('product-attribute-values')->name('product-attribute-values.')->group(function () {
        // List product attribute values
        Route::get('/', [ApiProductAttributeValueController::class, 'index'])->name('index');

        // Get value count
        Route::get('/count', [ApiProductAttributeValueController::class, 'getCount'])->name('count');

        // Search product attribute values
        Route::get('/search', [ApiProductAttributeValueController::class, 'search'])->name('search');

        // Filter by status
        Route::get('/active', [ApiProductAttributeValueController::class, 'active'])->name('active');
        Route::get('/default', [ApiProductAttributeValueController::class, 'default'])->name('default');

        // Usage analytics
        Route::get('/most-used', [ApiProductAttributeValueController::class, 'mostUsed'])->name('most-used');
        Route::get('/least-used', [ApiProductAttributeValueController::class, 'leastUsed'])->name('least-used');
        Route::get('/unused', [ApiProductAttributeValueController::class, 'unused'])->name('unused');

        // Filter by relationship
        Route::get('/by-attribute/{attribute}', [ApiProductAttributeValueController::class, 'byAttribute'])->name('by-attribute');
        Route::get('/by-variant/{variant}', [ApiProductAttributeValueController::class, 'byVariant'])->name('by-variant');
        Route::get('/by-product/{product}', [ApiProductAttributeValueController::class, 'byProduct'])->name('by-product');
        Route::get('/by-category/{category}', [ApiProductAttributeValueController::class, 'byCategory'])->name('by-category');
        Route::get('/by-brand/{brand}', [ApiProductAttributeValueController::class, 'byBrand'])->name('by-brand');

        // Create product attribute value
        Route::post('/', [ApiProductAttributeValueController::class, 'store'])->name('store');

        // ProductAttributeValue-specific routes
        Route::prefix('{value}')->group(function () {
            // Show product attribute value
            Route::get('/', [ApiProductAttributeValueController::class, 'show'])->name('show');

            // Update product attribute value (full update)
            Route::put('/', [ApiProductAttributeValueController::class, 'update'])->name('update');

            // Update product attribute value (partial update)
            Route::patch('/', [ApiProductAttributeValueController::class, 'update'])->name('update.partial');

            // Delete product attribute value
            Route::delete('/', [ApiProductAttributeValueController::class, 'destroy'])->name('destroy');

            // Status management
            Route::post('/toggle-active', [ApiProductAttributeValueController::class, 'toggleActive'])->name('toggle-active');
            Route::post('/toggle-default', [ApiProductAttributeValueController::class, 'toggleDefault'])->name('toggle-default');
            Route::post('/set-default', [ApiProductAttributeValueController::class, 'setDefault'])->name('set-default');

            // Usage and analytics
            Route::get('/usage', [ApiProductAttributeValueController::class, 'getUsage'])->name('usage');
            Route::get('/analytics', [ApiProductAttributeValueController::class, 'getAnalytics'])->name('analytics');

            // Relationship management
            Route::post('/assign-variant/{variant}', [ApiProductAttributeValueController::class, 'assignToVariant'])->name('assign-variant');
            Route::delete('/remove-variant/{variant}', [ApiProductAttributeValueController::class, 'removeFromVariant'])->name('remove-variant');
            Route::post('/assign-product/{product}', [ApiProductAttributeValueController::class, 'assignToProduct'])->name('assign-product');
            Route::delete('/remove-product/{product}', [ApiProductAttributeValueController::class, 'removeFromProduct'])->name('remove-product');
            Route::post('/assign-category/{category}', [ApiProductAttributeValueController::class, 'assignToCategory'])->name('assign-category');
            Route::delete('/remove-category/{category}', [ApiProductAttributeValueController::class, 'removeFromCategory'])->name('remove-category');
            Route::post('/assign-brand/{brand}', [ApiProductAttributeValueController::class, 'assignToBrand'])->name('assign-brand');
            Route::delete('/remove-brand/{brand}', [ApiProductAttributeValueController::class, 'removeFromBrand'])->name('remove-brand');
        });
    });

    // ProductDiscount API routes
    Route::prefix('product-discounts')->name('product-discounts.')->group(function () {
        // List product discounts
        Route::get('/', [ApiProductDiscountController::class, 'index'])->name('index');

        // Get discount count
        Route::get('/count', [ApiProductDiscountController::class, 'getCount'])->name('count');

        // Search product discounts
        Route::get('/search', [ApiProductDiscountController::class, 'search'])->name('search');

        // Filter by status
        Route::get('/active', [ApiProductDiscountController::class, 'active'])->name('active');
        Route::get('/expired', [ApiProductDiscountController::class, 'expired'])->name('expired');
        Route::get('/upcoming', [ApiProductDiscountController::class, 'upcoming'])->name('upcoming');
        Route::get('/current', [ApiProductDiscountController::class, 'current'])->name('current');

        // Filter by relationship
        Route::get('/by-product/{product}', [ApiProductDiscountController::class, 'byProduct'])->name('by-product');
        Route::get('/by-type/{type}', [ApiProductDiscountController::class, 'byType'])->name('by-type');

        // Get best discount for product
        Route::get('/best/{product}', [ApiProductDiscountController::class, 'getBest'])->name('best');

        // Create product discount
        Route::post('/', [ApiProductDiscountController::class, 'store'])->name('store');

        // ProductDiscount-specific routes
        Route::prefix('{discount}')->group(function () {
            // Show product discount
            Route::get('/', [ApiProductDiscountController::class, 'show'])->name('show');

            // Update product discount (full update)
            Route::put('/', [ApiProductDiscountController::class, 'update'])->name('update');

            // Update product discount (partial update)
            Route::patch('/', [ApiProductDiscountController::class, 'update'])->name('update.partial');

            // Delete product discount
            Route::delete('/', [ApiProductDiscountController::class, 'destroy'])->name('destroy');

            // Status management
            Route::post('/toggle-active', [ApiProductDiscountController::class, 'toggleActive'])->name('toggle-active');
            Route::post('/extend', [ApiProductDiscountController::class, 'extend'])->name('extend');
            Route::post('/shorten', [ApiProductDiscountController::class, 'shorten'])->name('shorten');

            // Calculation and application
            Route::post('/calculate', [ApiProductDiscountController::class, 'calculate'])->name('calculate');
            Route::post('/apply', [ApiProductDiscountController::class, 'apply'])->name('apply');
            Route::post('/validate', [ApiProductDiscountController::class, 'validate'])->name('validate');

            // Analytics and reporting
            Route::get('/analytics', [ApiProductDiscountController::class, 'analytics'])->name('analytics');
            Route::get('/performance', [ApiProductDiscountController::class, 'performance'])->name('performance');
            Route::get('/forecast', [ApiProductDiscountController::class, 'forecast'])->name('forecast');
        });

        // Recommendations
        Route::get('/recommendations/{product}', [ApiProductDiscountController::class, 'recommendations'])->name('recommendations');
    });
});
