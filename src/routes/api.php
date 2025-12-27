<?php

use App\Http\Controllers\Api\V1\ProviderPerformanceController;
use Fereydooni\Shopping\app\Http\Controllers\Api\EmployeeNoteController;
use Fereydooni\Shopping\app\Http\Controllers\Api\V1\AddressController as ApiAddressController;
use Fereydooni\Shopping\app\Http\Controllers\Api\V1\BrandController as ApiBrandController;
use Fereydooni\Shopping\app\Http\Controllers\Api\V1\CategoryController as ApiCategoryController;
use Fereydooni\Shopping\app\Http\Controllers\Api\V1\CustomerCommunicationController as ApiCustomerCommunicationController;
use Fereydooni\Shopping\app\Http\Controllers\Api\V1\CustomerController as ApiCustomerController;
use Fereydooni\Shopping\app\Http\Controllers\Api\V1\CustomerNoteController as ApiCustomerNoteController;
use Fereydooni\Shopping\app\Http\Controllers\Api\V1\CustomerPreferenceController as ApiCustomerPreferenceController;
use Fereydooni\Shopping\app\Http\Controllers\Api\V1\CustomerSegmentController as ApiCustomerSegmentController;
use Fereydooni\Shopping\app\Http\Controllers\Api\V1\EmployeeController as ApiEmployeeController;
use Fereydooni\Shopping\app\Http\Controllers\Api\V1\LoyaltyTransactionController as ApiLoyaltyTransactionController;
use Fereydooni\Shopping\app\Http\Controllers\Api\V1\OrderController as ApiOrderController;
use Fereydooni\Shopping\app\Http\Controllers\Api\V1\OrderItemController as ApiOrderItemController;
use Fereydooni\Shopping\app\Http\Controllers\Api\V1\OrderStatusHistoryController as ApiOrderStatusHistoryController;
use Fereydooni\Shopping\app\Http\Controllers\Api\V1\ProductAttributeController as ApiProductAttributeController;
use Fereydooni\Shopping\app\Http\Controllers\Api\V1\ProductAttributeValueController as ApiProductAttributeValueController;
use Fereydooni\Shopping\app\Http\Controllers\Api\V1\ProductController as ApiProductController;
use Fereydooni\Shopping\app\Http\Controllers\Api\V1\ProductDiscountController as ApiProductDiscountController;
use Fereydooni\Shopping\app\Http\Controllers\Api\V1\ProductMetaController as ApiProductMetaController;
use Fereydooni\Shopping\app\Http\Controllers\Api\V1\ProductReviewController as ApiProductReviewController;
use Fereydooni\Shopping\app\Http\Controllers\Api\V1\ProductTagController as ApiProductTagController;
use Fereydooni\Shopping\app\Http\Controllers\Api\V1\ProductVariantController as ApiProductVariantController;
use Fereydooni\Shopping\app\Http\Controllers\Api\V1\ProviderController as ApiProviderController;
use Fereydooni\Shopping\app\Http\Controllers\Api\V1\ProviderInsuranceController;
use Fereydooni\Shopping\app\Http\Controllers\Api\V1\ProviderLocationController;
use Fereydooni\Shopping\app\Http\Controllers\Api\V1\ShipmentController as ApiShipmentController;
use Fereydooni\Shopping\app\Http\Controllers\Api\V1\ShipmentItemController as ApiShipmentItemController;
use Fereydooni\Shopping\app\Http\Controllers\Api\V1\TransactionController as ApiTransactionController;
use Fereydooni\Shopping\app\Http\Controllers\Api\V1\UserController as ApiUserController;
use Fereydooni\Shopping\app\Http\Controllers\Api\V1\UserSubscriptionController as ApiUserSubscriptionController;
use Fereydooni\Shopping\app\Models\ProductTag;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Support\Facades\Route;

