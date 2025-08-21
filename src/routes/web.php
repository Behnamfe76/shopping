<?php

use Illuminate\Support\Facades\Route;
use Fereydooni\Shopping\app\Http\Controllers\Web\ProductController as WebProductController;
use Fereydooni\Shopping\app\Http\Controllers\Web\CategoryController as WebCategoryController;
use Fereydooni\Shopping\app\Http\Controllers\Web\OrderController as WebOrderController;
use Fereydooni\Shopping\app\Http\Controllers\Web\CartController as WebCartController;
use Fereydooni\Shopping\app\Http\Controllers\Web\CustomerController as WebCustomerController;
use Fereydooni\Shopping\app\Http\Controllers\Web\CustomerPreferenceController as WebCustomerPreferenceController;
use Fereydooni\Shopping\app\Http\Controllers\Web\CustomerNoteController as WebCustomerNoteController;
use Fereydooni\Shopping\app\Http\Controllers\Web\LoyaltyTransactionController as WebLoyaltyTransactionController;
use Fereydooni\Shopping\app\Http\Controllers\Web\CustomerSegmentController as WebCustomerSegmentController;

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

    // Customer management routes (authenticated)
    Route::prefix('customers')->name('customers.')->middleware(['auth'])->group(function () {
        // Customer listing and search
        Route::get('/', [WebCustomerController::class, 'index'])->name('index');
        Route::get('/search', [WebCustomerController::class, 'search'])->name('search');

        // Customer CRUD
        Route::get('/create', [WebCustomerController::class, 'create'])->name('create');
        Route::post('/', [WebCustomerController::class, 'store'])->name('store');
        Route::get('/{customer}', [WebCustomerController::class, 'show'])->name('show');
        Route::get('/{customer}/edit', [WebCustomerController::class, 'edit'])->name('edit');
        Route::put('/{customer}', [WebCustomerController::class, 'update'])->name('update');
        Route::delete('/{customer}', [WebCustomerController::class, 'destroy'])->name('destroy');

        // Customer status management
        Route::post('/{customer}/activate', [WebCustomerController::class, 'activate'])->name('activate');
        Route::post('/{customer}/deactivate', [WebCustomerController::class, 'deactivate'])->name('deactivate');
        Route::post('/{customer}/suspend', [WebCustomerController::class, 'suspend'])->name('suspend');

        // Customer loyalty management
        Route::post('/{customer}/loyalty/add', [WebCustomerController::class, 'addLoyaltyPoints'])->name('loyalty.add');
        Route::post('/{customer}/loyalty/deduct', [WebCustomerController::class, 'deductLoyaltyPoints'])->name('loyalty.deduct');

        // Customer analytics and management
        Route::get('/dashboard/analytics', [WebCustomerController::class, 'dashboard'])->name('dashboard');
        Route::get('/import-export', [WebCustomerController::class, 'importExport'])->name('import-export');
        Route::post('/import', [WebCustomerController::class, 'import'])->name('import');
        Route::get('/export', [WebCustomerController::class, 'export'])->name('export');
        Route::get('/communication', [WebCustomerController::class, 'communication'])->name('communication');
        Route::get('/loyalty', [WebCustomerController::class, 'loyalty'])->name('loyalty');
        Route::get('/segmentation', [WebCustomerController::class, 'segmentation'])->name('segmentation');
    });

    // Customer Segment management routes (authenticated)
    Route::prefix('customer-segments')->name('customer-segments.')->middleware(['auth', 'permission:customer-segments.*'])->group(function () {
        // Customer segment dashboard
        Route::get('/dashboard', [WebCustomerSegmentController::class, 'dashboard'])->name('dashboard');

        // Customer segment listing and search
        Route::get('/', [WebCustomerSegmentController::class, 'index'])->name('index');

        // Customer segment CRUD
        Route::get('/create', [WebCustomerSegmentController::class, 'create'])->name('create');
        Route::post('/', [WebCustomerSegmentController::class, 'store'])->name('store');
        Route::get('/{customerSegment}', [WebCustomerSegmentController::class, 'show'])->name('show');
        Route::get('/{customerSegment}/edit', [WebCustomerSegmentController::class, 'edit'])->name('edit');
        Route::put('/{customerSegment}', [WebCustomerSegmentController::class, 'update'])->name('update');
        Route::delete('/{customerSegment}', [WebCustomerSegmentController::class, 'destroy'])->name('destroy');

        // Customer segment status management
        Route::post('/{customerSegment}/activate', [WebCustomerSegmentController::class, 'activate'])->name('activate');
        Route::post('/{customerSegment}/deactivate', [WebCustomerSegmentController::class, 'deactivate'])->name('deactivate');

        // Customer segment criteria and calculation
        Route::get('/{customerSegment}/criteria-builder', [WebCustomerSegmentController::class, 'criteriaBuilder'])->name('criteria-builder');
        Route::put('/{customerSegment}/criteria', [WebCustomerSegmentController::class, 'updateCriteria'])->name('update-criteria');
        Route::post('/{customerSegment}/calculate', [WebCustomerSegmentController::class, 'calculate'])->name('calculate');

        // Customer segment analytics
        Route::get('/analytics', [WebCustomerSegmentController::class, 'analytics'])->name('analytics');
        Route::get('/compare', [WebCustomerSegmentController::class, 'compare'])->name('compare');

        // Customer segment import/export
        Route::get('/import-export', [WebCustomerSegmentController::class, 'importExport'])->name('import-export');
        Route::post('/import', [WebCustomerSegmentController::class, 'import'])->name('import');
        Route::get('/{customerSegment}/export', [WebCustomerSegmentController::class, 'export'])->name('export');

        // Customer segment performance
        Route::get('/{customerSegment}/performance', [WebCustomerSegmentController::class, 'performance'])->name('performance');

        // Customer segment operations
        Route::post('/{customerSegment}/duplicate', [WebCustomerSegmentController::class, 'duplicate'])->name('duplicate');
        Route::get('/merge', [WebCustomerSegmentController::class, 'mergeForm'])->name('merge-form');
        Route::post('/merge', [WebCustomerSegmentController::class, 'merge'])->name('merge');
        Route::get('/{customerSegment}/split', [WebCustomerSegmentController::class, 'splitForm'])->name('split-form');
        Route::post('/{customerSegment}/split', [WebCustomerSegmentController::class, 'split'])->name('split');

        // Recalculate all segments
        Route::post('/recalculate-all', [WebCustomerSegmentController::class, 'recalculateAll'])->name('recalculate-all');
    });

    // Customer Preference management routes (authenticated)
    Route::prefix('customer-preferences')->name('customer-preferences.')->middleware(['auth', 'permission:customer-preferences.*'])->group(function () {
        // Customer preference dashboard
        Route::get('/', [WebCustomerPreferenceController::class, 'index'])->name('index');

        // Customer preference CRUD
        Route::get('/create', [WebCustomerPreferenceController::class, 'create'])->name('create');
        Route::post('/', [WebCustomerPreferenceController::class, 'store'])->name('store');
        Route::get('/{preference}', [WebCustomerPreferenceController::class, 'show'])->name('show');
        Route::get('/{preference}/edit', [WebCustomerPreferenceController::class, 'edit'])->name('edit');
        Route::put('/{preference}', [WebCustomerPreferenceController::class, 'update'])->name('update');
        Route::delete('/{preference}', [WebCustomerPreferenceController::class, 'destroy'])->name('destroy');

        // Status management
        Route::post('/{preference}/activate', [WebCustomerPreferenceController::class, 'activate'])->name('activate');
        Route::post('/{preference}/deactivate', [WebCustomerPreferenceController::class, 'deactivate'])->name('deactivate');

        // Preference management interface
        Route::get('/list', [WebCustomerPreferenceController::class, 'list'])->name('list');
        Route::get('/templates', [WebCustomerPreferenceController::class, 'templates'])->name('templates');
        Route::get('/analytics', [WebCustomerPreferenceController::class, 'analytics'])->name('analytics');
        Route::get('/settings', [WebCustomerPreferenceController::class, 'settings'])->name('settings');

        // Customer-specific preference routes
        Route::prefix('customers/{customer}')->group(function () {
            Route::get('/manage', [WebCustomerPreferenceController::class, 'manage'])->name('manage');
            Route::get('/import-export', [WebCustomerPreferenceController::class, 'importExport'])->name('import-export');
            Route::post('/import', [WebCustomerPreferenceController::class, 'import'])->name('import');
            Route::get('/export', [WebCustomerPreferenceController::class, 'export'])->name('export');
            Route::post('/apply-template', [WebCustomerPreferenceController::class, 'applyTemplate'])->name('apply-template');
            Route::get('/backup-restore', [WebCustomerPreferenceController::class, 'backupRestore'])->name('backup-restore');
            Route::post('/restore', [WebCustomerPreferenceController::class, 'restore'])->name('restore');
            Route::post('/initialize', [WebCustomerPreferenceController::class, 'initialize'])->name('initialize');
        });
    });

    // Customer Note management routes (authenticated)
    Route::prefix('customer-notes')->name('customer-notes.')->middleware(['auth', 'permission:customer-notes.*'])->group(function () {
        // Customer note dashboard
        Route::get('/', [WebCustomerNoteController::class, 'index'])->name('index');

        // Customer note CRUD
        Route::get('/create', [WebCustomerNoteController::class, 'create'])->name('create');
        Route::post('/', [WebCustomerNoteController::class, 'store'])->name('store');
        Route::get('/{customerNote}', [WebCustomerNoteController::class, 'show'])->name('show');
        Route::get('/{customerNote}/edit', [WebCustomerNoteController::class, 'edit'])->name('edit');
        Route::put('/{customerNote}', [WebCustomerNoteController::class, 'update'])->name('update');
        Route::delete('/{customerNote}', [WebCustomerNoteController::class, 'destroy'])->name('destroy');

        // Status management
        Route::post('/{customerNote}/pin', [WebCustomerNoteController::class, 'pin'])->name('pin');
        Route::post('/{customerNote}/unpin', [WebCustomerNoteController::class, 'unpin'])->name('unpin');
        Route::post('/{customerNote}/make-private', [WebCustomerNoteController::class, 'makePrivate'])->name('make-private');
        Route::post('/{customerNote}/make-public', [WebCustomerNoteController::class, 'makePublic'])->name('make-public');

        // Tag management
        Route::post('/{customerNote}/tags', [WebCustomerNoteController::class, 'addTag'])->name('add-tag');
        Route::delete('/{customerNote}/tags/{tag}', [WebCustomerNoteController::class, 'removeTag'])->name('remove-tag');

        // Attachment management
        Route::post('/{customerNote}/attachments', [WebCustomerNoteController::class, 'addAttachment'])->name('add-attachment');
        Route::delete('/{customerNote}/attachments/{mediaId}', [WebCustomerNoteController::class, 'removeAttachment'])->name('remove-attachment');

        // Search and analytics
        Route::get('/search', [WebCustomerNoteController::class, 'search'])->name('search');
        Route::get('/stats', [WebCustomerNoteController::class, 'stats'])->name('stats');
        Route::get('/templates', [WebCustomerNoteController::class, 'templates'])->name('templates');

        // Customer-specific note routes
        Route::prefix('customers/{customerId}')->group(function () {
            Route::get('/notes', [WebCustomerNoteController::class, 'getCustomerNotes'])->name('customer-notes');
            Route::get('/notes/export', [WebCustomerNoteController::class, 'export'])->name('export');
            Route::get('/notes/import', [WebCustomerNoteController::class, 'showImport'])->name('show-import');
            Route::post('/notes/import', [WebCustomerNoteController::class, 'import'])->name('import');
        });
    });

    // Loyalty Transaction management routes (authenticated)
    Route::prefix('loyalty-transactions')->name('loyalty-transactions.')->middleware(['auth', 'permission:loyalty-transactions.*'])->group(function () {
        // Loyalty transaction dashboard
        Route::get('/dashboard', [WebLoyaltyTransactionController::class, 'dashboard'])->name('dashboard');

        // Loyalty transaction CRUD
        Route::get('/', [WebLoyaltyTransactionController::class, 'index'])->name('index');
        Route::get('/create', [WebLoyaltyTransactionController::class, 'create'])->name('create');
        Route::post('/', [WebLoyaltyTransactionController::class, 'store'])->name('store');
        Route::get('/{loyaltyTransaction}', [WebLoyaltyTransactionController::class, 'show'])->name('show');
        Route::get('/{loyaltyTransaction}/edit', [WebLoyaltyTransactionController::class, 'edit'])->name('edit');
        Route::put('/{loyaltyTransaction}', [WebLoyaltyTransactionController::class, 'update'])->name('update');
        Route::delete('/{loyaltyTransaction}', [WebLoyaltyTransactionController::class, 'destroy'])->name('destroy');

        // Points management
        Route::get('/points-management', [WebLoyaltyTransactionController::class, 'pointsManagement'])->name('points-management');
        Route::post('/add-points', [WebLoyaltyTransactionController::class, 'addPoints'])->name('add-points');
        Route::post('/deduct-points', [WebLoyaltyTransactionController::class, 'deductPoints'])->name('deduct-points');

        // Transaction reversal
        Route::get('/{loyaltyTransaction}/reverse', [WebLoyaltyTransactionController::class, 'showReverse'])->name('reverse.show');
        Route::post('/{loyaltyTransaction}/reverse', [WebLoyaltyTransactionController::class, 'reverse'])->name('reverse');

        // Analytics and reporting
        Route::get('/analytics', [WebLoyaltyTransactionController::class, 'analytics'])->name('analytics');

        // Import/Export
        Route::get('/import-export', [WebLoyaltyTransactionController::class, 'importExport'])->name('import-export');
        Route::post('/export-history', [WebLoyaltyTransactionController::class, 'exportHistory'])->name('export-history');
        Route::post('/import-history', [WebLoyaltyTransactionController::class, 'importHistory'])->name('import-history');

        // Tier management
        Route::get('/tier-management', [WebLoyaltyTransactionController::class, 'tierManagement'])->name('tier-management');

        // Expiration tracking
        Route::get('/expiration-tracking', [WebLoyaltyTransactionController::class, 'expirationTracking'])->name('expiration-tracking');

        // Search and filtering
        Route::get('/search', [WebLoyaltyTransactionController::class, 'index'])->name('search');
    });
});
