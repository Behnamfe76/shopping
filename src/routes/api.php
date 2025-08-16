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
use Fereydooni\Shopping\app\Http\Controllers\Api\V1\ProductMetaController as ApiProductMetaController;
use Fereydooni\Shopping\app\Http\Controllers\Api\V1\ProductReviewController as ApiProductReviewController;
use Fereydooni\Shopping\app\Http\Controllers\Api\V1\ProductTagController as ApiProductTagController;
use Fereydooni\Shopping\app\Http\Controllers\Api\V1\ProductVariantController as ApiProductVariantController;
use Fereydooni\Shopping\app\Http\Controllers\Api\V1\ShipmentController as ApiShipmentController;
use Fereydooni\Shopping\app\Http\Controllers\Api\V1\ShipmentItemController as ApiShipmentItemController;
use Fereydooni\Shopping\app\Http\Controllers\Api\V1\TransactionController as ApiTransactionController;

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

    // Product API routes
    Route::prefix('products')->name('products.')->group(function () {
        // List products
        Route::get('/', [ApiProductController::class, 'index'])->name('index');

        // Get product count
        Route::get('/count', [ApiProductController::class, 'getCount'])->name('count');

        // Get product analytics
        Route::get('/analytics', [ApiProductController::class, 'getAnalytics'])->name('analytics');

        // Search products
        Route::get('/search', [ApiProductController::class, 'search'])->name('search');

        // Filter by status
        Route::get('/active', [ApiProductController::class, 'active'])->name('active');
        Route::get('/featured', [ApiProductController::class, 'featured'])->name('featured');
        Route::get('/in-stock', [ApiProductController::class, 'inStock'])->name('in-stock');
        Route::get('/low-stock', [ApiProductController::class, 'lowStock'])->name('low-stock');
        Route::get('/out-of-stock', [ApiProductController::class, 'outOfStock'])->name('out-of-stock');

        // Analytics and reporting
        Route::get('/top-selling', [ApiProductController::class, 'topSelling'])->name('top-selling');
        Route::get('/most-viewed', [ApiProductController::class, 'mostViewed'])->name('most-viewed');
        Route::get('/best-rated', [ApiProductController::class, 'bestRated'])->name('best-rated');
        Route::get('/new-arrivals', [ApiProductController::class, 'newArrivals'])->name('new-arrivals');
        Route::get('/on-sale', [ApiProductController::class, 'onSale'])->name('on-sale');

        // Filter by relationship
        Route::get('/by-category/{category}', [ApiProductController::class, 'byCategory'])->name('by-category');
        Route::get('/by-brand/{brand}', [ApiProductController::class, 'byBrand'])->name('by-brand');
        Route::get('/related/{product}', [ApiProductController::class, 'related'])->name('related');

        // Bulk operations
        Route::post('/bulk-operations', [ApiProductController::class, 'bulkOperations'])->name('bulk-operations');

        // Inventory management
        Route::get('/inventory/{product}', [ApiProductController::class, 'getInventoryLevel'])->name('inventory');
        Route::post('/inventory/update', [ApiProductController::class, 'updateInventory'])->name('update-inventory');

        // Create product
        Route::post('/', [ApiProductController::class, 'store'])->name('store');

        // Product-specific routes
        Route::prefix('{product}')->group(function () {
            // Show product
            Route::get('/', [ApiProductController::class, 'show'])->name('show');

            // Update product (full update)
            Route::put('/', [ApiProductController::class, 'update'])->name('update');

            // Update product (partial update)
            Route::patch('/', [ApiProductController::class, 'update'])->name('update.partial');

            // Delete product
            Route::delete('/', [ApiProductController::class, 'destroy'])->name('destroy');

            // Status management
            Route::post('/toggle-active', [ApiProductController::class, 'toggleActive'])->name('toggle-active');
            Route::post('/toggle-featured', [ApiProductController::class, 'toggleFeatured'])->name('toggle-featured');
            Route::post('/publish', [ApiProductController::class, 'publish'])->name('publish');
            Route::post('/unpublish', [ApiProductController::class, 'unpublish'])->name('unpublish');
            Route::post('/archive', [ApiProductController::class, 'archive'])->name('archive');

            // Media management
            Route::post('/media', [ApiProductController::class, 'uploadMedia'])->name('upload-media');
            Route::delete('/media/{media}', [ApiProductController::class, 'deleteMedia'])->name('delete-media');

            // Product operations
            Route::post('/duplicate', [ApiProductController::class, 'duplicate'])->name('duplicate');
        });
    });

    // ProductMeta API routes
    Route::prefix('product-meta')->name('product-meta.')->group(function () {
        // List product meta
        Route::get('/', [ApiProductMetaController::class, 'index'])->name('index');

        // Get product meta count
        Route::get('/count', [ApiProductMetaController::class, 'getCount'])->name('count');

        // Search product meta
        Route::get('/search', [ApiProductMetaController::class, 'search'])->name('search');

        // Filter by visibility
        Route::get('/public', [ApiProductMetaController::class, 'public'])->name('public');
        Route::get('/private', [ApiProductMetaController::class, 'private'])->name('private');
        Route::get('/searchable', [ApiProductMetaController::class, 'searchable'])->name('searchable');
        Route::get('/filterable', [ApiProductMetaController::class, 'filterable'])->name('filterable');

        // Filter by relationship
        Route::get('/by-product/{product}', [ApiProductMetaController::class, 'byProduct'])->name('by-product');
        Route::get('/by-key/{key}', [ApiProductMetaController::class, 'byKey'])->name('by-key');
        Route::get('/by-type/{type}', [ApiProductMetaController::class, 'byType'])->name('by-type');

        // Analytics and reporting
        Route::get('/keys', [ApiProductMetaController::class, 'getKeys'])->name('keys');
        Route::get('/types', [ApiProductMetaController::class, 'getTypes'])->name('types');
        Route::get('/values/{key}', [ApiProductMetaController::class, 'getValuesByKey'])->name('values-by-key');

        // Bulk operations
        Route::post('/bulk-create/{product}', [ApiProductMetaController::class, 'bulkCreate'])->name('bulk-create');
        Route::put('/bulk-update/{product}', [ApiProductMetaController::class, 'bulkUpdate'])->name('bulk-update');
        Route::delete('/bulk-delete/{product}', [ApiProductMetaController::class, 'bulkDelete'])->name('bulk-delete');

        // Import/Export operations
        Route::post('/import/{product}', [ApiProductMetaController::class, 'import'])->name('import');
        Route::get('/export/{product}', [ApiProductMetaController::class, 'export'])->name('export');
        Route::post('/sync/{product}', [ApiProductMetaController::class, 'sync'])->name('sync');

        // Analytics
        Route::get('/analytics/{key}', [ApiProductMetaController::class, 'analytics'])->name('analytics');

        // Create product meta
        Route::post('/', [ApiProductMetaController::class, 'store'])->name('store');

        // ProductMeta-specific routes
        Route::prefix('{meta}')->group(function () {
            // Show product meta
            Route::get('/', [ApiProductMetaController::class, 'show'])->name('show');

            // Update product meta (full update)
            Route::put('/', [ApiProductMetaController::class, 'update'])->name('update');

            // Update product meta (partial update)
            Route::patch('/', [ApiProductMetaController::class, 'update'])->name('update.partial');

            // Delete product meta
            Route::delete('/', [ApiProductMetaController::class, 'destroy'])->name('destroy');

            // Status management
            Route::post('/toggle-public', [ApiProductMetaController::class, 'togglePublic'])->name('toggle-public');
            Route::post('/toggle-searchable', [ApiProductMetaController::class, 'toggleSearchable'])->name('toggle-searchable');
            Route::post('/toggle-filterable', [ApiProductMetaController::class, 'toggleFilterable'])->name('toggle-filterable');
        });
    });

    // ProductReview API routes
    Route::prefix('product-reviews')->name('product-reviews.')->group(function () {
        // List product reviews
        Route::get('/', [ApiProductReviewController::class, 'index'])->name('index');

        // Get product review count
        Route::get('/count', [ApiProductReviewController::class, 'getCount'])->name('count');

        // Search product reviews
        Route::get('/search', [ApiProductReviewController::class, 'search'])->name('search');

        // Filter by status
        Route::get('/approved', [ApiProductReviewController::class, 'approved'])->name('approved');
        Route::get('/pending', [ApiProductReviewController::class, 'pending'])->name('pending');
        Route::get('/rejected', [ApiProductReviewController::class, 'rejected'])->name('rejected');
        Route::get('/featured', [ApiProductReviewController::class, 'featured'])->name('featured');
        Route::get('/verified', [ApiProductReviewController::class, 'verified'])->name('verified');

        // Filter by relationship
        Route::get('/by-product/{product}', [ApiProductReviewController::class, 'byProduct'])->name('by-product');
        Route::get('/by-user/{user}', [ApiProductReviewController::class, 'byUser'])->name('by-user');
        Route::get('/by-rating/{rating}', [ApiProductReviewController::class, 'byRating'])->name('by-rating');

        // Time-based queries
        Route::get('/recent', [ApiProductReviewController::class, 'recent'])->name('recent');
        Route::get('/popular', [ApiProductReviewController::class, 'popular'])->name('popular');
        Route::get('/helpful', [ApiProductReviewController::class, 'helpful'])->name('helpful');

        // Sentiment-based queries
        Route::get('/positive', [ApiProductReviewController::class, 'positive'])->name('positive');
        Route::get('/negative', [ApiProductReviewController::class, 'negative'])->name('negative');
        Route::get('/neutral', [ApiProductReviewController::class, 'neutral'])->name('neutral');

        // Moderation
        Route::get('/flagged', [ApiProductReviewController::class, 'flagged'])->name('flagged');
        Route::get('/moderation-queue', [ApiProductReviewController::class, 'moderationQueue'])->name('moderation-queue');

        // Analytics and statistics
        Route::get('/stats/{product}', [ApiProductReviewController::class, 'stats'])->name('stats');
        Route::get('/rating-distribution/{product}', [ApiProductReviewController::class, 'ratingDistribution'])->name('rating-distribution');
        Route::get('/average-rating/{product}', [ApiProductReviewController::class, 'averageRating'])->name('average-rating');
        Route::get('/analytics/{review}', [ApiProductReviewController::class, 'analytics'])->name('analytics');
        Route::get('/analytics-by-product/{product}', [ApiProductReviewController::class, 'analyticsByProduct'])->name('analytics-by-product');
        Route::get('/analytics-by-user/{user}', [ApiProductReviewController::class, 'analyticsByUser'])->name('analytics-by-user');

        // Create product review
        Route::post('/', [ApiProductReviewController::class, 'store'])->name('store');

        // ProductReview-specific routes
        Route::prefix('{review}')->group(function () {
            // Show product review
            Route::get('/', [ApiProductReviewController::class, 'show'])->name('show');

            // Update product review (full update)
            Route::put('/', [ApiProductReviewController::class, 'update'])->name('update');

            // Update product review (partial update)
            Route::patch('/', [ApiProductReviewController::class, 'update'])->name('update.partial');

            // Delete product review
            Route::delete('/', [ApiProductReviewController::class, 'destroy'])->name('destroy');

            // Status management
            Route::post('/approve', [ApiProductReviewController::class, 'approve'])->name('approve');
            Route::post('/reject', [ApiProductReviewController::class, 'reject'])->name('reject');
            Route::post('/feature', [ApiProductReviewController::class, 'feature'])->name('feature');
            Route::post('/unfeature', [ApiProductReviewController::class, 'unfeature'])->name('unfeature');
            Route::post('/verify', [ApiProductReviewController::class, 'verify'])->name('verify');
            Route::post('/unverify', [ApiProductReviewController::class, 'unverify'])->name('unverify');

            // Vote management
            Route::post('/vote', [ApiProductReviewController::class, 'vote'])->name('vote');
            Route::post('/flag', [ApiProductReviewController::class, 'flag'])->name('flag');
        });
    });

    // ProductTag API routes
    Route::prefix('product-tags')->name('product-tags.')->group(function () {
        // List product tags
        Route::get('/', [ApiProductTagController::class, 'index'])->name('index');

        // Get product tag count
        Route::get('/count', [ApiProductTagController::class, 'getCount'])->name('count');

        // Search product tags
        Route::get('/search', [ApiProductTagController::class, 'search'])->name('search');

        // Filter by status
        Route::get('/active', [ApiProductTagController::class, 'active'])->name('active');
        Route::get('/featured', [ApiProductTagController::class, 'featured'])->name('featured');
        Route::get('/popular', [ApiProductTagController::class, 'popular'])->name('popular');
        Route::get('/recent', [ApiProductTagController::class, 'recent'])->name('recent');

        // Filter by attributes
        Route::get('/by-color/{color}', [ApiProductTagController::class, 'byColor'])->name('by-color');
        Route::get('/by-icon/{icon}', [ApiProductTagController::class, 'byIcon'])->name('by-icon');
        Route::get('/by-usage/{count}', [ApiProductTagController::class, 'byUsage'])->name('by-usage');

        // List methods
        Route::get('/names', [ApiProductTagController::class, 'getNames'])->name('names');
        Route::get('/slugs', [ApiProductTagController::class, 'getSlugs'])->name('slugs');
        Route::get('/colors', [ApiProductTagController::class, 'getColors'])->name('colors');
        Route::get('/icons', [ApiProductTagController::class, 'getIcons'])->name('icons');

        // Bulk operations
        Route::post('/bulk-create', [ApiProductTagController::class, 'bulkCreate'])->name('bulk-create');
        Route::put('/bulk-update', [ApiProductTagController::class, 'bulkUpdate'])->name('bulk-update');
        Route::delete('/bulk-delete', [ApiProductTagController::class, 'bulkDelete'])->name('bulk-delete');

        // Import/Export operations
        Route::post('/import', [ApiProductTagController::class, 'import'])->name('import');
        Route::get('/export', [ApiProductTagController::class, 'export'])->name('export');

        // Tag management
        Route::post('/sync/{product}', [ApiProductTagController::class, 'sync'])->name('sync');
        Route::post('/merge/{tag1}/{tag2}', [ApiProductTagController::class, 'merge'])->name('merge');
        Route::post('/split/{tag}', [ApiProductTagController::class, 'split'])->name('split');

        // Suggestions and autocomplete
        Route::get('/suggestions', [ApiProductTagController::class, 'suggestions'])->name('suggestions');
        Route::get('/autocomplete', [ApiProductTagController::class, 'autocomplete'])->name('autocomplete');

        // Relationships
        Route::get('/related/{tag}', [ApiProductTagController::class, 'related'])->name('related');
        Route::get('/synonyms/{tag}', [ApiProductTagController::class, 'synonyms'])->name('synonyms');
        Route::get('/hierarchy/{tag}', [ApiProductTagController::class, 'hierarchy'])->name('hierarchy');
        Route::get('/tree', [ApiProductTagController::class, 'tree'])->name('tree');
        Route::get('/cloud', [ApiProductTagController::class, 'cloud'])->name('cloud');
        Route::get('/stats', [ApiProductTagController::class, 'stats'])->name('stats');

        // Analytics
        Route::get('/analytics/{tag}', [ApiProductTagController::class, 'analytics'])->name('analytics');
        Route::get('/trends/{tag}', [ApiProductTagController::class, 'trends'])->name('trends');
        Route::get('/comparison/{tag1}/{tag2}', [ApiProductTagController::class, 'comparison'])->name('comparison');
        Route::get('/recommendations/{product}', [ApiProductTagController::class, 'recommendations'])->name('recommendations');
        Route::get('/forecast/{tag}', [ApiProductTagController::class, 'forecast'])->name('forecast');
        Route::get('/performance/{tag}', [ApiProductTagController::class, 'performance'])->name('performance');

        // Create product tag
        Route::post('/', [ApiProductTagController::class, 'store'])->name('store');

        // ProductTag-specific routes
        Route::prefix('{tag:slug}')->group(function () {
            // Show product tag
            Route::get('/', [ApiProductTagController::class, 'show'])->name('show');

            // Update product tag (full update)
            Route::put('/', [ApiProductTagController::class, 'update'])->name('update');

            // Update product tag (partial update)
            Route::patch('/', [ApiProductTagController::class, 'update'])->name('update.partial');

            // Delete product tag
            Route::delete('/', [ApiProductTagController::class, 'destroy'])->name('destroy');

            // Status management
            Route::post('/toggle-active', [ApiProductTagController::class, 'toggleActive'])->name('toggle-active');
            Route::post('/toggle-featured', [ApiProductTagController::class, 'toggleFeatured'])->name('toggle-featured');
        });
    });

    // ProductVariant API routes
    Route::prefix('product-variants')->name('product-variants.')->group(function () {
        // List product variants
        Route::get('/', [ApiProductVariantController::class, 'index'])->name('index');

        // Get product variant count
        Route::get('/count', [ApiProductVariantController::class, 'getCount'])->name('count');

        // Search product variants
        Route::get('/search', [ApiProductVariantController::class, 'search'])->name('search');

        // Filter by status
        Route::get('/active', [ApiProductVariantController::class, 'active'])->name('active');
        Route::get('/in-stock', [ApiProductVariantController::class, 'inStock'])->name('in-stock');
        Route::get('/out-of-stock', [ApiProductVariantController::class, 'outOfStock'])->name('out-of-stock');
        Route::get('/low-stock', [ApiProductVariantController::class, 'lowStock'])->name('low-stock');

        // Filter by relationships
        Route::get('/by-product/{product}', [ApiProductVariantController::class, 'byProduct'])->name('by-product');
        Route::get('/by-sku/{sku}', [ApiProductVariantController::class, 'bySku'])->name('by-sku');
        Route::get('/by-barcode/{barcode}', [ApiProductVariantController::class, 'byBarcode'])->name('by-barcode');

        // Filter by ranges
        Route::get('/by-price-range', [ApiProductVariantController::class, 'byPriceRange'])->name('by-price-range');
        Route::get('/by-stock-range', [ApiProductVariantController::class, 'byStockRange'])->name('by-stock-range');
        Route::get('/by-weight-range', [ApiProductVariantController::class, 'byWeightRange'])->name('by-weight-range');

        // List methods
        Route::get('/skus', [ApiProductVariantController::class, 'getSkus'])->name('skus');
        Route::get('/barcodes', [ApiProductVariantController::class, 'getBarcodes'])->name('barcodes');
        Route::get('/prices', [ApiProductVariantController::class, 'getPrices'])->name('prices');
        Route::get('/weights', [ApiProductVariantController::class, 'getWeights'])->name('weights');

        // Bulk operations
        Route::post('/bulk-create/{product}', [ApiProductVariantController::class, 'bulkCreate'])->name('bulk-create');
        Route::put('/bulk-update', [ApiProductVariantController::class, 'bulkUpdate'])->name('bulk-update');
        Route::delete('/bulk-delete', [ApiProductVariantController::class, 'bulkDelete'])->name('bulk-delete');

        // Import/Export operations
        Route::post('/import/{product}', [ApiProductVariantController::class, 'import'])->name('import');
        Route::get('/export/{product}', [ApiProductVariantController::class, 'export'])->name('export');
        Route::post('/sync/{product}', [ApiProductVariantController::class, 'sync'])->name('sync');

        // Create product variant
        Route::post('/', [ApiProductVariantController::class, 'store'])->name('store');

        // ProductVariant-specific routes
        Route::prefix('{variant}')->group(function () {
            // Show product variant
            Route::get('/', [ApiProductVariantController::class, 'show'])->name('show');

            // Update product variant (full update)
            Route::put('/', [ApiProductVariantController::class, 'update'])->name('update');

            // Update product variant (partial update)
            Route::patch('/', [ApiProductVariantController::class, 'update'])->name('update.partial');

            // Delete product variant
            Route::delete('/', [ApiProductVariantController::class, 'destroy'])->name('destroy');

            // Status management
            Route::post('/toggle-active', [ApiProductVariantController::class, 'toggleActive'])->name('toggle-active');
            Route::post('/toggle-featured', [ApiProductVariantController::class, 'toggleFeatured'])->name('toggle-featured');

            // Inventory management
            Route::post('/update-stock', [ApiProductVariantController::class, 'updateStock'])->name('update-stock');
            Route::post('/reserve-stock', [ApiProductVariantController::class, 'reserveStock'])->name('reserve-stock');
            Route::post('/release-stock', [ApiProductVariantController::class, 'releaseStock'])->name('release-stock');
            Route::post('/adjust-stock', [ApiProductVariantController::class, 'adjustStock'])->name('adjust-stock');

            // Pricing management
            Route::post('/set-price', [ApiProductVariantController::class, 'setPrice'])->name('set-price');
            Route::post('/set-sale-price', [ApiProductVariantController::class, 'setSalePrice'])->name('set-sale-price');
            Route::post('/set-compare-price', [ApiProductVariantController::class, 'setComparePrice'])->name('set-compare-price');

            // Inventory management
            Route::get('/inventory', [ApiProductVariantController::class, 'inventory'])->name('inventory');
            Route::get('/inventory-history', [ApiProductVariantController::class, 'inventoryHistory'])->name('inventory-history');
            Route::get('/inventory-alerts', [ApiProductVariantController::class, 'inventoryAlerts'])->name('inventory-alerts');

            // Analytics
            Route::get('/analytics', [ApiProductVariantController::class, 'analytics'])->name('analytics');
            Route::get('/sales', [ApiProductVariantController::class, 'sales'])->name('sales');
            Route::get('/revenue', [ApiProductVariantController::class, 'revenue'])->name('revenue');
            Route::get('/profit', [ApiProductVariantController::class, 'profit'])->name('profit');
            Route::get('/margin', [ApiProductVariantController::class, 'margin'])->name('margin');
        });
    });

    // Shipment API routes
    Route::prefix('shipments')->name('shipments.')->group(function () {
        // List shipments
        Route::get('/', [ApiShipmentController::class, 'index'])->name('index');

        // Get shipment count
        Route::get('/count', [ApiShipmentController::class, 'getCount'])->name('count');

        // Search shipments
        Route::get('/search', [ApiShipmentController::class, 'search'])->name('search');

        // Filter by status
        Route::get('/pending', [ApiShipmentController::class, 'pending'])->name('pending');
        Route::get('/in-transit', [ApiShipmentController::class, 'inTransit'])->name('in-transit');
        Route::get('/delivered', [ApiShipmentController::class, 'delivered'])->name('delivered');
        Route::get('/returned', [ApiShipmentController::class, 'returned'])->name('returned');
        Route::get('/overdue', [ApiShipmentController::class, 'overdue'])->name('overdue');
        Route::get('/delayed', [ApiShipmentController::class, 'delayed'])->name('delayed');
        Route::get('/on-time', [ApiShipmentController::class, 'onTime'])->name('on-time');

        // Filter by relationships
        Route::get('/by-order/{order}', [ApiShipmentController::class, 'byOrder'])->name('by-order');
        Route::get('/by-carrier/{carrier}', [ApiShipmentController::class, 'byCarrier'])->name('by-carrier');
        Route::get('/by-status/{status}', [ApiShipmentController::class, 'byStatus'])->name('by-status');
        Route::get('/by-tracking/{tracking}', [ApiShipmentController::class, 'byTracking'])->name('by-tracking');

        // List methods
        Route::get('/carriers', [ApiShipmentController::class, 'getCarriers'])->name('carriers');
        Route::get('/tracking-numbers', [ApiShipmentController::class, 'getTrackingNumbers'])->name('tracking-numbers');

        // Analytics
        Route::get('/delivery-performance', [ApiShipmentController::class, 'deliveryPerformance'])->name('delivery-performance');
        Route::get('/shipping-costs', [ApiShipmentController::class, 'shippingCosts'])->name('shipping-costs');
        Route::get('/delivery-times', [ApiShipmentController::class, 'deliveryTimes'])->name('delivery-times');
        Route::get('/return-rates', [ApiShipmentController::class, 'returnRates'])->name('return-rates');
        Route::get('/carrier-performance', [ApiShipmentController::class, 'carrierPerformance'])->name('carrier-performance');
        Route::get('/trends', [ApiShipmentController::class, 'trends'])->name('trends');
        Route::get('/forecast', [ApiShipmentController::class, 'forecast'])->name('forecast');

        // Tracking and labels
        Route::get('/tracking-info/{tracking}', [ApiShipmentController::class, 'trackingInfo'])->name('tracking-info');
        Route::get('/shipping-label/{shipment}', [ApiShipmentController::class, 'shippingLabel'])->name('shipping-label');
        Route::get('/return-label/{shipment}', [ApiShipmentController::class, 'returnLabel'])->name('return-label');

        // Pickup operations
        Route::post('/schedule-pickup/{shipment}', [ApiShipmentController::class, 'schedulePickup'])->name('schedule-pickup');
        Route::post('/cancel-pickup/{shipment}', [ApiShipmentController::class, 'cancelPickup'])->name('cancel-pickup');
        Route::get('/pickup-confirmation/{shipment}', [ApiShipmentController::class, 'pickupConfirmation'])->name('pickup-confirmation');

        // Create shipment
        Route::post('/', [ApiShipmentController::class, 'store'])->name('store');

        // Shipment-specific routes
        Route::prefix('{shipment}')->group(function () {
            // Show shipment
            Route::get('/', [ApiShipmentController::class, 'show'])->name('show');

            // Update shipment (full update)
            Route::put('/', [ApiShipmentController::class, 'update'])->name('update');

            // Update shipment (partial update)
            Route::patch('/', [ApiShipmentController::class, 'update'])->name('update.partial');

            // Delete shipment
            Route::delete('/', [ApiShipmentController::class, 'destroy'])->name('destroy');

            // Status management
            Route::post('/ship', [ApiShipmentController::class, 'ship'])->name('ship');
            Route::post('/deliver', [ApiShipmentController::class, 'deliver'])->name('deliver');
            Route::post('/return', [ApiShipmentController::class, 'return'])->name('return');
            Route::post('/update-tracking', [ApiShipmentController::class, 'updateTracking'])->name('update-tracking');
            Route::post('/update-status', [ApiShipmentController::class, 'updateStatus'])->name('update-status');

            // Analytics
            Route::get('/analytics', [ApiShipmentController::class, 'analytics'])->name('analytics');
            Route::get('/analytics-by-order', [ApiShipmentController::class, 'analyticsByOrder'])->name('analytics-by-order');
            Route::get('/analytics-by-carrier', [ApiShipmentController::class, 'analyticsByCarrier'])->name('analytics-by-carrier');

            // Shipment Item API routes
            Route::prefix('items')->name('items.')->group(function () {
                // List shipment items
                Route::get('/', [ApiShipmentItemController::class, 'index'])->name('index');

                // Get shipment items count
                Route::get('/count', [ApiShipmentItemController::class, 'getCount'])->name('count');

                // Search shipment items
                Route::get('/search', [ApiShipmentItemController::class, 'search'])->name('search');

                // Get shipment items summary
                Route::get('/summary', [ApiShipmentItemController::class, 'summary'])->name('summary');

                // Calculate shipment weight and volume
                Route::get('/weight', [ApiShipmentItemController::class, 'calculateWeight'])->name('weight');
                Route::get('/volume', [ApiShipmentItemController::class, 'calculateVolume'])->name('volume');

                // Create shipment item
                Route::post('/', [ApiShipmentItemController::class, 'store'])->name('store');

                // Bulk operations
                Route::post('/bulk-create', [ApiShipmentItemController::class, 'bulkCreate'])->name('bulk-create');
                Route::put('/bulk-update', [ApiShipmentItemController::class, 'bulkUpdate'])->name('bulk-update');
                Route::delete('/bulk-delete', [ApiShipmentItemController::class, 'bulkDelete'])->name('bulk-delete');

                // Filter by quantity range
                Route::get('/by-quantity-range', [ApiShipmentItemController::class, 'byQuantityRange'])->name('by-quantity-range');

                // Filter by shipping status
                Route::get('/fully-shipped', [ApiShipmentItemController::class, 'fullyShipped'])->name('fully-shipped');
                Route::get('/partially-shipped', [ApiShipmentItemController::class, 'partiallyShipped'])->name('partially-shipped');

                // Filter by product and variant
                Route::get('/by-product/{product}', [ApiShipmentItemController::class, 'byProduct'])->name('by-product');
                Route::get('/by-variant/{variant}', [ApiShipmentItemController::class, 'byVariant'])->name('by-variant');

                // Analytics
                Route::get('/analytics', [ApiShipmentItemController::class, 'analytics'])->name('analytics');
                Route::get('/top-shipped', [ApiShipmentItemController::class, 'topShipped'])->name('top-shipped');

                // Shipment item-specific routes
                Route::prefix('{item}')->group(function () {
                    // Show shipment item
                    Route::get('/', [ApiShipmentItemController::class, 'show'])->name('show');

                    // Update shipment item (full update)
                    Route::put('/', [ApiShipmentItemController::class, 'update'])->name('update');

                    // Update shipment item (partial update)
                    Route::patch('/', [ApiShipmentItemController::class, 'update'])->name('update.partial');

                    // Delete shipment item
                    Route::delete('/', [ApiShipmentItemController::class, 'destroy'])->name('destroy');

                    // Get shipment item status
                    Route::get('/status', [ApiShipmentItemController::class, 'getStatus'])->name('status');
                });
            });
        });
    });

    // Transaction API routes
    Route::prefix('transactions')->name('transactions.')->group(function () {
        // List transactions
        Route::get('/', [ApiTransactionController::class, 'index'])->name('index');

        // Get transaction count
        Route::get('/count', [ApiTransactionController::class, 'getCount'])->name('count');

        // Get transaction revenue
        Route::get('/revenue', [ApiTransactionController::class, 'getRevenue'])->name('revenue');

        // Search transactions
        Route::get('/search', [ApiTransactionController::class, 'search'])->name('search');

        // Get transaction statistics
        Route::get('/statistics', [ApiTransactionController::class, 'statistics'])->name('statistics');

        // Create transaction
        Route::post('/', [ApiTransactionController::class, 'store'])->name('store');

        // Filter by gateway
        Route::get('/by-gateway/{gateway}', [ApiTransactionController::class, 'getByGateway'])->name('by-gateway');

        // Filter by status
        Route::get('/by-status/{status}', [ApiTransactionController::class, 'getByStatus'])->name('by-status');

        // Transaction-specific routes
        Route::prefix('{transaction}')->group(function () {
            // Show transaction
            Route::get('/', [ApiTransactionController::class, 'show'])->name('show');

            // Update transaction (full update)
            Route::put('/', [ApiTransactionController::class, 'update'])->name('update');

            // Update transaction (partial update)
            Route::patch('/', [ApiTransactionController::class, 'update'])->name('update.partial');

            // Delete transaction
            Route::delete('/', [ApiTransactionController::class, 'destroy'])->name('destroy');

            // Transaction status management
            Route::post('/success', [ApiTransactionController::class, 'markAsSuccess'])->name('success');
            Route::post('/failed', [ApiTransactionController::class, 'markAsFailed'])->name('failed');
            Route::post('/refund', [ApiTransactionController::class, 'markAsRefunded'])->name('refund');
        });
    });
});