Route::prefix('api/v1/shopping')->name('api.v1.shopping.')->middleware(['auth:sanctum', 'throttle:60,1', 'setlocale', SubstituteBindings::class])->group(function () {
    // ProductTag API routes
    Route::prefix('product-tags')->name('product-tags.')->group(function () {
        // List product tags
        Route::get('/', [ApiProductTagController::class, 'index'])->name('index');
        Route::get('/cursor-all', [ApiProductTagController::class, 'cursorAll'])->name('cursor-all');

        // Delete product tag
        Route::delete('/destroy-some', [ApiProductTagController::class, 'destroySome'])->name('destroySome');
        Route::delete('/destroy-all', [ApiProductTagController::class, 'destroyAll'])->name('destroyAll');
        Route::delete('/{tag}', [ApiProductTagController::class, 'destroy'])->where('tag', '[0-9]+')->name('destroy');

        // Get product tag count
        Route::get('/lens', [ApiProductTagController::class, 'lens'])->name('lens');

        // // Get product tag count
        // Route::get('/count', [ApiProductTagController::class, 'getCount'])->name('count');

        // // Search product tags
        // Route::get('/search', [ApiProductTagController::class, 'search'])->name('search');

        // // Filter by status
        // Route::get('/active', [ApiProductTagController::class, 'active'])->name('active');
        // Route::get('/featured', [ApiProductTagController::class, 'featured'])->name('featured');
        // Route::get('/popular', [ApiProductTagController::class, 'popular'])->name('popular');
        // Route::get('/recent', [ApiProductTagController::class, 'recent'])->name('recent');

        // // Filter by attributes
        // Route::get('/by-color/{color}', [ApiProductTagController::class, 'byColor'])->name('by-color');
        // Route::get('/by-icon/{icon}', [ApiProductTagController::class, 'byIcon'])->name('by-icon');
        // Route::get('/by-usage/{count}', [ApiProductTagController::class, 'byUsage'])->name('by-usage');

        // // List methods
        // Route::get('/names', [ApiProductTagController::class, 'getNames'])->name('names');
        // Route::get('/slugs', [ApiProductTagController::class, 'getSlugs'])->name('slugs');
        // Route::get('/colors', [ApiProductTagController::class, 'getColors'])->name('colors');
        // Route::get('/icons', [ApiProductTagController::class, 'getIcons'])->name('icons');

        // // Bulk operations
        // Route::post('/bulk-create', [ApiProductTagController::class, 'bulkCreate'])->name('bulk-create');
        Route::put('/bulk-update', [ApiProductTagController::class, 'bulkUpdate'])->name('bulk-update');
        // Route::delete('/bulk-delete', [ApiProductTagController::class, 'bulkDelete'])->name('bulk-delete');

        // // Import/Export operations
        // Route::post('/import', [ApiProductTagController::class, 'import'])->name('import');
        // Route::get('/export', [ApiProductTagController::class, 'export'])->name('export');

        // // Tag management
        // Route::post('/sync/{product}', [ApiProductTagController::class, 'sync'])->name('sync');
        // Route::post('/merge/{tag1}/{tag2}', [ApiProductTagController::class, 'merge'])->name('merge');
        // Route::post('/split/{tag}', [ApiProductTagController::class, 'split'])->name('split');

        // // Suggestions and autocomplete
        // Route::get('/suggestions', [ApiProductTagController::class, 'suggestions'])->name('suggestions');
        // Route::get('/autocomplete', [ApiProductTagController::class, 'autocomplete'])->name('autocomplete');

        // // Relationships
        // Route::get('/related/{tag}', [ApiProductTagController::class, 'related'])->name('related');
        // Route::get('/synonyms/{tag}', [ApiProductTagController::class, 'synonyms'])->name('synonyms');
        // Route::get('/hierarchy/{tag}', [ApiProductTagController::class, 'hierarchy'])->name('hierarchy');
        // Route::get('/tree', [ApiProductTagController::class, 'tree'])->name('tree');
        // Route::get('/cloud', [ApiProductTagController::class, 'cloud'])->name('cloud');
        // Route::get('/stats', [ApiProductTagController::class, 'stats'])->name('stats');

        // // Analytics
        // Route::get('/analytics/{tag}', [ApiProductTagController::class, 'analytics'])->name('analytics');
        // Route::get('/trends/{tag}', [ApiProductTagController::class, 'trends'])->name('trends');
        // Route::get('/comparison/{tag1}/{tag2}', [ApiProductTagController::class, 'comparison'])->name('comparison');
        // Route::get('/recommendations/{product}', [ApiProductTagController::class, 'recommendations'])->name('recommendations');
        // Route::get('/forecast/{tag}', [ApiProductTagController::class, 'forecast'])->name('forecast');
        // Route::get('/performance/{tag}', [ApiProductTagController::class, 'performance'])->name('performance');

        // Create product tag
        Route::post('/', [ApiProductTagController::class, 'store'])->name('store');

        // // ProductTag-specific routes
        Route::prefix('{tag}')->group(function () {
            // Show product tag
            Route::get('/', [ApiProductTagController::class, 'show'])->name('show');

            // Update product tag (full update)
            Route::put('/', [ApiProductTagController::class, 'update'])->name('update');

            // Update product tag (partial update)
            Route::patch('/', [ApiProductTagController::class, 'update'])->name('update.partial');

            // Status management
            Route::put('/toggle-active', [ApiProductTagController::class, 'toggleActive'])->name('toggle-active');
            Route::put('/toggle-featured', [ApiProductTagController::class, 'toggleFeatured'])->name('toggle-featured');
        });
    });

    // Category API routes
    Route::prefix('categories')->name('categories.')->group(function () {
        // List categories
        Route::get('/', [ApiCategoryController::class, 'index'])->name('index');

        Route::get('/cursor-all', [ApiCategoryController::class, 'cursorAll'])->name('cursor-all');

        Route::get('/statuses/cursor-all', [ApiCategoryController::class, 'statuses'])->name('statuses');

        Route::delete('/destroy-some', [ApiCategoryController::class, 'destroySome'])->name('destroySome');
        Route::delete('/destroy-all', [ApiCategoryController::class, 'destroyAll'])->name('destroyAll');

        // // Get category count
        // Route::get('/count', [ApiCategoryController::class, 'getCount'])->name('count');

        // // Get root categories
        // Route::get('/root', [ApiCategoryController::class, 'getRoot'])->name('root');

        // // Get category tree
        // Route::get('/tree', [ApiCategoryController::class, 'tree'])->name('tree');

        // // Search categories
        // Route::get('/search', [ApiCategoryController::class, 'search'])->name('search');

        // // Get category statistics
        // Route::get('/stats', [ApiCategoryController::class, 'getStats'])->name('stats');

        // // Create category
        Route::post('/', [ApiCategoryController::class, 'store'])->name('store');

        // // Reorder categories
        // Route::post('/reorder', [ApiCategoryController::class, 'reorder'])->name('reorder');

        // Category-specific routes
        Route::prefix('{category}')->group(function () {
            // Show category
            Route::get('/', [ApiCategoryController::class, 'show'])->name('show');

            // Update category (full update)
            Route::put('/', [ApiCategoryController::class, 'update'])->name('update');

            // // Update category (partial update)
            // Route::patch('/', [ApiCategoryController::class, 'update'])->name('update.partial');

            // Delete category
            Route::delete('/', [ApiCategoryController::class, 'destroy'])->name('destroy');

            // // Set as default
            // Route::post('/default', [ApiCategoryController::class, 'setDefault'])->name('set-default');

            // // Move category
            // Route::post('/move', [ApiCategoryController::class, 'move'])->name('move');

            // // Get category children
            // Route::get('/children', [ApiCategoryController::class, 'getChildren'])->name('children');

            // // Get category ancestors
            // Route::get('/ancestors', [ApiCategoryController::class, 'getAncestors'])->name('ancestors');

            // // Get category descendants
            // Route::get('/descendants', [ApiCategoryController::class, 'getDescendants'])->name('descendants');
        });
    });

    // Brand API routes
    Route::prefix('brands')->name('brands.')->group(function () {
        // List brands
        Route::get('/', [ApiBrandController::class, 'index'])->name('index');

        Route::get('/cursor-all', [ApiBrandController::class, 'cursorAll'])->name('cursor-all');

        // Get brand's status list
        Route::get('/statuses/cursor-all', [ApiBrandController::class, 'statuses'])->name('statuses');

        // Create brand
        Route::post('/', [ApiBrandController::class, 'store'])->name('store');

        Route::delete('/destroy-some', [ApiBrandController::class, 'destroySome'])->name('destroySome');
        Route::delete('/destroy-all', [ApiBrandController::class, 'destroyAll'])->name('destroyAll');

        //     // Get brand count
        //     Route::get('/count', [ApiBrandController::class, 'getCount'])->name('count');

        //     // Search brands
        //     Route::get('/search', [ApiBrandController::class, 'search'])->name('search');

        //     // Get active brands
        //     Route::get('/active', [ApiBrandController::class, 'active'])->name('active');

        //     // Get featured brands
        //     Route::get('/featured', [ApiBrandController::class, 'featured'])->name('featured');

        //     // Get popular brands
        //     Route::get('/popular', [ApiBrandController::class, 'popular'])->name('popular');

        //     // Get brands by first letter
        //     Route::get('/alphabetical/{letter}', [ApiBrandController::class, 'alphabetical'])->name('alphabetical');

        //     // Get brands with products
        //     Route::get('/with-products', [ApiBrandController::class, 'getWithProducts'])->name('with-products');

        // Brand-specific routes
        Route::prefix('{brand}')->group(function () {
            // Show brand
            Route::get('/', [ApiBrandController::class, 'show'])->name('show');

            // Update brand (full update)
            Route::put('/', [ApiBrandController::class, 'update'])->name('update');

            // Delete brand
            Route::delete('/', [ApiBrandController::class, 'destroy'])->name('destroy');

            //         // Update brand (partial update)
            //         Route::patch('/', [ApiBrandController::class, 'update'])->name('update.partial');

            //         // Toggle active status
            //         Route::post('/toggle-active', [ApiBrandController::class, 'toggleActive'])->name('toggle-active');

            //         // Toggle featured status
            //         Route::post('/toggle-featured', [ApiBrandController::class, 'toggleFeatured'])->name('toggle-featured');

            //         // Upload brand media
            //         Route::post('/media', [ApiBrandController::class, 'uploadMedia'])->name('media.upload');

            // Delete brand media
            Route::delete('/media/{media}', [ApiBrandController::class, 'deleteMedia'])->name('media.delete');
        });
    });

    // Product API routes
    Route::prefix('products')->name('products.')->group(function () {
        // List products
        Route::get('/', [ApiProductController::class, 'index'])->name('index');

        Route::get('/statuses/cursor-all', [ApiProductController::class, 'statuses'])->name('statuses');
        Route::get('/product-types/cursor-all', [ApiProductController::class, 'productTypes'])->name('product-types');

        // Create product
        Route::post('/', [ApiProductController::class, 'store'])->name('store');

        //     // Get product count
        //     Route::get('/count', [ApiProductController::class, 'getCount'])->name('count');

        //     // Get product analytics
        //     Route::get('/analytics', [ApiProductController::class, 'getAnalytics'])->name('analytics');

        //     // Search products
        //     Route::get('/search', [ApiProductController::class, 'search'])->name('search');

        //     // Filter by status
        //     Route::get('/active', [ApiProductController::class, 'active'])->name('active');
        //     Route::get('/featured', [ApiProductController::class, 'featured'])->name('featured');
        //     Route::get('/in-stock', [ApiProductController::class, 'inStock'])->name('in-stock');
        //     Route::get('/low-stock', [ApiProductController::class, 'lowStock'])->name('low-stock');
        //     Route::get('/out-of-stock', [ApiProductController::class, 'outOfStock'])->name('out-of-stock');

        //     // Analytics and reporting
        //     Route::get('/top-selling', [ApiProductController::class, 'topSelling'])->name('top-selling');
        //     Route::get('/most-viewed', [ApiProductController::class, 'mostViewed'])->name('most-viewed');
        //     Route::get('/best-rated', [ApiProductController::class, 'bestRated'])->name('best-rated');
        //     Route::get('/new-arrivals', [ApiProductController::class, 'newArrivals'])->name('new-arrivals');
        //     Route::get('/on-sale', [ApiProductController::class, 'onSale'])->name('on-sale');

        //     // Filter by relationship
        //     Route::get('/by-category/{category}', [ApiProductController::class, 'byCategory'])->name('by-category');
        //     Route::get('/by-brand/{brand}', [ApiProductController::class, 'byBrand'])->name('by-brand');
        //     Route::get('/related/{product}', [ApiProductController::class, 'related'])->name('related');

        //     // Bulk operations
        //     Route::post('/bulk-operations', [ApiProductController::class, 'bulkOperations'])->name('bulk-operations');

        //     // Inventory management
        //     Route::get('/inventory/{product}', [ApiProductController::class, 'getInventoryLevel'])->name('inventory');
        //     Route::post('/inventory/update', [ApiProductController::class, 'updateInventory'])->name('update-inventory');

        // Product-specific routes
        Route::prefix('{product}')->group(function () {
            // Show product
            Route::get('/', [ApiProductController::class, 'show'])->name('show');

            // Update product (full update)
            Route::put('/', [ApiProductController::class, 'update'])->name('update');

            //         // Update product (partial update)
            //         Route::patch('/', [ApiProductController::class, 'update'])->name('update.partial');

            //         // Delete product
            //         Route::delete('/', [ApiProductController::class, 'destroy'])->name('destroy');

            //         // Status management
            //         Route::post('/toggle-active', [ApiProductController::class, 'toggleActive'])->name('toggle-active');
            //         Route::post('/toggle-featured', [ApiProductController::class, 'toggleFeatured'])->name('toggle-featured');
            //         Route::post('/publish', [ApiProductController::class, 'publish'])->name('publish');
            //         Route::post('/unpublish', [ApiProductController::class, 'unpublish'])->name('unpublish');
            //         Route::post('/archive', [ApiProductController::class, 'archive'])->name('archive');

            //         // Media management
            //         Route::post('/media', [ApiProductController::class, 'uploadMedia'])->name('upload-media');
            //         Route::delete('/media/{media}', [ApiProductController::class, 'deleteMedia'])->name('delete-media');

            //         // Product operations
            //         Route::post('/duplicate', [ApiProductController::class, 'duplicate'])->name('duplicate');
        });
    });

    // ProductAttribute API routes
    Route::prefix('product-attributes')->name('product-attributes.')->group(function () {
        // List product attributes
        Route::get('/', [ApiProductAttributeController::class, 'index'])->name('index');

        // Get product attribute types
        Route::get('/types/cursor-all', [ApiProductAttributeController::class, 'types'])->name('types');

        Route::get('/input-types/cursor-all', [ApiProductAttributeController::class, 'inputTypes'])->name('input-types');

        // Get product attribute input-types
        Route::post('/', [ApiProductAttributeController::class, 'store'])->name('store');

        Route::delete('/destroy-some', [ApiProductAttributeController::class, 'destroySome'])->name('destroySome');
        Route::delete('/destroy-all', [ApiProductAttributeController::class, 'destroyAll'])->name('destroyAll');

        //     // Get attribute count
        //     Route::get('/count', [ApiProductAttributeController::class, 'getCount'])->name('count');

        //     // Get attribute groups
        //     Route::get('/groups', [ApiProductAttributeController::class, 'getGroups'])->name('groups');

        //     // Get attribute types
        //     Route::get('/types', [ApiProductAttributeController::class, 'getTypes'])->name('types');

        //     // Get input types
        //     Route::get('/input-types', [ApiProductAttributeController::class, 'getInputTypes'])->name('input-types');

        //     // Search product attributes
        //     Route::get('/search', [ApiProductAttributeController::class, 'search'])->name('search');

        //     // Filter by functionality
        //     Route::get('/required', [ApiProductAttributeController::class, 'required'])->name('required');
        //     Route::get('/searchable', [ApiProductAttributeController::class, 'searchable'])->name('searchable');
        //     Route::get('/filterable', [ApiProductAttributeController::class, 'filterable'])->name('filterable');
        //     Route::get('/comparable', [ApiProductAttributeController::class, 'comparable'])->name('comparable');
        //     Route::get('/visible', [ApiProductAttributeController::class, 'visible'])->name('visible');
        //     Route::get('/system', [ApiProductAttributeController::class, 'system'])->name('system');
        //     Route::get('/custom', [ApiProductAttributeController::class, 'custom'])->name('custom');

        //     // Filter by type/group/input type
        //     Route::get('/by-type/{type}', [ApiProductAttributeController::class, 'byType'])->name('by-type');
        //     Route::get('/by-group/{group}', [ApiProductAttributeController::class, 'byGroup'])->name('by-group');
        //     Route::get('/by-input-type/{inputType}', [ApiProductAttributeController::class, 'byInputType'])->name('by-input-type');

        // ProductAttribute-specific routes
        Route::prefix('{productAttribute}')->group(function () {
            // Show product attribute
            Route::get('/', [ApiProductAttributeController::class, 'show'])->name('show');

            // Update product attribute (full update)
            Route::put('/', [ApiProductAttributeController::class, 'update'])->name('update');

            //         // Update product attribute (partial update)
            //         Route::patch('/', [ApiProductAttributeController::class, 'update'])->name('update.partial');

            // Delete product attribute
            Route::delete('/', [ApiProductAttributeController::class, 'destroy'])->name('destroy');

            //         // Toggle operations
            //         Route::post('/toggle-active', [ApiProductAttributeController::class, 'toggleActive'])->name('toggle-active');
            //         Route::post('/toggle-required', [ApiProductAttributeController::class, 'toggleRequired'])->name('toggle-required');
            //         Route::post('/toggle-searchable', [ApiProductAttributeController::class, 'toggleSearchable'])->name('toggle-searchable');
            //         Route::post('/toggle-filterable', [ApiProductAttributeController::class, 'toggleFilterable'])->name('toggle-filterable');
            //         Route::post('/toggle-comparable', [ApiProductAttributeController::class, 'toggleComparable'])->name('toggle-comparable');
            //         Route::post('/toggle-visible', [ApiProductAttributeController::class, 'toggleVisible'])->name('toggle-visible');

            //         // Analytics
            //         Route::get('/analytics', [ApiProductAttributeController::class, 'getAnalytics'])->name('analytics');
            //         Route::get('/usage', [ApiProductAttributeController::class, 'getUsage'])->name('usage');

            //         // Attribute values
            //         Route::prefix('values')->name('values.')->group(function () {
            //             Route::get('/', [ApiProductAttributeController::class, 'getValues'])->name('index');
            //             Route::post('/', [ApiProductAttributeController::class, 'addValue'])->name('store');
            //             Route::put('/{value}', [ApiProductAttributeController::class, 'updateValue'])->name('update');
            //             Route::delete('/{value}', [ApiProductAttributeController::class, 'deleteValue'])->name('destroy');
            //         });
        });
    });

    // // Address API routes
    // Route::prefix('addresses')->name('addresses.')->group(function () {
    //     // List addresses
    //     Route::get('/', [ApiAddressController::class, 'index'])->name('index');

    //     // Get address count
    //     Route::get('/count', [ApiAddressController::class, 'getCount'])->name('count');

    //     // Get default address by type
    //     Route::get('/default/{type}', [ApiAddressController::class, 'getDefault'])->name('default');

    //     // Search addresses
    //     Route::get('/search', [ApiAddressController::class, 'search'])->name('search');

    //     // Create address
    //     Route::post('/', [ApiAddressController::class, 'store'])->name('store');

    //     // Address-specific routes
    //     Route::prefix('{address}')->group(function () {
    //         // Show address
    //         Route::get('/', [ApiAddressController::class, 'show'])->name('show');

    //         // Update address (full update)
    //         Route::put('/', [ApiAddressController::class, 'update'])->name('update');

    //         // Update address (partial update)
    //         Route::patch('/', [ApiAddressController::class, 'update'])->name('update.partial');

    //         // Delete address
    //         Route::delete('/', [ApiAddressController::class, 'destroy'])->name('destroy');

    //         // Set as default
    //         Route::post('/default', [ApiAddressController::class, 'setDefault'])->name('set-default');
    //     });
    // });

    // // Order API routes
    // Route::prefix('orders')->name('orders.')->group(function () {
    //     // List orders
    //     Route::get('/', [ApiOrderController::class, 'index'])->name('index');

    //     // Get order count
    //     Route::get('/count', [ApiOrderController::class, 'getCount'])->name('count');

    //     // Get total revenue
    //     Route::get('/revenue', [ApiOrderController::class, 'getRevenue'])->name('revenue');

    //     // Search orders
    //     Route::get('/search', [ApiOrderController::class, 'search'])->name('search');

    //     // Get orders by status
    //     Route::get('/pending', [ApiOrderController::class, 'pending'])->name('pending');
    //     Route::get('/shipped', [ApiOrderController::class, 'shipped'])->name('shipped');
    //     Route::get('/completed', [ApiOrderController::class, 'completed'])->name('completed');
    //     Route::get('/cancelled', [ApiOrderController::class, 'cancelled'])->name('cancelled');

    //     // Create order
    //     Route::post('/', [ApiOrderController::class, 'store'])->name('store');

    //     // Order-specific routes
    //     Route::prefix('{order}')->group(function () {
    //         // Show order
    //         Route::get('/', [ApiOrderController::class, 'show'])->name('show');

    //         // Update order (full update)
    //         Route::put('/', [ApiOrderController::class, 'update'])->name('update');

    //         // Update order (partial update)
    //         Route::patch('/', [ApiOrderController::class, 'update'])->name('update.partial');

    //         // Delete order
    //         Route::delete('/', [ApiOrderController::class, 'destroy'])->name('destroy');

    //         // Order status management
    //         Route::post('/cancel', [ApiOrderController::class, 'cancel'])->name('cancel');
    //         Route::post('/mark-paid', [ApiOrderController::class, 'markPaid'])->name('mark-paid');
    //         Route::post('/mark-shipped', [ApiOrderController::class, 'markShipped'])->name('mark-shipped');
    //         Route::post('/mark-completed', [ApiOrderController::class, 'markCompleted'])->name('mark-completed');

    //         // Order notes
    //         Route::get('/notes', [ApiOrderController::class, 'getNotes'])->name('notes');
    //         Route::post('/notes', [ApiOrderController::class, 'addNote'])->name('add-note');

    //         // Process refund
    //         Route::post('/refund', [ApiOrderController::class, 'processRefund'])->name('refund');
    //     });
    // });

    // // OrderItem API routes
    // Route::prefix('order-items')->name('order-items.')->group(function () {
    //     // List order items
    //     Route::get('/', [ApiOrderItemController::class, 'index'])->name('index');

    //     // Get order item count
    //     Route::get('/count', [ApiOrderItemController::class, 'getCount'])->name('count');

    //     // Get total revenue
    //     Route::get('/revenue', [ApiOrderItemController::class, 'getRevenue'])->name('revenue');

    //     // Search order items
    //     Route::get('/search', [ApiOrderItemController::class, 'search'])->name('search');

    //     // Get items by status
    //     Route::get('/shipped', [ApiOrderItemController::class, 'shipped'])->name('shipped');
    //     Route::get('/unshipped', [ApiOrderItemController::class, 'unshipped'])->name('unshipped');

    //     // Analytics
    //     Route::get('/top-selling', [ApiOrderItemController::class, 'topSelling'])->name('top-selling');
    //     Route::get('/low-stock', [ApiOrderItemController::class, 'lowStock'])->name('low-stock');

    //     // Get items by order/product
    //     Route::get('/by-order/{order}', [ApiOrderItemController::class, 'byOrder'])->name('by-order');
    //     Route::get('/by-product/{product}', [ApiOrderItemController::class, 'byProduct'])->name('by-product');

    //     // Inventory management
    //     Route::get('/inventory/{product}', [ApiOrderItemController::class, 'getInventoryLevel'])->name('inventory.level');
    //     Route::post('/inventory/reserve', [ApiOrderItemController::class, 'reserveInventory'])->name('inventory.reserve');
    //     Route::post('/inventory/release', [ApiOrderItemController::class, 'releaseInventory'])->name('inventory.release');

    //     // Create order item
    //     Route::post('/', [ApiOrderItemController::class, 'store'])->name('store');

    //     // OrderItem-specific routes
    //     Route::prefix('{orderItem}')->group(function () {
    //         // Show order item
    //         Route::get('/', [ApiOrderItemController::class, 'show'])->name('show');

    //         // Update order item (full update)
    //         Route::put('/', [ApiOrderItemController::class, 'update'])->name('update');

    //         // Update order item (partial update)
    //         Route::patch('/', [ApiOrderItemController::class, 'update'])->name('update.partial');

    //         // Delete order item
    //         Route::delete('/', [ApiOrderItemController::class, 'destroy'])->name('destroy');

    //         // Shipping operations
    //         Route::post('/mark-shipped', [ApiOrderItemController::class, 'markShipped'])->name('mark-shipped');
    //         Route::post('/mark-returned', [ApiOrderItemController::class, 'markReturned'])->name('mark-returned');
    //         Route::post('/process-refund', [ApiOrderItemController::class, 'processRefund'])->name('process-refund');

    //         // Get item status
    //         Route::get('/status', [ApiOrderItemController::class, 'getStatus'])->name('status');
    //     });
    // });

    // // OrderStatusHistory API routes
    // Route::prefix('order-status-history')->name('order-status-history.')->group(function () {
    //     // List status history
    //     Route::get('/', [ApiOrderStatusHistoryController::class, 'index'])->name('index');

    //     // Get history count
    //     Route::get('/count', [ApiOrderStatusHistoryController::class, 'getCount'])->name('count');

    //     // Search status history
    //     Route::get('/search', [ApiOrderStatusHistoryController::class, 'search'])->name('search');

    //     // Get status change frequency
    //     Route::get('/frequency', [ApiOrderStatusHistoryController::class, 'getFrequency'])->name('frequency');

    //     // Get history by order/user/status
    //     Route::get('/by-order/{order}', [ApiOrderStatusHistoryController::class, 'byOrder'])->name('by-order');
    //     Route::get('/by-user/{user}', [ApiOrderStatusHistoryController::class, 'byUser'])->name('by-user');
    //     Route::get('/by-status/{status}', [ApiOrderStatusHistoryController::class, 'byStatus'])->name('by-status');

    //     // Get order timeline
    //     Route::get('/timeline/{order}', [ApiOrderStatusHistoryController::class, 'timeline'])->name('timeline');

    //     // Analytics and reports
    //     Route::get('/analytics', [ApiOrderStatusHistoryController::class, 'analytics'])->name('analytics');
    //     Route::get('/reports', [ApiOrderStatusHistoryController::class, 'reports'])->name('reports');

    //     // Create status history
    //     Route::post('/', [ApiOrderStatusHistoryController::class, 'store'])->name('store');

    //     // StatusHistory-specific routes
    //     Route::prefix('{history}')->group(function () {
    //         // Show status history
    //         Route::get('/', [ApiOrderStatusHistoryController::class, 'show'])->name('show');

    //         // Update status history (full update)
    //         Route::put('/', [ApiOrderStatusHistoryController::class, 'update'])->name('update');

    //         // Update status history (partial update)
    //         Route::patch('/', [ApiOrderStatusHistoryController::class, 'update'])->name('update.partial');

    //         // Delete status history
    //         Route::delete('/', [ApiOrderStatusHistoryController::class, 'destroy'])->name('destroy');
    //     });
    // });

    // // ProductAttributeValue API routes
    // Route::prefix('product-attribute-values')->name('product-attribute-values.')->group(function () {
    //     // List product attribute values
    //     Route::get('/', [ApiProductAttributeValueController::class, 'index'])->name('index');

    //     // Get value count
    //     Route::get('/count', [ApiProductAttributeValueController::class, 'getCount'])->name('count');

    //     // Search product attribute values
    //     Route::get('/search', [ApiProductAttributeValueController::class, 'search'])->name('search');

    //     // Filter by status
    //     Route::get('/active', [ApiProductAttributeValueController::class, 'active'])->name('active');
    //     Route::get('/default', [ApiProductAttributeValueController::class, 'default'])->name('default');

    //     // Usage analytics
    //     Route::get('/most-used', [ApiProductAttributeValueController::class, 'mostUsed'])->name('most-used');
    //     Route::get('/least-used', [ApiProductAttributeValueController::class, 'leastUsed'])->name('least-used');
    //     Route::get('/unused', [ApiProductAttributeValueController::class, 'unused'])->name('unused');

    //     // Filter by relationship
    //     Route::get('/by-attribute/{attribute}', [ApiProductAttributeValueController::class, 'byAttribute'])->name('by-attribute');
    //     Route::get('/by-variant/{variant}', [ApiProductAttributeValueController::class, 'byVariant'])->name('by-variant');
    //     Route::get('/by-product/{product}', [ApiProductAttributeValueController::class, 'byProduct'])->name('by-product');
    //     Route::get('/by-category/{category}', [ApiProductAttributeValueController::class, 'byCategory'])->name('by-category');
    //     Route::get('/by-brand/{brand}', [ApiProductAttributeValueController::class, 'byBrand'])->name('by-brand');

    //     // Create product attribute value
    //     Route::post('/', [ApiProductAttributeValueController::class, 'store'])->name('store');

    //     // ProductAttributeValue-specific routes
    //     Route::prefix('{value}')->group(function () {
    //         // Show product attribute value
    //         Route::get('/', [ApiProductAttributeValueController::class, 'show'])->name('show');

    //         // Update product attribute value (full update)
    //         Route::put('/', [ApiProductAttributeValueController::class, 'update'])->name('update');

    //         // Update product attribute value (partial update)
    //         Route::patch('/', [ApiProductAttributeValueController::class, 'update'])->name('update.partial');

    //         // Delete product attribute value
    //         Route::delete('/', [ApiProductAttributeValueController::class, 'destroy'])->name('destroy');

    //         // Status management
    //         Route::post('/toggle-active', [ApiProductAttributeValueController::class, 'toggleActive'])->name('toggle-active');
    //         Route::post('/toggle-default', [ApiProductAttributeValueController::class, 'toggleDefault'])->name('toggle-default');
    //         Route::post('/set-default', [ApiProductAttributeValueController::class, 'setDefault'])->name('set-default');

    //         // Usage and analytics
    //         Route::get('/usage', [ApiProductAttributeValueController::class, 'getUsage'])->name('usage');
    //         Route::get('/analytics', [ApiProductAttributeValueController::class, 'getAnalytics'])->name('analytics');

    //         // Relationship management
    //         Route::post('/assign-variant/{variant}', [ApiProductAttributeValueController::class, 'assignToVariant'])->name('assign-variant');
    //         Route::delete('/remove-variant/{variant}', [ApiProductAttributeValueController::class, 'removeFromVariant'])->name('remove-variant');
    //         Route::post('/assign-product/{product}', [ApiProductAttributeValueController::class, 'assignToProduct'])->name('assign-product');
    //         Route::delete('/remove-product/{product}', [ApiProductAttributeValueController::class, 'removeFromProduct'])->name('remove-product');
    //         Route::post('/assign-category/{category}', [ApiProductAttributeValueController::class, 'assignToCategory'])->name('assign-category');
    //         Route::delete('/remove-category/{category}', [ApiProductAttributeValueController::class, 'removeFromCategory'])->name('remove-category');
    //         Route::post('/assign-brand/{brand}', [ApiProductAttributeValueController::class, 'assignToBrand'])->name('assign-brand');
    //         Route::delete('/remove-brand/{brand}', [ApiProductAttributeValueController::class, 'removeFromBrand'])->name('remove-brand');
    //     });
    // });

    // // ProductDiscount API routes
    // Route::prefix('product-discounts')->name('product-discounts.')->group(function () {
    //     // List product discounts
    //     Route::get('/', [ApiProductDiscountController::class, 'index'])->name('index');

    //     // Get discount count
    //     Route::get('/count', [ApiProductDiscountController::class, 'getCount'])->name('count');

    //     // Search product discounts
    //     Route::get('/search', [ApiProductDiscountController::class, 'search'])->name('search');

    //     // Filter by status
    //     Route::get('/active', [ApiProductDiscountController::class, 'active'])->name('active');
    //     Route::get('/expired', [ApiProductDiscountController::class, 'expired'])->name('expired');
    //     Route::get('/upcoming', [ApiProductDiscountController::class, 'upcoming'])->name('upcoming');
    //     Route::get('/current', [ApiProductDiscountController::class, 'current'])->name('current');

    //     // Filter by relationship
    //     Route::get('/by-product/{product}', [ApiProductDiscountController::class, 'byProduct'])->name('by-product');
    //     Route::get('/by-type/{type}', [ApiProductDiscountController::class, 'byType'])->name('by-type');

    //     // Get best discount for product
    //     Route::get('/best/{product}', [ApiProductDiscountController::class, 'getBest'])->name('best');

    //     // Create product discount
    //     Route::post('/', [ApiProductDiscountController::class, 'store'])->name('store');

    //     // ProductDiscount-specific routes
    //     Route::prefix('{discount}')->group(function () {
    //         // Show product discount
    //         Route::get('/', [ApiProductDiscountController::class, 'show'])->name('show');

    //         // Update product discount (full update)
    //         Route::put('/', [ApiProductDiscountController::class, 'update'])->name('update');

    //         // Update product discount (partial update)
    //         Route::patch('/', [ApiProductDiscountController::class, 'update'])->name('update.partial');

    //         // Delete product discount
    //         Route::delete('/', [ApiProductDiscountController::class, 'destroy'])->name('destroy');

    //         // Status management
    //         Route::post('/toggle-active', [ApiProductDiscountController::class, 'toggleActive'])->name('toggle-active');
    //         Route::post('/extend', [ApiProductDiscountController::class, 'extend'])->name('extend');
    //         Route::post('/shorten', [ApiProductDiscountController::class, 'shorten'])->name('shorten');

    //         // Calculation and application
    //         Route::post('/calculate', [ApiProductDiscountController::class, 'calculate'])->name('calculate');
    //         Route::post('/apply', [ApiProductDiscountController::class, 'apply'])->name('apply');
    //         Route::post('/validate', [ApiProductDiscountController::class, 'validate'])->name('validate');

    //         // Analytics and reporting
    //         Route::get('/analytics', [ApiProductDiscountController::class, 'analytics'])->name('analytics');
    //         Route::get('/performance', [ApiProductDiscountController::class, 'performance'])->name('performance');
    //         Route::get('/forecast', [ApiProductDiscountController::class, 'forecast'])->name('forecast');
    //     });

    //     // Recommendations
    //     Route::get('/recommendations/{product}', [ApiProductDiscountController::class, 'recommendations'])->name('recommendations');
    // });

    // // ProductMeta API routes
    // Route::prefix('product-meta')->name('product-meta.')->group(function () {
    //     // List product meta
    //     Route::get('/', [ApiProductMetaController::class, 'index'])->name('index');

    //     // Get product meta count
    //     Route::get('/count', [ApiProductMetaController::class, 'getCount'])->name('count');

    //     // Search product meta
    //     Route::get('/search', [ApiProductMetaController::class, 'search'])->name('search');

    //     // Filter by visibility
    //     Route::get('/public', [ApiProductMetaController::class, 'public'])->name('public');
    //     Route::get('/private', [ApiProductMetaController::class, 'private'])->name('private');
    //     Route::get('/searchable', [ApiProductMetaController::class, 'searchable'])->name('searchable');
    //     Route::get('/filterable', [ApiProductMetaController::class, 'filterable'])->name('filterable');

    //     // Filter by relationship
    //     Route::get('/by-product/{product}', [ApiProductMetaController::class, 'byProduct'])->name('by-product');
    //     Route::get('/by-key/{key}', [ApiProductMetaController::class, 'byKey'])->name('by-key');
    //     Route::get('/by-type/{type}', [ApiProductMetaController::class, 'byType'])->name('by-type');

    //     // Analytics and reporting
    //     Route::get('/keys', [ApiProductMetaController::class, 'getKeys'])->name('keys');
    //     Route::get('/types', [ApiProductMetaController::class, 'getTypes'])->name('types');
    //     Route::get('/values/{key}', [ApiProductMetaController::class, 'getValuesByKey'])->name('values-by-key');

    //     // Bulk operations
    //     Route::post('/bulk-create/{product}', [ApiProductMetaController::class, 'bulkCreate'])->name('bulk-create');
    //     Route::put('/bulk-update/{product}', [ApiProductMetaController::class, 'bulkUpdate'])->name('bulk-update');
    //     Route::delete('/bulk-delete/{product}', [ApiProductMetaController::class, 'bulkDelete'])->name('bulk-delete');

    //     // Import/Export operations
    //     Route::post('/import/{product}', [ApiProductMetaController::class, 'import'])->name('import');
    //     Route::get('/export/{product}', [ApiProductMetaController::class, 'export'])->name('export');
    //     Route::post('/sync/{product}', [ApiProductMetaController::class, 'sync'])->name('sync');

    //     // Analytics
    //     Route::get('/analytics/{key}', [ApiProductMetaController::class, 'analytics'])->name('analytics');

    //     // Create product meta
    //     Route::post('/', [ApiProductMetaController::class, 'store'])->name('store');

    //     // ProductMeta-specific routes
    //     Route::prefix('{meta}')->group(function () {
    //         // Show product meta
    //         Route::get('/', [ApiProductMetaController::class, 'show'])->name('show');

    //         // Update product meta (full update)
    //         Route::put('/', [ApiProductMetaController::class, 'update'])->name('update');

    //         // Update product meta (partial update)
    //         Route::patch('/', [ApiProductMetaController::class, 'update'])->name('update.partial');

    //         // Delete product meta
    //         Route::delete('/', [ApiProductMetaController::class, 'destroy'])->name('destroy');

    //         // Status management
    //         Route::post('/toggle-public', [ApiProductMetaController::class, 'togglePublic'])->name('toggle-public');
    //         Route::post('/toggle-searchable', [ApiProductMetaController::class, 'toggleSearchable'])->name('toggle-searchable');
    //         Route::post('/toggle-filterable', [ApiProductMetaController::class, 'toggleFilterable'])->name('toggle-filterable');
    //     });
    // });

    // // ProductReview API routes
    // Route::prefix('product-reviews')->name('product-reviews.')->group(function () {
    //     // List product reviews
    //     Route::get('/', [ApiProductReviewController::class, 'index'])->name('index');

    //     // Get product review count
    //     Route::get('/count', [ApiProductReviewController::class, 'getCount'])->name('count');

    //     // Search product reviews
    //     Route::get('/search', [ApiProductReviewController::class, 'search'])->name('search');

    //     // Filter by status
    //     Route::get('/approved', [ApiProductReviewController::class, 'approved'])->name('approved');
    //     Route::get('/pending', [ApiProductReviewController::class, 'pending'])->name('pending');
    //     Route::get('/rejected', [ApiProductReviewController::class, 'rejected'])->name('rejected');
    //     Route::get('/featured', [ApiProductReviewController::class, 'featured'])->name('featured');
    //     Route::get('/verified', [ApiProductReviewController::class, 'verified'])->name('verified');

    //     // Filter by relationship
    //     Route::get('/by-product/{product}', [ApiProductReviewController::class, 'byProduct'])->name('by-product');
    //     Route::get('/by-user/{user}', [ApiProductReviewController::class, 'byUser'])->name('by-user');
    //     Route::get('/by-rating/{rating}', [ApiProductReviewController::class, 'byRating'])->name('by-rating');

    //     // Time-based queries
    //     Route::get('/recent', [ApiProductReviewController::class, 'recent'])->name('recent');
    //     Route::get('/popular', [ApiProductReviewController::class, 'popular'])->name('popular');
    //     Route::get('/helpful', [ApiProductReviewController::class, 'helpful'])->name('helpful');

    //     // Sentiment-based queries
    //     Route::get('/positive', [ApiProductReviewController::class, 'positive'])->name('positive');
    //     Route::get('/negative', [ApiProductReviewController::class, 'negative'])->name('negative');
    //     Route::get('/neutral', [ApiProductReviewController::class, 'neutral'])->name('neutral');

    //     // Moderation
    //     Route::get('/flagged', [ApiProductReviewController::class, 'flagged'])->name('flagged');
    //     Route::get('/moderation-queue', [ApiProductReviewController::class, 'moderationQueue'])->name('moderation-queue');

    //     // Analytics and statistics
    //     Route::get('/stats/{product}', [ApiProductReviewController::class, 'stats'])->name('stats');
    //     Route::get('/rating-distribution/{product}', [ApiProductReviewController::class, 'ratingDistribution'])->name('rating-distribution');
    //     Route::get('/average-rating/{product}', [ApiProductReviewController::class, 'averageRating'])->name('average-rating');
    //     Route::get('/analytics/{review}', [ApiProductReviewController::class, 'analytics'])->name('analytics');
    //     Route::get('/analytics-by-product/{product}', [ApiProductReviewController::class, 'analyticsByProduct'])->name('analytics-by-product');
    //     Route::get('/analytics-by-user/{user}', [ApiProductReviewController::class, 'analyticsByUser'])->name('analytics-by-user');

    //     // Create product review
    //     Route::post('/', [ApiProductReviewController::class, 'store'])->name('store');

    //     // ProductReview-specific routes
    //     Route::prefix('{review}')->group(function () {
    //         // Show product review
    //         Route::get('/', [ApiProductReviewController::class, 'show'])->name('show');

    //         // Update product review (full update)
    //         Route::put('/', [ApiProductReviewController::class, 'update'])->name('update');

    //         // Update product review (partial update)
    //         Route::patch('/', [ApiProductReviewController::class, 'update'])->name('update.partial');

    //         // Delete product review
    //         Route::delete('/', [ApiProductReviewController::class, 'destroy'])->name('destroy');

    //         // Status management
    //         Route::post('/approve', [ApiProductReviewController::class, 'approve'])->name('approve');
    //         Route::post('/reject', [ApiProductReviewController::class, 'reject'])->name('reject');
    //         Route::post('/feature', [ApiProductReviewController::class, 'feature'])->name('feature');
    //         Route::post('/unfeature', [ApiProductReviewController::class, 'unfeature'])->name('unfeature');
    //         Route::post('/verify', [ApiProductReviewController::class, 'verify'])->name('verify');
    //         Route::post('/unverify', [ApiProductReviewController::class, 'unverify'])->name('unverify');

    //         // Vote management
    //         Route::post('/vote', [ApiProductReviewController::class, 'vote'])->name('vote');
    //         Route::post('/flag', [ApiProductReviewController::class, 'flag'])->name('flag');
    //     });
    // });

    // // ProductVariant API routes
    // Route::prefix('product-variants')->name('product-variants.')->group(function () {
    //     // List product variants
    //     Route::get('/', [ApiProductVariantController::class, 'index'])->name('index');

    //     // Get product variant count
    //     Route::get('/count', [ApiProductVariantController::class, 'getCount'])->name('count');

    //     // Search product variants
    //     Route::get('/search', [ApiProductVariantController::class, 'search'])->name('search');

    //     // Filter by status
    //     Route::get('/active', [ApiProductVariantController::class, 'active'])->name('active');
    //     Route::get('/in-stock', [ApiProductVariantController::class, 'inStock'])->name('in-stock');
    //     Route::get('/out-of-stock', [ApiProductVariantController::class, 'outOfStock'])->name('out-of-stock');
    //     Route::get('/low-stock', [ApiProductVariantController::class, 'lowStock'])->name('low-stock');

    //     // Filter by relationships
    //     Route::get('/by-product/{product}', [ApiProductVariantController::class, 'byProduct'])->name('by-product');
    //     Route::get('/by-sku/{sku}', [ApiProductVariantController::class, 'bySku'])->name('by-sku');
    //     Route::get('/by-barcode/{barcode}', [ApiProductVariantController::class, 'byBarcode'])->name('by-barcode');

    //     // Filter by ranges
    //     Route::get('/by-price-range', [ApiProductVariantController::class, 'byPriceRange'])->name('by-price-range');
    //     Route::get('/by-stock-range', [ApiProductVariantController::class, 'byStockRange'])->name('by-stock-range');
    //     Route::get('/by-weight-range', [ApiProductVariantController::class, 'byWeightRange'])->name('by-weight-range');

    //     // List methods
    //     Route::get('/skus', [ApiProductVariantController::class, 'getSkus'])->name('skus');
    //     Route::get('/barcodes', [ApiProductVariantController::class, 'getBarcodes'])->name('barcodes');
    //     Route::get('/prices', [ApiProductVariantController::class, 'getPrices'])->name('prices');
    //     Route::get('/weights', [ApiProductVariantController::class, 'getWeights'])->name('weights');

    //     // Bulk operations
    //     Route::post('/bulk-create/{product}', [ApiProductVariantController::class, 'bulkCreate'])->name('bulk-create');
    //     Route::put('/bulk-update', [ApiProductVariantController::class, 'bulkUpdate'])->name('bulk-update');
    //     Route::delete('/bulk-delete', [ApiProductVariantController::class, 'bulkDelete'])->name('bulk-delete');

    //     // Import/Export operations
    //     Route::post('/import/{product}', [ApiProductVariantController::class, 'import'])->name('import');
    //     Route::get('/export/{product}', [ApiProductVariantController::class, 'export'])->name('export');
    //     Route::post('/sync/{product}', [ApiProductVariantController::class, 'sync'])->name('sync');

    //     // Create product variant
    //     Route::post('/', [ApiProductVariantController::class, 'store'])->name('store');

    //     // ProductVariant-specific routes
    //     Route::prefix('{variant}')->group(function () {
    //         // Show product variant
    //         Route::get('/', [ApiProductVariantController::class, 'show'])->name('show');

    //         // Update product variant (full update)
    //         Route::put('/', [ApiProductVariantController::class, 'update'])->name('update');

    //         // Update product variant (partial update)
    //         Route::patch('/', [ApiProductVariantController::class, 'update'])->name('update.partial');

    //         // Delete product variant
    //         Route::delete('/', [ApiProductVariantController::class, 'destroy'])->name('destroy');

    //         // Status management
    //         Route::post('/toggle-active', [ApiProductVariantController::class, 'toggleActive'])->name('toggle-active');
    //         Route::post('/toggle-featured', [ApiProductVariantController::class, 'toggleFeatured'])->name('toggle-featured');

    //         // Inventory management
    //         Route::post('/update-stock', [ApiProductVariantController::class, 'updateStock'])->name('update-stock');
    //         Route::post('/reserve-stock', [ApiProductVariantController::class, 'reserveStock'])->name('reserve-stock');
    //         Route::post('/release-stock', [ApiProductVariantController::class, 'releaseStock'])->name('release-stock');
    //         Route::post('/adjust-stock', [ApiProductVariantController::class, 'adjustStock'])->name('adjust-stock');

    //         // Pricing management
    //         Route::post('/set-price', [ApiProductVariantController::class, 'setPrice'])->name('set-price');
    //         Route::post('/set-sale-price', [ApiProductVariantController::class, 'setSalePrice'])->name('set-sale-price');
    //         Route::post('/set-compare-price', [ApiProductVariantController::class, 'setComparePrice'])->name('set-compare-price');

    //         // Inventory management
    //         Route::get('/inventory', [ApiProductVariantController::class, 'inventory'])->name('inventory');
    //         Route::get('/inventory-history', [ApiProductVariantController::class, 'inventoryHistory'])->name('inventory-history');
    //         Route::get('/inventory-alerts', [ApiProductVariantController::class, 'inventoryAlerts'])->name('inventory-alerts');

    //         // Analytics
    //         Route::get('/analytics', [ApiProductVariantController::class, 'analytics'])->name('analytics');
    //         Route::get('/sales', [ApiProductVariantController::class, 'sales'])->name('sales');
    //         Route::get('/revenue', [ApiProductVariantController::class, 'revenue'])->name('revenue');
    //         Route::get('/profit', [ApiProductVariantController::class, 'profit'])->name('profit');
    //         Route::get('/margin', [ApiProductVariantController::class, 'margin'])->name('margin');
    //     });
    // });

    // // Shipment API routes
    // Route::prefix('shipments')->name('shipments.')->group(function () {
    //     // List shipments
    //     Route::get('/', [ApiShipmentController::class, 'index'])->name('index');

    //     // Get shipment count
    //     Route::get('/count', [ApiShipmentController::class, 'getCount'])->name('count');

    //     // Search shipments
    //     Route::get('/search', [ApiShipmentController::class, 'search'])->name('search');

    //     // Filter by status
    //     Route::get('/pending', [ApiShipmentController::class, 'pending'])->name('pending');
    //     Route::get('/in-transit', [ApiShipmentController::class, 'inTransit'])->name('in-transit');
    //     Route::get('/delivered', [ApiShipmentController::class, 'delivered'])->name('delivered');
    //     Route::get('/returned', [ApiShipmentController::class, 'returned'])->name('returned');
    //     Route::get('/overdue', [ApiShipmentController::class, 'overdue'])->name('overdue');
    //     Route::get('/delayed', [ApiShipmentController::class, 'delayed'])->name('delayed');
    //     Route::get('/on-time', [ApiShipmentController::class, 'onTime'])->name('on-time');

    //     // Filter by relationships
    //     Route::get('/by-order/{order}', [ApiShipmentController::class, 'byOrder'])->name('by-order');
    //     Route::get('/by-carrier/{carrier}', [ApiShipmentController::class, 'byCarrier'])->name('by-carrier');
    //     Route::get('/by-status/{status}', [ApiShipmentController::class, 'byStatus'])->name('by-status');
    //     Route::get('/by-tracking/{tracking}', [ApiShipmentController::class, 'byTracking'])->name('by-tracking');

    //     // List methods
    //     Route::get('/carriers', [ApiShipmentController::class, 'getCarriers'])->name('carriers');
    //     Route::get('/tracking-numbers', [ApiShipmentController::class, 'getTrackingNumbers'])->name('tracking-numbers');

    //     // Analytics
    //     Route::get('/delivery-performance', [ApiShipmentController::class, 'deliveryPerformance'])->name('delivery-performance');
    //     Route::get('/shipping-costs', [ApiShipmentController::class, 'shippingCosts'])->name('shipping-costs');
    //     Route::get('/delivery-times', [ApiShipmentController::class, 'deliveryTimes'])->name('delivery-times');
    //     Route::get('/return-rates', [ApiShipmentController::class, 'returnRates'])->name('return-rates');
    //     Route::get('/carrier-performance', [ApiShipmentController::class, 'carrierPerformance'])->name('carrier-performance');
    //     Route::get('/trends', [ApiShipmentController::class, 'trends'])->name('trends');
    //     Route::get('/forecast', [ApiShipmentController::class, 'forecast'])->name('forecast');

    //     // Tracking and labels
    //     Route::get('/tracking-info/{tracking}', [ApiShipmentController::class, 'trackingInfo'])->name('tracking-info');
    //     Route::get('/shipping-label/{shipment}', [ApiShipmentController::class, 'shippingLabel'])->name('shipping-label');
    //     Route::get('/return-label/{shipment}', [ApiShipmentController::class, 'returnLabel'])->name('return-label');

    //     // Pickup operations
    //     Route::post('/schedule-pickup/{shipment}', [ApiShipmentController::class, 'schedulePickup'])->name('schedule-pickup');
    //     Route::post('/cancel-pickup/{shipment}', [ApiShipmentController::class, 'cancelPickup'])->name('cancel-pickup');
    //     Route::get('/pickup-confirmation/{shipment}', [ApiShipmentController::class, 'pickupConfirmation'])->name('pickup-confirmation');

    //     // Create shipment
    //     Route::post('/', [ApiShipmentController::class, 'store'])->name('store');

    //     // Shipment-specific routes
    //     Route::prefix('{shipment}')->group(function () {
    //         // Show shipment
    //         Route::get('/', [ApiShipmentController::class, 'show'])->name('show');

    //         // Update shipment (full update)
    //         Route::put('/', [ApiShipmentController::class, 'update'])->name('update');

    //         // Update shipment (partial update)
    //         Route::patch('/', [ApiShipmentController::class, 'update'])->name('update.partial');

    //         // Delete shipment
    //         Route::delete('/', [ApiShipmentController::class, 'destroy'])->name('destroy');

    //         // Status management
    //         Route::post('/ship', [ApiShipmentController::class, 'ship'])->name('ship');
    //         Route::post('/deliver', [ApiShipmentController::class, 'deliver'])->name('deliver');
    //         Route::post('/return', [ApiShipmentController::class, 'return'])->name('return');
    //         Route::post('/update-tracking', [ApiShipmentController::class, 'updateTracking'])->name('update-tracking');
    //         Route::post('/update-status', [ApiShipmentController::class, 'updateStatus'])->name('update-status');

    //         // Analytics
    //         Route::get('/analytics', [ApiShipmentController::class, 'analytics'])->name('analytics');
    //         Route::get('/analytics-by-order', [ApiShipmentController::class, 'analyticsByOrder'])->name('analytics-by-order');
    //         Route::get('/analytics-by-carrier', [ApiShipmentController::class, 'analyticsByCarrier'])->name('analytics-by-carrier');

    //         // Shipment Item API routes
    //         Route::prefix('items')->name('items.')->group(function () {
    //             // List shipment items
    //             Route::get('/', [ApiShipmentItemController::class, 'index'])->name('index');

    //             // Get shipment items count
    //             Route::get('/count', [ApiShipmentItemController::class, 'getCount'])->name('count');

    //             // Search shipment items
    //             Route::get('/search', [ApiShipmentItemController::class, 'search'])->name('search');

    //             // Get shipment items summary
    //             Route::get('/summary', [ApiShipmentItemController::class, 'summary'])->name('summary');

    //             // Calculate shipment weight and volume
    //             Route::get('/weight', [ApiShipmentItemController::class, 'calculateWeight'])->name('weight');
    //             Route::get('/volume', [ApiShipmentItemController::class, 'calculateVolume'])->name('volume');

    //             // Create shipment item
    //             Route::post('/', [ApiShipmentItemController::class, 'store'])->name('store');

    //             // Bulk operations
    //             Route::post('/bulk-create', [ApiShipmentItemController::class, 'bulkCreate'])->name('bulk-create');
    //             Route::put('/bulk-update', [ApiShipmentItemController::class, 'bulkUpdate'])->name('bulk-update');
    //             Route::delete('/bulk-delete', [ApiShipmentItemController::class, 'bulkDelete'])->name('bulk-delete');

    //             // Filter by quantity range
    //             Route::get('/by-quantity-range', [ApiShipmentItemController::class, 'byQuantityRange'])->name('by-quantity-range');

    //             // Filter by shipping status
    //             Route::get('/fully-shipped', [ApiShipmentItemController::class, 'fullyShipped'])->name('fully-shipped');
    //             Route::get('/partially-shipped', [ApiShipmentItemController::class, 'partiallyShipped'])->name('partially-shipped');

    //             // Filter by product and variant
    //             Route::get('/by-product/{product}', [ApiShipmentItemController::class, 'byProduct'])->name('by-product');
    //             Route::get('/by-variant/{variant}', [ApiShipmentItemController::class, 'byVariant'])->name('by-variant');

    //             // Analytics
    //             Route::get('/analytics', [ApiShipmentItemController::class, 'analytics'])->name('analytics');
    //             Route::get('/top-shipped', [ApiShipmentItemController::class, 'topShipped'])->name('top-shipped');

    //             // Shipment item-specific routes
    //             Route::prefix('{item}')->group(function () {
    //                 // Show shipment item
    //                 Route::get('/', [ApiShipmentItemController::class, 'show'])->name('show');

    //                 // Update shipment item (full update)
    //                 Route::put('/', [ApiShipmentItemController::class, 'update'])->name('update');

    //                 // Update shipment item (partial update)
    //                 Route::patch('/', [ApiShipmentItemController::class, 'update'])->name('update.partial');

    //                 // Delete shipment item
    //                 Route::delete('/', [ApiShipmentItemController::class, 'destroy'])->name('destroy');

    //                 // Get shipment item status
    //                 Route::get('/status', [ApiShipmentItemController::class, 'getStatus'])->name('status');
    //             });
    //         });
    //     });
    // });

    // // Transaction API routes
    // Route::prefix('transactions')->name('transactions.')->group(function () {
    //     // List transactions
    //     Route::get('/', [ApiTransactionController::class, 'index'])->name('index');

    //     // Get transaction count
    //     Route::get('/count', [ApiTransactionController::class, 'getCount'])->name('count');

    //     // Get transaction revenue
    //     Route::get('/revenue', [ApiTransactionController::class, 'getRevenue'])->name('revenue');

    //     // Search transactions
    //     Route::get('/search', [ApiTransactionController::class, 'search'])->name('search');

    //     // Get transaction statistics
    //     Route::get('/statistics', [ApiTransactionController::class, 'statistics'])->name('statistics');

    //     // Create transaction
    //     Route::post('/', [ApiTransactionController::class, 'store'])->name('store');

    //     // Filter by gateway
    //     Route::get('/by-gateway/{gateway}', [ApiTransactionController::class, 'getByGateway'])->name('by-gateway');

    //     // Filter by status
    //     Route::get('/by-status/{status}', [ApiTransactionController::class, 'getByStatus'])->name('by-status');

    //     // Transaction-specific routes
    //     Route::prefix('{transaction}')->group(function () {
    //         // Show transaction
    //         Route::get('/', [ApiTransactionController::class, 'show'])->name('show');

    //         // Update transaction (full update)
    //         Route::put('/', [ApiTransactionController::class, 'update'])->name('update');

    //         // Update transaction (partial update)
    //         Route::patch('/', [ApiTransactionController::class, 'update'])->name('update.partial');

    //         // Delete transaction
    //         Route::delete('/', [ApiTransactionController::class, 'destroy'])->name('destroy');

    //         // Transaction status management
    //         Route::post('/success', [ApiTransactionController::class, 'markAsSuccess'])->name('success');
    //         Route::post('/failed', [ApiTransactionController::class, 'markAsFailed'])->name('failed');
    //         Route::post('/refund', [ApiTransactionController::class, 'markAsRefunded'])->name('refund');
    //     });
    // });

    // // UserSubscription API routes
    // Route::prefix('user-subscriptions')->name('user-subscriptions.')->group(function () {
    //     // List user subscriptions
    //     Route::get('/', [ApiUserSubscriptionController::class, 'index'])->name('index');

    //     // Get user subscription count
    //     Route::get('/count', [ApiUserSubscriptionController::class, 'getCount'])->name('count');

    //     // Search user subscriptions
    //     Route::get('/search', [ApiUserSubscriptionController::class, 'search'])->name('search');

    //     // Get user subscription statistics
    //     Route::get('/statistics', [ApiUserSubscriptionController::class, 'statistics'])->name('statistics');

    //     // Get user subscription analytics
    //     Route::get('/analytics', [ApiUserSubscriptionController::class, 'analytics'])->name('analytics');

    //     // Get user subscription revenue
    //     Route::get('/revenue', [ApiUserSubscriptionController::class, 'revenue'])->name('revenue');

    //     // Get popular subscriptions
    //     Route::get('/popular', [ApiUserSubscriptionController::class, 'popular'])->name('popular');

    //     // Filter by status
    //     Route::get('/active', [ApiUserSubscriptionController::class, 'getActive'])->name('active');
    //     Route::get('/trial', [ApiUserSubscriptionController::class, 'getTrial'])->name('trial');
    //     Route::get('/expired', [ApiUserSubscriptionController::class, 'getExpired'])->name('expired');
    //     Route::get('/cancelled', [ApiUserSubscriptionController::class, 'getCancelled'])->name('cancelled');
    //     Route::get('/paused', [ApiUserSubscriptionController::class, 'getPaused'])->name('paused');

    //     // Renewal and expiration tracking
    //     Route::get('/renewals', [ApiUserSubscriptionController::class, 'getRenewals'])->name('renewals');
    //     Route::get('/expiring-trials', [ApiUserSubscriptionController::class, 'getExpiringTrials'])->name('expiring-trials');
    //     Route::get('/expiring', [ApiUserSubscriptionController::class, 'getExpiring'])->name('expiring');

    //     // Create user subscription
    //     Route::post('/', [ApiUserSubscriptionController::class, 'store'])->name('store');

    //     // Filter by user
    //     Route::get('/by-user/{user}', [ApiUserSubscriptionController::class, 'getByUser'])->name('by-user');

    //     // Filter by subscription
    //     Route::get('/by-subscription/{subscription}', [ApiUserSubscriptionController::class, 'getBySubscription'])->name('by-subscription');

    //     // Filter by status
    //     Route::get('/by-status/{status}', [ApiUserSubscriptionController::class, 'getByStatus'])->name('by-status');

    //     // User subscription-specific routes
    //     Route::prefix('{userSubscription}')->group(function () {
    //         // Show user subscription
    //         Route::get('/', [ApiUserSubscriptionController::class, 'show'])->name('show');

    //         // Update user subscription (full update)
    //         Route::put('/', [ApiUserSubscriptionController::class, 'update'])->name('update');

    //         // Update user subscription (partial update)
    //         Route::patch('/', [ApiUserSubscriptionController::class, 'update'])->name('update.partial');

    //         // Delete user subscription
    //         Route::delete('/', [ApiUserSubscriptionController::class, 'destroy'])->name('destroy');

    //         // Lifecycle management
    //         Route::post('/activate', [ApiUserSubscriptionController::class, 'activate'])->name('activate');
    //         Route::post('/cancel', [ApiUserSubscriptionController::class, 'cancel'])->name('cancel');
    //         Route::post('/renew', [ApiUserSubscriptionController::class, 'renew'])->name('renew');
    //         Route::post('/pause', [ApiUserSubscriptionController::class, 'pause'])->name('pause');
    //         Route::post('/resume', [ApiUserSubscriptionController::class, 'resume'])->name('resume');
    //     });
    // });

    // Customer API routes
    Route::prefix('customers')->name('customers.')->group(function () {
        // List customers
        Route::get('/', [ApiCustomerController::class, 'index'])->name('index');

        // Get customer's type list
        Route::get('/customer-types/cursor-all', [ApiCustomerController::class, 'customerTypes'])->name('customer-types');

        // Get customer's status list
        Route::get('/statuses/cursor-all', [ApiCustomerController::class, 'statuses'])->name('statuses');

        //     // Search customers
        //     Route::get('/search', [ApiCustomerController::class, 'search'])->name('search');

        //     // Get customer statistics
        //     Route::get('/stats', [ApiCustomerController::class, 'stats'])->name('stats');

        // Create customer
        Route::post('/', [ApiCustomerController::class, 'store'])->name('store');

        // Customer-specific routes
        Route::prefix('{customer}')->group(function () {
            //         // Show customer
            Route::get('/', [ApiCustomerController::class, 'show'])->name('show');

            // Update customer (full update)
            Route::put('/', [ApiCustomerController::class, 'update'])->name('update');

            //         // Update customer (partial update)
            //         Route::patch('/', [ApiCustomerController::class, 'update'])->name('update.partial');

            //         // Delete customer
            //         Route::delete('/', [ApiCustomerController::class, 'destroy'])->name('destroy');

            //         // Customer status management
            //         Route::post('/activate', [ApiCustomerController::class, 'activate'])->name('activate');
            //         Route::post('/deactivate', [ApiCustomerController::class, 'deactivate'])->name('deactivate');
            //         Route::post('/suspend', [ApiCustomerController::class, 'suspend'])->name('suspend');

            //         // Loyalty points management
            //         Route::post('/loyalty-points', [ApiCustomerController::class, 'loyaltyPoints'])->name('loyalty-points');

            //         // Customer relationships
            //         Route::get('/orders', [ApiCustomerController::class, 'orders'])->name('orders');
            //         Route::get('/addresses', [ApiCustomerController::class, 'addresses'])->name('addresses');
            //         Route::get('/reviews', [ApiCustomerController::class, 'reviews'])->name('reviews');
            //         Route::get('/wishlist', [ApiCustomerController::class, 'wishlist'])->name('wishlist');

            //         // Customer analytics
            //         Route::get('/analytics', [ApiCustomerController::class, 'analytics'])->name('analytics');

            //         // Customer notes
            //         Route::get('/notes', [ApiCustomerController::class, 'notes'])->name('notes');
            //         Route::post('/notes', [ApiCustomerController::class, 'addNote'])->name('add-note');

            //         // Customer preferences
            //         Route::put('/preferences', [ApiCustomerController::class, 'updatePreferences'])->name('update-preferences');
        });
    });

    // Customer API routes
    Route::prefix('users')->name('users.')->group(function () {
        // List customers
        Route::get('/', [ApiUserController::class, 'index'])->name('index');

        // Get customer's type list
        Route::get('/user-types/cursor-all', [ApiUserController::class, 'userTypes'])->name('user-types');

        // Get user's status list
        Route::get('/roles/cursor-all', [ApiUserController::class, 'roles'])->name('roles');

        // Create user
        Route::post('/', [ApiUserController::class, 'store'])->name('store');

        // Customer-specific routes
        Route::prefix('{user}')->group(function () {
            //         // Show user
            Route::get('/', [ApiUserController::class, 'show'])->name('show');

            // Update user (full update)
            Route::put('/', [ApiUserController::class, 'update'])->name('update');

            //         // Update user (partial update)
            //         Route::patch('/', [ApiUserController::class, 'update'])->name('update.partial');

            //         // Delete user
            //         Route::delete('/', [ApiUserController::class, 'destroy'])->name('destroy');

            //         // Customer status management
            //         Route::post('/activate', [ApiUserController::class, 'activate'])->name('activate');
            //         Route::post('/deactivate', [ApiUserController::class, 'deactivate'])->name('deactivate');
            //         Route::post('/suspend', [ApiUserController::class, 'suspend'])->name('suspend');
        });
    });

    // // Customer Segment API routes
    // Route::prefix('customer-segments')->name('customer-segments.')->middleware(['permission:customer-segments.*'])->group(function () {
    //     // List customer segments
    //     Route::get('/', [ApiCustomerSegmentController::class, 'index'])->name('index');

    //     // Get segment statistics
    //     Route::get('/statistics', [ApiCustomerSegmentController::class, 'statistics'])->name('statistics');

    //     // Get segment recommendations
    //     Route::get('/recommendations', [ApiCustomerSegmentController::class, 'recommendations'])->name('recommendations');

    //     // Get segment insights
    //     Route::get('/insights', [ApiCustomerSegmentController::class, 'insights'])->name('insights');

    //     // Get segment trends forecast
    //     Route::get('/trends-forecast', [ApiCustomerSegmentController::class, 'trendsForecast'])->name('trends-forecast');

    //     // Compare segments
    //     Route::get('/compare', [ApiCustomerSegmentController::class, 'compare'])->name('compare');

    //     // Recalculate all segments
    //     Route::post('/recalculate-all', [ApiCustomerSegmentController::class, 'recalculateAll'])->name('recalculate-all');

    //     // Get segments needing recalculation
    //     Route::get('/needing-recalculation', [ApiCustomerSegmentController::class, 'needingRecalculation'])->name('needing-recalculation');

    //     // Merge segments
    //     Route::post('/merge', [ApiCustomerSegmentController::class, 'merge'])->name('merge');

    //     // Import segment
    //     Route::post('/import', [ApiCustomerSegmentController::class, 'import'])->name('import');

    //     // Create customer segment
    //     Route::post('/', [ApiCustomerSegmentController::class, 'store'])->name('store');

    //     // Customer segment-specific routes
    //     Route::prefix('{customerSegment}')->group(function () {
    //         // Show customer segment
    //         Route::get('/', [ApiCustomerSegmentController::class, 'show'])->name('show');

    //         // Update customer segment (full update)
    //         Route::put('/', [ApiCustomerSegmentController::class, 'update'])->name('update');

    //         // Update customer segment (partial update)
    //         Route::patch('/', [ApiCustomerSegmentController::class, 'update'])->name('update.partial');

    //         // Delete customer segment
    //         Route::delete('/', [ApiCustomerSegmentController::class, 'destroy'])->name('destroy');

    //         // Status management
    //         Route::post('/activate', [ApiCustomerSegmentController::class, 'activate'])->name('activate');
    //         Route::post('/deactivate', [ApiCustomerSegmentController::class, 'deactivate'])->name('deactivate');

    //         // Segment type management
    //         Route::post('/make-automatic', [ApiCustomerSegmentController::class, 'makeAutomatic'])->name('make-automatic');
    //         Route::post('/make-manual', [ApiCustomerSegmentController::class, 'makeManual'])->name('make-manual');
    //         Route::post('/make-dynamic', [ApiCustomerSegmentController::class, 'makeDynamic'])->name('make-dynamic');
    //         Route::post('/make-static', [ApiCustomerSegmentController::class, 'makeStatic'])->name('make-static');

    //         // Priority management
    //         Route::post('/set-priority', [ApiCustomerSegmentController::class, 'setPriority'])->name('set-priority');

    //         // Calculation
    //         Route::post('/calculate', [ApiCustomerSegmentController::class, 'calculate'])->name('calculate');

    //         // Customer management
    //         Route::post('/add-customer', [ApiCustomerSegmentController::class, 'addCustomer'])->name('add-customer');
    //         Route::post('/remove-customer', [ApiCustomerSegmentController::class, 'removeCustomer'])->name('remove-customer');
    //         Route::get('/customers', [ApiCustomerSegmentController::class, 'customers'])->name('customers');

    //         // Criteria and conditions
    //         Route::put('/criteria', [ApiCustomerSegmentController::class, 'updateCriteria'])->name('update-criteria');
    //         Route::put('/conditions', [ApiCustomerSegmentController::class, 'updateConditions'])->name('update-conditions');

    //         // Analytics
    //         Route::get('/analytics', [ApiCustomerSegmentController::class, 'analytics'])->name('analytics');
    //         Route::get('/forecast', [ApiCustomerSegmentController::class, 'forecast'])->name('forecast');

    //         // Export
    //         Route::get('/export', [ApiCustomerSegmentController::class, 'export'])->name('export');

    //         // Duplicate
    //         Route::post('/duplicate', [ApiCustomerSegmentController::class, 'duplicate'])->name('duplicate');

    //         // Split
    //         Route::post('/split', [ApiCustomerSegmentController::class, 'split'])->name('split');

    //         // Overlapping segments
    //         Route::get('/overlapping', [ApiCustomerSegmentController::class, 'overlapping'])->name('overlapping');
    //     });
    // });

    // // Customer Preference API routes
    // Route::prefix('customer-preferences')->name('customer-preferences.')->middleware(['permission:customer-preferences.*'])->group(function () {
    //     // List customer preferences
    //     Route::get('/', [ApiCustomerPreferenceController::class, 'index'])->name('index');

    //     // Search customer preferences
    //     Route::get('/search', [ApiCustomerPreferenceController::class, 'search'])->name('search');

    //     // Get preference statistics
    //     Route::get('/stats', [ApiCustomerPreferenceController::class, 'stats'])->name('stats');

    //     // Get preference templates
    //     Route::get('/templates', [ApiCustomerPreferenceController::class, 'templates'])->name('templates');

    //     // Create customer preference
    //     Route::post('/', [ApiCustomerPreferenceController::class, 'store'])->name('store');

    //     // Customer preference-specific routes
    //     Route::prefix('{preference}')->group(function () {
    //         // Show customer preference
    //         Route::get('/', [ApiCustomerPreferenceController::class, 'show'])->name('show');

    //         // Update customer preference (full update)
    //         Route::put('/', [ApiCustomerPreferenceController::class, 'update'])->name('update');

    //         // Update customer preference (partial update)
    //         Route::patch('/', [ApiCustomerPreferenceController::class, 'update'])->name('update.partial');

    //         // Delete customer preference
    //         Route::delete('/', [ApiCustomerPreferenceController::class, 'destroy'])->name('destroy');

    //         // Status management
    //         Route::post('/activate', [ApiCustomerPreferenceController::class, 'activate'])->name('activate');
    //         Route::post('/deactivate', [ApiCustomerPreferenceController::class, 'deactivate'])->name('deactivate');
    //     });
    // });

    // // Customer-specific preference routes
    // Route::prefix('customers/{customer}/preferences')->name('customers.preferences.')->middleware(['permission:customer-preferences.*'])->group(function () {
    //     // Get customer preferences
    //     Route::get('/', [ApiCustomerPreferenceController::class, 'getCustomerPreferences'])->name('index');

    //     // Set customer preference
    //     Route::post('/', [ApiCustomerPreferenceController::class, 'setCustomerPreference'])->name('store');

    //     // Reset all customer preferences
    //     Route::post('/reset', [ApiCustomerPreferenceController::class, 'resetCustomerPreferences'])->name('reset');

    //     // Import customer preferences
    //     Route::post('/import', [ApiCustomerPreferenceController::class, 'importCustomerPreferences'])->name('import');

    //     // Export customer preferences
    //     Route::get('/export', [ApiCustomerPreferenceController::class, 'exportCustomerPreferences'])->name('export');

    //     // Sync customer preferences
    //     Route::post('/sync', [ApiCustomerPreferenceController::class, 'syncCustomerPreferences'])->name('sync');

    //     // Get customer preference summary
    //     Route::get('/summary', [ApiCustomerPreferenceController::class, 'summary'])->name('summary');

    //     // Initialize customer preferences
    //     Route::post('/initialize', [ApiCustomerPreferenceController::class, 'initialize'])->name('initialize');

    //     // Apply preference template
    //     Route::post('/apply-template', [ApiCustomerPreferenceController::class, 'applyTemplate'])->name('apply-template');

    //     // Key-specific preference routes
    //     Route::prefix('{key}')->group(function () {
    //         // Get specific customer preference
    //         Route::get('/', [ApiCustomerPreferenceController::class, 'getCustomerPreference'])->name('show');

    //         // Update specific customer preference
    //         Route::put('/', [ApiCustomerPreferenceController::class, 'updateCustomerPreference'])->name('update');

    //         // Remove specific customer preference
    //         Route::delete('/', [ApiCustomerPreferenceController::class, 'removeCustomerPreference'])->name('destroy');
    //     });
    // });

    // // Customer Note API routes
    // Route::prefix('customer-notes')->name('customer-notes.')->middleware(['permission:customer-notes.*'])->group(function () {
    //     // List customer notes
    //     Route::get('/', [ApiCustomerNoteController::class, 'index'])->name('index');

    //     // Search customer notes
    //     Route::get('/search', [ApiCustomerNoteController::class, 'search'])->name('search');

    //     // Get note statistics
    //     Route::get('/stats', [ApiCustomerNoteController::class, 'stats'])->name('stats');

    //     // Get note types
    //     Route::get('/types', [ApiCustomerNoteController::class, 'types'])->name('types');

    //     // Get note priorities
    //     Route::get('/priorities', [ApiCustomerNoteController::class, 'priorities'])->name('priorities');

    //     // Get note templates
    //     Route::get('/templates', [ApiCustomerNoteController::class, 'templates'])->name('templates');

    //     // Create customer note
    //     Route::post('/', [ApiCustomerNoteController::class, 'store'])->name('store');

    //     // Customer note-specific routes
    //     Route::prefix('{customerNote}')->group(function () {
    //         // Show customer note
    //         Route::get('/', [ApiCustomerNoteController::class, 'show'])->name('show');

    //         // Update customer note (full update)
    //         Route::put('/', [ApiCustomerNoteController::class, 'update'])->name('update');

    //         // Update customer note (partial update)
    //         Route::patch('/', [ApiCustomerNoteController::class, 'update'])->name('update.partial');

    //         // Delete customer note
    //         Route::delete('/', [ApiCustomerNoteController::class, 'destroy'])->name('destroy');

    //         // Status management
    //         Route::post('/pin', [ApiCustomerNoteController::class, 'pin'])->name('pin');
    //         Route::post('/unpin', [ApiCustomerNoteController::class, 'unpin'])->name('unpin');
    //         Route::post('/make-private', [ApiCustomerNoteController::class, 'makePrivate'])->name('make-private');
    //         Route::post('/make-public', [ApiCustomerNoteController::class, 'makePublic'])->name('make-public');

    //         // Tag management
    //         Route::post('/tags', [ApiCustomerNoteController::class, 'addTag'])->name('add-tag');
    //         Route::delete('/tags/{tag}', [ApiCustomerNoteController::class, 'removeTag'])->name('remove-tag');

    //         // Attachment management
    //         Route::post('/attachments', [ApiCustomerNoteController::class, 'addAttachment'])->name('add-attachment');
    //         Route::delete('/attachments/{mediaId}', [ApiCustomerNoteController::class, 'removeAttachment'])->name('remove-attachment');
    //     });
    // });

    // // Customer-specific note routes
    // Route::prefix('customers/{customerId}/notes')->name('customers.notes.')->middleware(['permission:customer-notes.*'])->group(function () {
    //     // Get customer notes
    //     Route::get('/', [ApiCustomerNoteController::class, 'getCustomerNotes'])->name('index');

    //     // Export customer notes
    //     Route::get('/export', [ApiCustomerNoteController::class, 'export'])->name('export');

    //     // Import customer notes
    //     Route::post('/import', [ApiCustomerNoteController::class, 'import'])->name('import');
    // });

    // // Loyalty Transaction API routes
    // Route::prefix('loyalty-transactions')->name('loyalty-transactions.')->middleware(['permission:loyalty-transactions.*'])->group(function () {
    //     // List loyalty transactions
    //     Route::get('/', [ApiLoyaltyTransactionController::class, 'index'])->name('index');

    //     // Search loyalty transactions
    //     Route::get('/search', [ApiLoyaltyTransactionController::class, 'search'])->name('search');

    //     // Get recent transactions
    //     Route::get('/recent', [ApiLoyaltyTransactionController::class, 'getRecent'])->name('recent');

    //     // Get transaction statistics
    //     Route::get('/statistics', [ApiLoyaltyTransactionController::class, 'getStatistics'])->name('statistics');

    //     // Points management
    //     Route::post('/add-points', [ApiLoyaltyTransactionController::class, 'addPoints'])->name('add-points');
    //     Route::post('/deduct-points', [ApiLoyaltyTransactionController::class, 'deductPoints'])->name('deduct-points');

    //     // Create loyalty transaction
    //     Route::post('/', [ApiLoyaltyTransactionController::class, 'store'])->name('store');

    //     // Loyalty transaction-specific routes
    //     Route::prefix('{loyaltyTransaction}')->group(function () {
    //         // Show loyalty transaction
    //         Route::get('/', [ApiLoyaltyTransactionController::class, 'show'])->name('show');

    //         // Update loyalty transaction (full update)
    //         Route::put('/', [ApiLoyaltyTransactionController::class, 'update'])->name('update');

    //         // Update loyalty transaction (partial update)
    //         Route::patch('/', [ApiLoyaltyTransactionController::class, 'update'])->name('update.partial');

    //         // Delete loyalty transaction
    //         Route::delete('/', [ApiLoyaltyTransactionController::class, 'destroy'])->name('destroy');

    //         // Reverse transaction
    //         Route::post('/reverse', [ApiLoyaltyTransactionController::class, 'reverse'])->name('reverse');

    //         // Get customer balance
    //         Route::get('/balance', [ApiLoyaltyTransactionController::class, 'getBalance'])->name('balance');

    //         // Get customer tier
    //         Route::get('/tier', [ApiLoyaltyTransactionController::class, 'getTier'])->name('tier');

    //         // Get transaction history
    //         Route::get('/history', [ApiLoyaltyTransactionController::class, 'getHistory'])->name('history');

    //         // Get transaction analytics
    //         Route::get('/analytics', [ApiLoyaltyTransactionController::class, 'getAnalytics'])->name('analytics');
    //     });

    //     // Customer-specific loyalty routes
    //     Route::prefix('customers/{customerId}')->group(function () {
    //         // Get customer balance
    //         Route::get('/balance', [ApiLoyaltyTransactionController::class, 'getBalance'])->name('customer.balance');

    //         // Get customer tier
    //         Route::get('/tier', [ApiLoyaltyTransactionController::class, 'getTier'])->name('customer.tier');

    //         // Get customer transaction history
    //         Route::get('/history', [ApiLoyaltyTransactionController::class, 'getHistory'])->name('customer.history');

    //         // Get customer transaction analytics
    //         Route::get('/analytics', [ApiLoyaltyTransactionController::class, 'getAnalytics'])->name('customer.analytics');

    //         // Export customer history
    //         Route::get('/export-history', [ApiLoyaltyTransactionController::class, 'exportHistory'])->name('customer.export-history');
    //     });
    // });

    // // Customer Communication API routes
    // Route::prefix('customer-communications')->name('customer-communications.')->middleware(['permission:customer-communications.*'])->group(function () {
    //     // List customer communications
    //     Route::get('/', [ApiCustomerCommunicationController::class, 'index'])->name('index');

    //     // Create customer communication
    //     Route::post('/', [ApiCustomerCommunicationController::class, 'store'])->name('store');

    //     // Customer communication-specific routes
    //     Route::prefix('{id}')->group(function () {
    //         // Show customer communication
    //         Route::get('/', [ApiCustomerCommunicationController::class, 'show'])->name('show');

    //         // Update customer communication (full update)
    //         Route::put('/', [ApiCustomerCommunicationController::class, 'update'])->name('update');

    //         // Update customer communication (partial update)
    //         Route::patch('/', [ApiCustomerCommunicationController::class, 'update'])->name('update.partial');

    //         // Delete customer communication
    //         Route::delete('/', [ApiCustomerCommunicationController::class, 'destroy'])->name('destroy');

    //         // Communication status management
    //         Route::post('/schedule', [ApiCustomerCommunicationController::class, 'schedule'])->name('schedule');
    //         Route::post('/send', [ApiCustomerCommunicationController::class, 'send'])->name('send');
    //         Route::post('/cancel', [ApiCustomerCommunicationController::class, 'cancel'])->name('cancel');
    //         Route::post('/reschedule', [ApiCustomerCommunicationController::class, 'reschedule'])->name('reschedule');

    //         // Communication tracking
    //         Route::post('/mark-delivered', [ApiCustomerCommunicationController::class, 'markAsDelivered'])->name('mark-delivered');
    //         Route::post('/mark-opened', [ApiCustomerCommunicationController::class, 'markAsOpened'])->name('mark-opened');
    //         Route::post('/mark-clicked', [ApiCustomerCommunicationController::class, 'markAsClicked'])->name('mark-clicked');
    //         Route::post('/mark-bounced', [ApiCustomerCommunicationController::class, 'markAsBounced'])->name('mark-bounced');
    //         Route::post('/mark-unsubscribed', [ApiCustomerCommunicationController::class, 'markAsUnsubscribed'])->name('mark-unsubscribed');

    //         // Analytics and tracking
    //         Route::get('/analytics', [ApiCustomerCommunicationController::class, 'analytics'])->name('analytics');
    //         Route::get('/tracking', [ApiCustomerCommunicationController::class, 'tracking'])->name('tracking');

    //         // Attachment management
    //         Route::post('/attachments', [ApiCustomerCommunicationController::class, 'addAttachment'])->name('add-attachment');
    //         Route::delete('/attachments/{mediaId}', [ApiCustomerCommunicationController::class, 'removeAttachment'])->name('remove-attachment');
    //     });
    // });

    // // Employee API routes
    // Route::prefix('employees')->name('employees.')->middleware(['permission:employees.*'])->group(function () {
    //     // List employees
    //     Route::get('/', [ApiEmployeeController::class, 'index'])->name('index');

    //     // Get employee count
    //     Route::get('/count', [ApiEmployeeController::class, 'getCount'])->name('count');

    //     // Search employees
    //     Route::get('/search', [ApiEmployeeController::class, 'search'])->name('search');

    //     // Get employee statistics
    //     Route::get('/stats', [ApiEmployeeController::class, 'getStats'])->name('stats');

    //     // Get employee analytics
    //     Route::get('/analytics', [ApiEmployeeController::class, 'analytics'])->name('analytics');

    //     // Create employee
    //     Route::post('/', [ApiEmployeeController::class, 'store'])->name('store');

    //     // Employee-specific routes
    //     Route::prefix('{employee}')->group(function () {
    //         // Show employee
    //         Route::get('/', [ApiEmployeeController::class, 'show'])->name('show');

    //         // Update employee (full update)
    //         Route::put('/', [ApiEmployeeController::class, 'update'])->name('update');

    //         // Update employee (partial update)
    //         Route::patch('/', [ApiEmployeeController::class, 'update'])->name('update.partial');

    //         // Delete employee
    //         Route::delete('/', [ApiEmployeeController::class, 'destroy'])->name('destroy');

    //         // Employee status management
    //         Route::post('/activate', [ApiEmployeeController::class, 'activate'])->name('activate');
    //         Route::post('/deactivate', [ApiEmployeeController::class, 'deactivate'])->name('deactivate');
    //         Route::post('/terminate', [ApiEmployeeController::class, 'terminate'])->name('terminate');
    //         Route::post('/rehire', [ApiEmployeeController::class, 'rehire'])->name('rehire');

    //         // Employee position and department management
    //         Route::put('/position', [ApiEmployeeController::class, 'updatePosition'])->name('update-position');
    //         Route::put('/department', [ApiEmployeeController::class, 'updateDepartment'])->name('update-department');
    //         Route::post('/manager', [ApiEmployeeController::class, 'assignManager'])->name('assign-manager');

    //         // Employee salary management
    //         Route::put('/salary', [ApiEmployeeController::class, 'updateSalary'])->name('update-salary');

    //         // Employee performance management
    //         Route::put('/performance', [ApiEmployeeController::class, 'updatePerformance'])->name('update-performance');

    //         // Employee time-off management
    //         Route::post('/time-off', [ApiEmployeeController::class, 'manageTimeOff'])->name('manage-time-off');
    //         Route::get('/time-off', [ApiEmployeeController::class, 'getTimeOff'])->name('get-time-off');

    //         // Employee hierarchy
    //         Route::get('/subordinates', [ApiEmployeeController::class, 'getSubordinates'])->name('subordinates');
    //         Route::get('/managers', [ApiEmployeeController::class, 'getManagers'])->name('managers');
    //         Route::get('/hierarchy', [ApiEmployeeController::class, 'getHierarchy'])->name('hierarchy');

    //         // Employee analytics
    //         Route::get('/analytics', [ApiEmployeeController::class, 'getEmployeeAnalytics'])->name('employee-analytics');

    //         // Employee notes
    //         Route::post('/notes', [ApiEmployeeController::class, 'addNote'])->name('add-note');
    //         Route::get('/notes', [ApiEmployeeController::class, 'getNotes'])->name('get-notes');

    //         // Employee benefits
    //         Route::put('/benefits', [ApiEmployeeController::class, 'updateBenefits'])->name('update-benefits');
    //         Route::get('/benefits', [ApiEmployeeController::class, 'getBenefits'])->name('get-benefits');

    //         // Employee skills and certifications
    //         Route::put('/skills', [ApiEmployeeController::class, 'updateSkills'])->name('update-skills');
    //         Route::get('/skills', [ApiEmployeeController::class, 'getSkills'])->name('get-skills');
    //         Route::put('/certifications', [ApiEmployeeController::class, 'updateCertifications'])->name('update-certifications');
    //         Route::get('/certifications', [ApiEmployeeController::class, 'getCertifications'])->name('get-certifications');
    //     });

    //     // Bulk operations
    //     Route::post('/bulk-activate', [ApiEmployeeController::class, 'bulkActivate'])->name('bulk-activate');
    //     Route::post('/bulk-deactivate', [ApiEmployeeController::class, 'bulkDeactivate'])->name('bulk-deactivate');
    //     Route::post('/bulk-terminate', [ApiEmployeeController::class, 'bulkTerminate'])->name('bulk-terminate');

    //     // Import/Export
    //     Route::post('/import', [ApiEmployeeController::class, 'import'])->name('import');
    //     Route::get('/export', [ApiEmployeeController::class, 'export'])->name('export');

    //     // Reports
    //     Route::get('/reports/performance', [ApiEmployeeController::class, 'performanceReport'])->name('reports.performance');
    //     Route::get('/reports/turnover', [ApiEmployeeController::class, 'turnoverReport'])->name('reports.turnover');
    //     Route::get('/reports/salary', [ApiEmployeeController::class, 'salaryReport'])->name('reports.salary');
    //     Route::get('/reports/time-off', [ApiEmployeeController::class, 'timeOffReport'])->name('reports.time-off');
    // });

    // // Provider API routes
    // Route::prefix('providers')->name('providers.')->middleware(['permission:providers.*'])->group(function () {
    //     // List providers
    //     Route::get('/', [ApiProviderController::class, 'index'])->name('index');

    //     // Create provider
    //     Route::post('/', [ApiProviderController::class, 'store'])->name('store');

    //     // Provider-specific routes
    //     Route::prefix('{id}')->group(function () {
    //         // Show provider
    //         Route::get('/', [ApiProviderController::class, 'show'])->name('show');

    //         // Update provider
    //         Route::put('/', [ApiProviderController::class, 'update'])->name('update');

    //         // Delete provider
    //         Route::delete('/', [ApiProviderController::class, 'destroy'])->name('destroy');

    //         // Provider status management
    //         Route::post('/activate', [ApiProviderController::class, 'activate'])->name('activate');
    //         Route::post('/deactivate', [ApiProviderController::class, 'deactivate'])->name('deactivate');
    //         Route::post('/suspend', [ApiProviderController::class, 'suspend'])->name('suspend');

    //         // Provider rating management
    //         Route::put('/rating', [ApiProviderController::class, 'updateRating'])->name('update-rating');
    //         Route::put('/quality-rating', [ApiProviderController::class, 'updateQualityRating'])->name('update-quality-rating');
    //         Route::put('/delivery-rating', [ApiProviderController::class, 'updateDeliveryRating'])->name('update-delivery-rating');
    //         Route::put('/communication-rating', [ApiProviderController::class, 'updateCommunicationRating'])->name('update-communication-rating');

    //         // Provider financial management
    //         Route::put('/credit-limit', [ApiProviderController::class, 'updateCreditLimit'])->name('update-credit-limit');
    //         Route::put('/commission-rate', [ApiProviderController::class, 'updateCommissionRate'])->name('update-commission-rate');
    //         Route::put('/discount-rate', [ApiProviderController::class, 'updateDiscountRate'])->name('update-discount-rate');

    //         // Provider contract management
    //         Route::put('/extend-contract', [ApiProviderController::class, 'extendContract'])->name('extend-contract');
    //         Route::post('/terminate-contract', [ApiProviderController::class, 'terminateContract'])->name('terminate-contract');

    //         // Provider data
    //         Route::get('/orders', [ApiProviderController::class, 'getOrders'])->name('orders');
    //         Route::get('/products', [ApiProviderController::class, 'getProducts'])->name('products');
    //         Route::get('/analytics', [ApiProviderController::class, 'getAnalytics'])->name('analytics');
    //         Route::get('/performance-metrics', [ApiProviderController::class, 'getPerformanceMetrics'])->name('performance-metrics');

    //         // Provider notes
    //         Route::post('/notes', [ApiProviderController::class, 'addNote'])->name('add-note');
    //         Route::get('/notes', [ApiProviderController::class, 'getNotes'])->name('get-notes');

    //         // Provider specializations and certifications
    //         Route::put('/specializations', [ApiProviderController::class, 'updateSpecializations'])->name('update-specializations');
    //         Route::put('/certifications', [ApiProviderController::class, 'updateCertifications'])->name('update-certifications');
    //         Route::put('/insurance', [ApiProviderController::class, 'updateInsurance'])->name('update-insurance');
    //     });
    // });

    // // Provider Insurance API routes
    // Route::prefix('provider-insurance')->name('provider-insurance.')->middleware(['permission:provider-insurance.*'])->group(function () {
    //     // List provider insurance
    //     Route::get('/', [ProviderInsuranceController::class, 'index'])->name('index');

    //     // Get provider insurance count
    //     Route::get('/count', [ProviderInsuranceController::class, 'getCount'])->name('count');

    //     // Search provider insurance
    //     Route::get('/search', [ProviderInsuranceController::class, 'search'])->name('search');

    //     // Get insurance statistics
    //     Route::get('/stats', [ProviderInsuranceController::class, 'getStats'])->name('stats');

    //     // Get insurance analytics
    //     Route::get('/analytics', [ProviderInsuranceController::class, 'getAnalytics'])->name('analytics');

    //     // Create provider insurance
    //     Route::post('/', [ProviderInsuranceController::class, 'store'])->name('store');

    //     // Bulk operations
    //     Route::post('/bulk-verify', [ProviderInsuranceController::class, 'bulkVerify'])->name('bulk-verify');
    //     Route::post('/bulk-reject', [ProviderInsuranceController::class, 'bulkReject'])->name('bulk-reject');
    //     Route::post('/bulk-activate', [ProviderInsuranceController::class, 'bulkActivate'])->name('bulk-activate');
    //     Route::post('/bulk-deactivate', [ProviderInsuranceController::class, 'bulkDeactivate'])->name('bulk-deactivate');

    //     // Provider insurance-specific routes
    //     Route::prefix('{providerInsurance}')->group(function () {
    //         // Show provider insurance
    //         Route::get('/', [ProviderInsuranceController::class, 'show'])->name('show');

    //         // Update provider insurance (full update)
    //         Route::put('/', [ProviderInsuranceController::class, 'update'])->name('update');

    //         // Update provider insurance (partial update)
    //         Route::patch('/', [ProviderInsuranceController::class, 'update'])->name('update.partial');

    //         // Delete provider insurance
    //         Route::delete('/', [ProviderInsuranceController::class, 'destroy'])->name('destroy');

    //         // Status management
    //         Route::post('/activate', [ProviderInsuranceController::class, 'activate'])->name('activate');
    //         Route::post('/deactivate', [ProviderInsuranceController::class, 'deactivate'])->name('deactivate');
    //         Route::post('/expire', [ProviderInsuranceController::class, 'expire'])->name('expire');
    //         Route::post('/cancel', [ProviderInsuranceController::class, 'cancel'])->name('cancel');
    //         Route::post('/suspend', [ProviderInsuranceController::class, 'suspend'])->name('suspend');

    //         // Verification management
    //         Route::post('/verify', [ProviderInsuranceController::class, 'verify'])->name('verify');
    //         Route::post('/reject', [ProviderInsuranceController::class, 'reject'])->name('reject');
    //         Route::post('/unverify', [ProviderInsuranceController::class, 'unverify'])->name('unverify');

    //         // Renewal management
    //         Route::post('/renew', [ProviderInsuranceController::class, 'renew'])->name('renew');

    //         // Document management
    //         Route::post('/documents/upload', [ProviderInsuranceController::class, 'uploadDocument'])->name('documents.upload');
    //         Route::delete('/documents/{document}', [ProviderInsuranceController::class, 'removeDocument'])->name('documents.remove');
    //     });

    //     // Provider-specific insurance routes
    //     Route::prefix('providers/{provider}')->group(function () {
    //         Route::get('/', [ProviderInsuranceController::class, 'byProvider'])->name('by-provider');
    //         Route::get('/active', [ProviderInsuranceController::class, 'providerActiveInsurance'])->name('active');
    //         Route::get('/expired', [ProviderInsuranceController::class, 'providerExpiredInsurance'])->name('expired');
    //         Route::get('/expiring-soon', [ProviderInsuranceController::class, 'providerExpiringSoonInsurance'])->name('expiring-soon');
    //         Route::get('/pending-verification', [ProviderInsuranceController::class, 'providerPendingVerificationInsurance'])->name('pending-verification');
    //         Route::get('/compliance', [ProviderInsuranceController::class, 'providerCompliance'])->name('compliance');
    //     });
    // });

    // // Employee Performance Review API routes
    // Route::prefix('employee-performance-reviews')->name('employee-performance-reviews.')->middleware(['permission:employee-performance-reviews.*'])->group(function () {
    //     // List performance reviews
    //     Route::get('/', [\Fereydooni\Shopping\app\Http\Controllers\Api\EmployeePerformanceReviewController::class, 'index'])->name('index');

    //     // Create performance review
    //     Route::post('/', [\Fereydooni\Shopping\app\Http\Controllers\Api\EmployeePerformanceReviewController::class, 'store'])->name('store');

    //     // Search performance reviews
    //     Route::get('/search', [\Fereydooni\Shopping\app\Http\Controllers\Api\EmployeePerformanceReviewController::class, 'search'])->name('search');

    //     // Get pending approval reviews
    //     Route::get('/pending-approval', [\Fereydooni\Shopping\app\Http\Controllers\Api\EmployeePerformanceReviewController::class, 'pendingApproval'])->name('pending-approval');

    //     // Get overdue reviews
    //     Route::get('/overdue', [\Fereydooni\Shopping\app\Http\Controllers\Api\EmployeePerformanceReviewController::class, 'overdue'])->name('overdue');

    //     // Get upcoming reviews
    //     Route::get('/upcoming', [\Fereydooni\Shopping\app\Http\Controllers\Api\EmployeePerformanceReviewController::class, 'upcoming'])->name('upcoming');

    //     // Get review statistics
    //     Route::get('/statistics', [\Fereydooni\Shopping\app\Http\Controllers\Api\EmployeePerformanceReviewController::class, 'statistics'])->name('statistics');

    //     // Generate performance reports
    //     Route::get('/reports', [\Fereydooni\Shopping\app\Http\Controllers\Api\EmployeePerformanceReviewController::class, 'reports'])->name('reports');

    //     // Export performance reviews
    //     Route::post('/export', [\Fereydooni\Shopping\app\Http\Controllers\Api\EmployeePerformanceReviewController::class, 'export'])->name('export');

    //     // Import performance reviews
    //     Route::post('/import', [\Fereydooni\Shopping\app\Http\Controllers\Api\EmployeePerformanceReviewController::class, 'import'])->name('import');

    //     // Bulk operations
    //     Route::post('/bulk-approve', [\Fereydooni\Shopping\app\Http\Controllers\Api\EmployeePerformanceReviewController::class, 'bulkApprove'])->name('bulk-approve');
    //     Route::post('/bulk-reject', [\Fereydooni\Shopping\app\Http\Controllers\Api\EmployeePerformanceReviewController::class, 'bulkReject'])->name('bulk-reject');

    //     // Send review reminders
    //     Route::post('/send-reminders', [\Fereydooni\Shopping\app\Http\Controllers\Api\EmployeePerformanceReviewController::class, 'sendReminders'])->name('send-reminders');

    //     // Performance review-specific routes
    //     Route::prefix('{review}')->group(function () {
    //         // Show performance review
    //         Route::get('/', [\Fereydooni\Shopping\app\Http\Controllers\Api\EmployeePerformanceReviewController::class, 'show'])->name('show');

    //         // Update performance review
    //         Route::put('/', [\Fereydooni\Shopping\app\Http\Controllers\Api\EmployeePerformanceReviewController::class, 'update'])->name('update');

    //         // Delete performance review
    //         Route::delete('/', [\Fereydooni\Shopping\app\Http\Controllers\Api\EmployeePerformanceReviewController::class, 'destroy'])->name('destroy');

    //         // Submit review for approval
    //         Route::post('/submit', [\Fereydooni\Shopping\app\Http\Controllers\Api\EmployeePerformanceReviewController::class, 'submit'])->name('submit');

    //         // Approve review
    //         Route::post('/approve', [\Fereydooni\Shopping\app\Http\Controllers\Api\EmployeePerformanceReviewController::class, 'approve'])->name('approve');

    //         // Reject review
    //         Route::post('/reject', [\Fereydooni\Shopping\app\Http\Controllers\Api\EmployeePerformanceReviewController::class, 'reject'])->name('reject');

    //         // Assign reviewer
    //         Route::post('/assign-reviewer', [\Fereydooni\Shopping\app\Http\Controllers\Api\EmployeePerformanceReviewController::class, 'assignReviewer'])->name('assign-reviewer');

    //         // Schedule review
    //         Route::post('/schedule', [\Fereydooni\Shopping\app\Http\Controllers\Api\EmployeePerformanceReviewController::class, 'schedule'])->name('schedule');
    //     });

    //     // Employee-specific performance review routes
    //     Route::prefix('employees/{employee}')->group(function () {
    //         // Get employee performance reviews
    //         Route::get('/performance-reviews', [\Fereydooni\Shopping\app\Http\Controllers\Api\EmployeePerformanceReviewController::class, 'employeeReviews'])->name('employee-reviews');

    //         // Create performance review for employee
    //         Route::post('/performance-reviews', [\Fereydooni\Shopping\app\Http\Controllers\Api\EmployeePerformanceReviewController::class, 'storeForEmployee'])->name('store-for-employee');

    //         // Get employee review statistics
    //         Route::get('/performance-reviews/statistics', [\Fereydooni\Shopping\app\Http\Controllers\Api\EmployeePerformanceReviewController::class, 'employeeStatistics'])->name('employee-statistics');
    //     });

    //     // Department-specific performance review routes
    //     Route::prefix('departments/{department}')->group(function () {
    //         // Get department review statistics
    //         Route::get('/performance-reviews/statistics', [\Fereydooni\Shopping\app\Http\Controllers\Api\EmployeePerformanceReviewController::class, 'departmentStatistics'])->name('department-statistics');
    //     });
    // });

    // // Provider Location API routes
    // Route::prefix('provider-locations')->name('provider-locations.')->group(function () {
    //     // List provider locations
    //     Route::get('/', [ProviderLocationController::class, 'index'])->name('index');

    //     // Get provider location count
    //     Route::get('/count', [ProviderLocationController::class, 'getCount'])->name('count');

    //     // Search provider locations
    //     Route::get('/search', [ProviderLocationController::class, 'search'])->name('search');

    //     // Filter by status
    //     Route::get('/active', [ProviderLocationController::class, 'getActive'])->name('active');
    //     Route::get('/inactive', [ProviderLocationController::class, 'getInactive'])->name('inactive');

    //     // Get provider location statistics
    //     Route::get('/stats', [ProviderLocationController::class, 'getStats'])->name('stats');

    //     // Create provider location
    //     Route::post('/', [ProviderLocationController::class, 'store'])->name('store');

    //     // Provider location-specific routes
    //     Route::prefix('{providerLocation}')->group(function () {
    //         // Show provider location
    //         Route::get('/', [ProviderLocationController::class, 'show'])->name('show');

    //         // Update provider location (full update)
    //         Route::put('/', [ProviderLocationController::class, 'update'])->name('update');

    //         // Update provider location (partial update)
    //         Route::patch('/', [ProviderLocationController::class, 'update'])->name('update.partial');

    //         // Delete provider location
    //         Route::delete('/', [ProviderLocationController::class, 'destroy'])->name('destroy');

    //         // Status management
    //         Route::post('/activate', [ProviderLocationController::class, 'activate'])->name('activate');
    //         Route::post('/deactivate', [ProviderLocationController::class, 'deactivate'])->name('deactivate');

    //         // Coordinates management
    //         Route::put('/coordinates', [ProviderLocationController::class, 'updateCoordinates'])->name('update-coordinates');
    //         Route::post('/geocode', [ProviderLocationController::class, 'geocode'])->name('geocode');

    //         // Operating hours management
    //         Route::put('/operating-hours', [ProviderLocationController::class, 'updateOperatingHours'])->name('update-operating-hours');

    //         // Search and filter
    //         Route::get('/search', [ProviderLocationController::class, 'search'])->name('search');
    //         Route::post('/search', [ProviderLocationController::class, 'search'])->name('search.post');
    //         Route::get('/filter', [ProviderLocationController::class, 'filter'])->name('filter');
    //         Route::post('/filter', [ProviderLocationController::class, 'filter'])->name('filter.post');

    //         // Geocoding
    //         Route::post('/geocode', [ProviderLocationController::class, 'geocodeAddress'])->name('geocode-address');
    //         Route::post('/batch-geocode', [ProviderLocationController::class, 'batchGeocode'])->name('batch-geocode');

    //         // Analytics
    //         Route::get('/analytics', [ProviderLocationController::class, 'analytics'])->name('analytics');
    //         Route::get('/analytics/overview', [ProviderLocationController::class, 'analyticsOverview'])->name('analytics.overview');
    //         Route::get('/analytics/geographic', [ProviderLocationController::class, 'geographicAnalytics'])->name('analytics.geographic');
    //         Route::get('/analytics/types', [ProviderLocationController::class, 'typeAnalytics'])->name('analytics.types');
    //         Route::get('/analytics/performance', [ProviderLocationController::class, 'performanceAnalytics'])->name('analytics.performance');

    //         // Map data
    //         Route::get('/map', [ProviderLocationController::class, 'mapData'])->name('map');
    //         Route::get('/map/overview', [ProviderLocationController::class, 'mapOverview'])->name('map.overview');
    //         Route::get('/map/clusters', [ProviderLocationController::class, 'mapClusters'])->name('map.clusters');
    //         Route::get('/map/bounds', [ProviderLocationController::class, 'mapBounds'])->name('map.bounds');

    //         // Nearby locations
    //         Route::get('/nearby', [ProviderLocationController::class, 'nearby'])->name('nearby');
    //         Route::get('/nearby/coordinates', [ProviderLocationController::class, 'nearbyByCoordinates'])->name('nearby.coordinates');
    //         Route::get('/nearby/address', [ProviderLocationController::class, 'nearbyByAddress'])->name('nearby.address');
    //         Route::get('/nearby/radius/{radius}', [ProviderLocationController::class, 'nearbyByRadius'])->name('nearby.radius');

    //         // Provider-specific
    //         Route::get('/provider/{provider}', [ProviderLocationController::class, 'byProvider'])->name('by-provider');
    //         Route::get('/provider/{provider}/primary', [ProviderLocationController::class, 'providerPrimary'])->name('provider.primary');
    //         Route::get('/provider/{provider}/active', [ProviderLocationController::class, 'providerActive'])->name('provider.active');
    //         Route::get('/provider/{provider}/inactive', [ProviderLocationController::class, 'providerInactive'])->name('provider.inactive');
    //         Route::get('/provider/{provider}/by-type/{type}', [ProviderLocationController::class, 'providerByType'])->name('provider.by-type');
    //         Route::get('/provider/{provider}/by-country/{country}', [ProviderLocationController::class, 'providerByCountry'])->name('provider.by-country');
    //         Route::get('/provider/{provider}/by-state/{state}', [ProviderLocationController::class, 'providerByState'])->name('provider.by-state');
    //         Route::get('/provider/{provider}/by-city/{city}', [ProviderLocationController::class, 'providerByCity'])->name('provider.by-city');

    //         // Geographic data
    //         Route::get('/geographic/countries', [ProviderLocationController::class, 'countries'])->name('geographic.countries');
    //         Route::get('/geographic/states/{country}', [ProviderLocationController::class, 'states'])->name('geographic.states');
    //         Route::get('/geographic/cities/{state}', [ProviderLocationController::class, 'cities'])->name('geographic.cities');
    //         Route::get('/geographic/postal-codes/{city}', [ProviderLocationController::class, 'postalCodes'])->name('geographic.postal-codes');

    //         // Location types
    //         Route::get('/types', [ProviderLocationController::class, 'locationTypes'])->name('types');
    //         Route::get('/types/{type}', [ProviderLocationController::class, 'byType'])->name('by-type');
    //         Route::get('/types/{type}/analytics', [ProviderLocationController::class, 'typeAnalytics'])->name('type.analytics');

    //         // Time-based queries
    //         Route::get('/time/now-open', [ProviderLocationController::class, 'nowOpen'])->name('time.now-open');
    //         Route::get('/time/open-now', [ProviderLocationController::class, 'openNow'])->name('time.open-now');
    //         Route::get('/time/by-day/{day}', [ProviderLocationController::class, 'byDay'])->name('time.by-day');
    //         Route::get('/time/by-time/{time}', [ProviderLocationController::class, 'byTime'])->name('time.by-time');

    //         // Bulk operations
    //         Route::post('/bulk/activate', [ProviderLocationController::class, 'bulkActivate'])->name('bulk.activate');
    //         Route::post('/bulk/deactivate', [ProviderLocationController::class, 'bulkDeactivate'])->name('bulk.deactivate');
    //         Route::post('/bulk/delete', [ProviderLocationController::class, 'bulkDelete'])->name('bulk.delete');
    //         Route::post('/bulk/geocode', [ProviderLocationController::class, 'bulkGeocode'])->name('bulk.geocode');
    //         Route::post('/bulk/update', [ProviderLocationController::class, 'bulkUpdate'])->name('bulk.update');

    //         // Export/import
    //         Route::get('/export', [ProviderLocationController::class, 'export'])->name('export');
    //         Route::post('/import', [ProviderLocationController::class, 'import'])->name('import');
    //         Route::get('/import/template', [ProviderLocationController::class, 'importTemplate'])->name('import.template');

    //         // Statistics and reports
    //         Route::get('/statistics', [ProviderLocationController::class, 'statistics'])->name('statistics');
    //         Route::get('/statistics/summary', [ProviderLocationController::class, 'statisticsSummary'])->name('statistics.summary');
    //         Route::get('/statistics/geographic', [ProviderLocationController::class, 'statisticsGeographic'])->name('statistics.geographic');
    //         Route::get('/statistics/types', [ProviderLocationController::class, 'statisticsTypes'])->name('statistics.types');
    //         Route::get('/statistics/performance', [ProviderLocationController::class, 'statisticsPerformance'])->name('statistics.performance');

    //         // Validation and verification
    //         Route::post('/{providerLocation}/verify', [ProviderLocationController::class, 'verify'])->name('verify');
    //         Route::post('/{providerLocation}/validate', [ProviderLocationController::class, 'validate'])->name('validate');
    //         Route::get('/{providerLocation}/validation-status', [ProviderLocationController::class, 'validationStatus'])->name('validation-status');

    //         // Contact information
    //         Route::get('/{providerLocation}/contact', [ProviderLocationController::class, 'contactInfo'])->name('contact');
    //         Route::put('/{providerLocation}/contact', [ProviderLocationController::class, 'updateContactInfo'])->name('contact.update');

    //         // History and audit
    //         Route::get('/{providerLocation}/history', [ProviderLocationController::class, 'history'])->name('history');
    //         Route::get('/{providerLocation}/audit', [ProviderLocationController::class, 'audit'])->name('audit');
    //         Route::get('/{providerLocation}/changes', [ProviderLocationController::class, 'changes'])->name('changes');

    //         // Settings and configuration
    //         Route::get('/settings', [ProviderLocationController::class, 'settings'])->name('settings');
    //         Route::put('/settings', [ProviderLocationController::class, 'updateSettings'])->name('settings.update');
    //         Route::get('/settings/geocoding', [ProviderLocationController::class, 'geocodingSettings'])->name('settings.geocoding');
    //         Route::put('/settings/geocoding', [ProviderLocationController::class, 'updateGeocodingSettings'])->name('settings.geocoding.update');

    //         // Advanced search and queries
    //         Route::post('/advanced-search', [ProviderLocationController::class, 'advancedSearch'])->name('advanced-search');
    //         Route::post('/geospatial-search', [ProviderLocationController::class, 'geospatialSearch'])->name('geospatial-search');
    //         Route::post('/fulltext-search', [ProviderLocationController::class, 'fulltextSearch'])->name('fulltext-search');

    //         // Pagination and cursor-based navigation
    //         Route::get('/cursor-paginate', [ProviderLocationController::class, 'cursorPaginate'])->name('cursor-paginate');
    //         Route::get('/simple-paginate', [ProviderLocationController::class, 'simplePaginate'])->name('simple-paginate');

    //         // Relationship data
    //         Route::get('/{providerLocation}/provider', [ProviderLocationController::class, 'provider'])->name('provider');
    //         Route::get('/{providerLocation}/related', [ProviderLocationController::class, 'related'])->name('related');
    //         Route::get('/{providerLocation}/similar', [ProviderLocationController::class, 'similar'])->name('similar');

    //         // Performance and metrics
    //         Route::get('/performance', [ProviderLocationController::class, 'performance'])->name('performance');
    //         Route::get('/performance/metrics', [ProviderLocationController::class, 'performanceMetrics'])->name('performance.metrics');
    //         Route::get('/performance/trends', [ProviderLocationController::class, 'performanceTrends'])->name('performance.trends');

    //         // System and health
    //         Route::get('/system/health', [ProviderLocationController::class, 'systemHealth'])->name('system.health');
    //         Route::get('/system/status', [ProviderLocationController::class, 'systemStatus'])->name('system.status');
    //         Route::get('/system/info', [ProviderLocationController::class, 'systemInfo'])->name('system.info');
    //     });
    // });
    //     });

    // // Provider Performance API routes
    // Route::prefix('provider-performances')->name('provider-performances.')->group(function () {
    //     // List provider performances
    //     Route::get('/', [ProviderPerformanceController::class, 'index'])->name('index');

    //     // Search provider performances
    //     Route::get('/search', [ProviderPerformanceController::class, 'search'])->name('search');

    //     // Create provider performance
    //     Route::post('/', [ProviderPerformanceController::class, 'store'])->name('store');

    //     // Get performance analytics
    //     Route::get('/analytics', [ProviderPerformanceController::class, 'analytics'])->name('analytics');

    //     // Generate performance report
    //     Route::post('/reports', [ProviderPerformanceController::class, 'generateReport'])->name('reports');

    //     // Get top performers
    //     Route::get('/top-performers', [ProviderPerformanceController::class, 'topPerformers'])->name('top-performers');

    //     // Get performance trends
    //     Route::get('/trends', [ProviderPerformanceController::class, 'trends'])->name('trends');

    //     // Get performance alerts
    //     Route::get('/alerts', [ProviderPerformanceController::class, 'alerts'])->name('alerts');

    //     // Get performances by provider
    //     Route::get('/provider/{providerId}', [ProviderPerformanceController::class, 'byProvider'])->name('by-provider');

    //     // Get performances by grade
    //     Route::get('/grade/{grade}', [ProviderPerformanceController::class, 'byGrade'])->name('by-grade');

    //     // Get performances by period
    //     Route::get('/period', [ProviderPerformanceController::class, 'byPeriod'])->name('by-period');

    //     // Provider performance-specific routes
    //     Route::prefix('{providerPerformance}')->group(function () {
    //         // Show provider performance
    //         Route::get('/', [ProviderPerformanceController::class, 'show'])->name('show');

    //         // Update provider performance
    //         Route::put('/', [ProviderPerformanceController::class, 'update'])->name('update');

    //         // Delete provider performance
    //         Route::delete('/', [ProviderPerformanceController::class, 'destroy'])->name('destroy');

    //         // Verify performance
    //         Route::post('/verify', [ProviderPerformanceController::class, 'verify'])->name('verify');

    //         // Unverify performance
    //         Route::post('/unverify', [ProviderPerformanceController::class, 'unverify'])->name('unverify');

    //         // Calculate performance
    //         Route::post('/calculate', [ProviderPerformanceController::class, 'calculate'])->name('calculate');
    //     });
    // });

    // // Provider Communication API Routes
    // Route::prefix('provider-communications')->name('provider-communications.')->group(function () {
    //     // List provider communications
    //     Route::get('/', [\Fereydooni\Shopping\app\Http\Controllers\Api\V1\ProviderCommunicationController::class, 'index'])->name('index');

    //     // Search communications
    //     Route::get('/search', [\Fereydooni\Shopping\app\Http\Controllers\Api\V1\ProviderCommunicationController::class, 'search'])->name('search');

    //     // Create communication
    //     Route::post('/', [\Fereydooni\Shopping\app\Http\Controllers\Api\V1\ProviderCommunicationController::class, 'store'])->name('store');

    //     // Send communication
    //     Route::post('/send', [\Fereydooni\Shopping\app\Http\Controllers\Api\V1\ProviderCommunicationController::class, 'send'])->name('send');

    //     // Get analytics
    //     Route::get('/analytics', [\Fereydooni\Shopping\app\Http\Controllers\Api\V1\ProviderCommunicationController::class, 'analytics'])->name('analytics');

    //     // Get statistics
    //     Route::get('/statistics', [\Fereydooni\Shopping\app\Http\Controllers\Api\V1\ProviderCommunicationController::class, 'statistics'])->name('statistics');

    //     // Get communications by provider
    //     Route::get('/provider/{providerId}', [\Fereydooni\Shopping\app\Http\Controllers\Api\V1\ProviderCommunicationController::class, 'byProvider'])->name('by-provider');

    //     // Get communications by type
    //     Route::get('/type/{type}', [\Fereydooni\Shopping\app\Http\Controllers\Api\V1\ProviderCommunicationController::class, 'byType'])->name('by-type');

    //     // Get urgent communications
    //     Route::get('/urgent', [\Fereydooni\Shopping\app\Http\Controllers\Api\V1\ProviderCommunicationController::class, 'urgent'])->name('urgent');

    //     // Get unread communications
    //     Route::get('/unread', [\Fereydooni\Shopping\app\Http\Controllers\Api\V1\ProviderCommunicationController::class, 'unread'])->name('unread');

    //     // Get unreplied communications
    //     Route::get('/unreplied', [\Fereydooni\Shopping\app\Http\Controllers\Api\V1\ProviderCommunicationController::class, 'unreplied'])->name('unreplied');

    //     // Get thread
    //     Route::get('/thread/{threadId}', [\Fereydooni\Shopping\app\Http\Controllers\Api\V1\ProviderCommunicationController::class, 'thread'])->name('thread');

    //     // Get conversation
    //     Route::get('/conversation/{providerId}', [\Fereydooni\Shopping\app\Http\Controllers\Api\V1\ProviderCommunicationController::class, 'conversation'])->name('conversation');

    //     // Provider communication-specific routes
    //     Route::prefix('{providerCommunication}')->group(function () {
    //         // Show communication
    //         Route::get('/', [\Fereydooni\Shopping\app\Http\Controllers\Api\V1\ProviderCommunicationController::class, 'show'])->name('show');

    //         // Update communication
    //         Route::put('/', [\Fereydooni\Shopping\app\Http\Controllers\Api\V1\ProviderCommunicationController::class, 'update'])->name('update');

    //         // Delete communication
    //         Route::delete('/', [\Fereydooni\Shopping\app\Http\Controllers\Api\V1\ProviderCommunicationController::class, 'destroy'])->name('destroy');

    //         // Reply to communication
    //         Route::post('/reply', [\Fereydooni\Shopping\app\Http\Controllers\Api\V1\ProviderCommunicationController::class, 'reply'])->name('reply');

    //         // Mark as read
    //         Route::patch('/mark-read', [\Fereydooni\Shopping\app\Http\Controllers\Api\V1\ProviderCommunicationController::class, 'markAsRead'])->name('mark-read');

    //         // Mark as replied
    //         Route::patch('/mark-replied', [\Fereydooni\Shopping\app\Http\Controllers\Api\V1\ProviderCommunicationController::class, 'markAsReplied'])->name('mark-replied');

    //         // Archive communication
    //         Route::patch('/archive', [\Fereydooni\Shopping\app\Http\Controllers\Api\V1\ProviderCommunicationController::class, 'archive'])->name('archive');

    //         // Unarchive communication
    //         Route::patch('/unarchive', [\Fereydooni\Shopping\app\Http\Controllers\Api\V1\ProviderCommunicationController::class, 'unarchive'])->name('unarchive');

    //         // Set urgent
    //         Route::patch('/urgent', [\Fereydooni\Shopping\app\Http\Controllers\Api\V1\ProviderCommunicationController::class, 'setUrgent'])->name('urgent');

    //         // Unset urgent
    //         Route::patch('/unurgent', [\Fereydooni\Shopping\app\Http\Controllers\Api\V1\ProviderCommunicationController::class, 'unsetUrgent'])->name('unurgent');
    //     });
    // });

    // // EmployeeNote API Routes

    // Route::prefix('api')->group(function () {
    // Route::apiResource('employee-notes', EmployeeNoteController::class);

    // Route::get('employees/{employee}/notes', [EmployeeNoteController::class, 'employeeNotes']);
    // Route::get('employee-notes/search', [EmployeeNoteController::class, 'search']);
    // Route::post('employee-notes/{employeeNote}/archive', [EmployeeNoteController::class, 'archive']);
    // Route::post('employee-notes/{employeeNote}/unarchive', [EmployeeNoteController::class, 'unarchive']);
});
