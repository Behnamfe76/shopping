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
use Fereydooni\Shopping\app\Http\Controllers\Web\CustomerCommunicationController as WebCustomerCommunicationController;
use Fereydooni\Shopping\app\Http\Controllers\Web\EmployeeController as WebEmployeeController;
use Fereydooni\Shopping\app\Http\Controllers\Web\ProviderController as WebProviderController;
use Fereydooni\Shopping\app\Http\Controllers\Web\ProviderLocationController;

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

    // Customer Communication management routes (authenticated)
    Route::prefix('customer-communications')->name('customer-communications.')->middleware(['auth', 'permission:customer-communications.*'])->group(function () {
        // Customer communication dashboard
        Route::get('/dashboard', [WebCustomerCommunicationController::class, 'dashboard'])->name('dashboard');

        // Customer communication CRUD
        Route::get('/', [WebCustomerCommunicationController::class, 'index'])->name('index');
        Route::get('/create', [WebCustomerCommunicationController::class, 'create'])->name('create');
        Route::post('/', [WebCustomerCommunicationController::class, 'store'])->name('store');
        Route::get('/{id}', [WebCustomerCommunicationController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [WebCustomerCommunicationController::class, 'edit'])->name('edit');
        Route::put('/{id}', [WebCustomerCommunicationController::class, 'update'])->name('update');
        Route::delete('/{id}', [WebCustomerCommunicationController::class, 'destroy'])->name('destroy');

        // Communication status management
        Route::get('/{id}/schedule', [WebCustomerCommunicationController::class, 'schedule'])->name('schedule');
        Route::post('/{id}/schedule', [WebCustomerCommunicationController::class, 'scheduleCommunication'])->name('schedule.store');
        Route::post('/{id}/send', [WebCustomerCommunicationController::class, 'send'])->name('send');
        Route::post('/{id}/cancel', [WebCustomerCommunicationController::class, 'cancel'])->name('cancel');

        // Communication tracking and analytics
        Route::get('/{id}/tracking', [WebCustomerCommunicationController::class, 'tracking'])->name('tracking');

        // Communication management
        Route::get('/templates', [WebCustomerCommunicationController::class, 'templates'])->name('templates');
        Route::get('/campaigns', [WebCustomerCommunicationController::class, 'campaigns'])->name('campaigns');
        Route::get('/import-export', [WebCustomerCommunicationController::class, 'importExport'])->name('import-export');
        Route::get('/reporting', [WebCustomerCommunicationController::class, 'reporting'])->name('reporting');

        // Attachment management
        Route::post('/{id}/attachments', [WebCustomerCommunicationController::class, 'addAttachment'])->name('add-attachment');
        Route::delete('/{id}/attachments/{mediaId}', [WebCustomerCommunicationController::class, 'removeAttachment'])->name('remove-attachment');
    });

    // Employee management routes (authenticated)
    Route::prefix('employees')->name('employees.')->middleware(['auth', 'permission:employees.*'])->group(function () {
        // Employee dashboard
        Route::get('/dashboard', [WebEmployeeController::class, 'dashboard'])->name('dashboard');

        // Employee CRUD
        Route::get('/', [WebEmployeeController::class, 'index'])->name('index');
        Route::get('/create', [WebEmployeeController::class, 'create'])->name('create');
        Route::post('/', [WebEmployeeController::class, 'store'])->name('store');
        Route::get('/{employee}', [WebEmployeeController::class, 'show'])->name('show');
        Route::get('/{employee}/edit', [WebEmployeeController::class, 'edit'])->name('edit');
        Route::put('/{employee}', [WebEmployeeController::class, 'update'])->name('update');
        Route::delete('/{employee}', [WebEmployeeController::class, 'destroy'])->name('destroy');

        // Employee status management
        Route::post('/{employee}/activate', [WebEmployeeController::class, 'activate'])->name('activate');
        Route::post('/{employee}/deactivate', [WebEmployeeController::class, 'deactivate'])->name('deactivate');
        Route::post('/{employee}/terminate', [WebEmployeeController::class, 'terminate'])->name('terminate');
        Route::post('/{employee}/rehire', [WebEmployeeController::class, 'rehire'])->name('rehire');

        // Employee analytics and management
        Route::get('/analytics', [WebEmployeeController::class, 'analytics'])->name('analytics');
        Route::get('/import-export', [WebEmployeeController::class, 'importExport'])->name('import-export');
        Route::post('/import', [WebEmployeeController::class, 'import'])->name('import');
        Route::get('/export', [WebEmployeeController::class, 'export'])->name('export');

        // Employee performance management
        Route::get('/performance', [WebEmployeeController::class, 'performance'])->name('performance');

        // Employee time-off management
        Route::get('/time-off', [WebEmployeeController::class, 'timeOff'])->name('time-off');

        // Employee benefits administration
        Route::get('/benefits', [WebEmployeeController::class, 'benefits'])->name('benefits');

        // Employee hierarchy visualization
        Route::get('/hierarchy', [WebEmployeeController::class, 'hierarchy'])->name('hierarchy');

        // Employee training management
        Route::get('/training', [WebEmployeeController::class, 'training'])->name('training');

        // AJAX endpoints
        Route::get('/{employee}/data', [WebEmployeeController::class, 'getEmployeeData'])->name('get-data');
        Route::get('/stats', [WebEmployeeController::class, 'getEmployeeStats'])->name('get-stats');
        Route::get('/search', [WebEmployeeController::class, 'search'])->name('search');
    });

    // Employee Performance Review management routes (authenticated)
    Route::prefix('employee-performance-reviews')->name('employee-performance-reviews.')->middleware(['auth', 'permission:employee-performance-reviews.*'])->group(function () {
        // Performance review dashboard
        Route::get('/dashboard', [\Fereydooni\Shopping\app\Http\Controllers\Web\EmployeePerformanceReviewController::class, 'index'])->name('dashboard');

        // Performance review CRUD
        Route::get('/', [\Fereydooni\Shopping\app\Http\Controllers\Web\EmployeePerformanceReviewController::class, 'index'])->name('index');
        Route::get('/create', [\Fereydooni\Shopping\app\Http\Controllers\Web\EmployeePerformanceReviewController::class, 'create'])->name('create');
        Route::post('/', [\Fereydooni\Shopping\app\Http\Controllers\Web\EmployeePerformanceReviewController::class, 'store'])->name('store');
        Route::get('/{id}', [\Fereydooni\Shopping\app\Http\Controllers\Web\EmployeePerformanceReviewController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [\Fereydooni\Shopping\app\Http\Controllers\Web\EmployeePerformanceReviewController::class, 'edit'])->name('edit');
        Route::put('/{id}', [\Fereydooni\Shopping\app\Http\Controllers\Web\EmployeePerformanceReviewController::class, 'update'])->name('update');
        Route::delete('/{id}', [\Fereydooni\Shopping\app\Http\Controllers\Web\EmployeePerformanceReviewController::class, 'destroy'])->name('destroy');

        // Performance review workflow
        Route::post('/{id}/submit', [\Fereydooni\Shopping\app\Http\Controllers\Web\EmployeePerformanceReviewController::class, 'submit'])->name('submit');
        Route::post('/{id}/approve', [\Fereydooni\Shopping\app\Http\Controllers\Web\EmployeePerformanceReviewController::class, 'approve'])->name('approve');
        Route::post('/{id}/reject', [\Fereydooni\Shopping\app\Http\Controllers\Web\EmployeePerformanceReviewController::class, 'reject'])->name('reject');
        Route::post('/{id}/assign-reviewer', [\Fereydooni\Shopping\app\Http\Controllers\Web\EmployeePerformanceReviewController::class, 'assignReviewer'])->name('assign-reviewer');
        Route::post('/{id}/schedule', [\Fereydooni\Shopping\app\Http\Controllers\Web\EmployeePerformanceReviewController::class, 'schedule'])->name('schedule');

        // Performance review management
        Route::get('/pending-approval', [\Fereydooni\Shopping\app\Http\Controllers\Web\EmployeePerformanceReviewController::class, 'pendingApproval'])->name('pending-approval');
        Route::get('/overdue', [\Fereydooni\Shopping\app\Http\Controllers\Web\EmployeePerformanceReviewController::class, 'overdue'])->name('overdue');
        Route::get('/upcoming', [\Fereydooni\Shopping\app\Http\Controllers\Web\EmployeePerformanceReviewController::class, 'upcoming'])->name('upcoming');

        // Performance review analytics and reporting
        Route::get('/statistics', [\Fereydooni\Shopping\app\Http\Controllers\Web\EmployeePerformanceReviewController::class, 'statistics'])->name('statistics');
        Route::get('/reports', [\Fereydooni\Shopping\app\Http\Controllers\Web\EmployeePerformanceReviewController::class, 'reports'])->name('reports');

        // Import/Export
        Route::get('/import-export', [\Fereydooni\Shopping\app\Http\Controllers\Web\EmployeePerformanceReviewController::class, 'index'])->name('import-export');
        Route::post('/export', [\Fereydooni\Shopping\app\Http\Controllers\Web\EmployeePerformanceReviewController::class, 'export'])->name('export');
        Route::post('/import', [\Fereydooni\Shopping\app\Http\Controllers\Web\EmployeePerformanceReviewController::class, 'import'])->name('import');

        // Bulk operations
        Route::post('/bulk-approve', [\Fereydooni\Shopping\app\Http\Controllers\Web\EmployeePerformanceReviewController::class, 'bulkApprove'])->name('bulk-approve');
        Route::post('/bulk-reject', [\Fereydooni\Shopping\app\Http\Controllers\Web\EmployeePerformanceReviewController::class, 'bulkReject'])->name('bulk-reject');

        // Review reminders
        Route::post('/send-reminders', [\Fereydooni\Shopping\app\Http\Controllers\Web\EmployeePerformanceReviewController::class, 'sendReminders'])->name('send-reminders');

        // Search functionality
        Route::get('/search', [\Fereydooni\Shopping\app\Http\Controllers\Web\EmployeePerformanceReviewController::class, 'search'])->name('search');
    });

    // Provider management routes (authenticated)
    Route::prefix('providers')->name('providers.')->middleware(['auth', 'permission:providers.*'])->group(function () {
        // Provider dashboard
        Route::get('/dashboard', [WebProviderController::class, 'dashboard'])->name('dashboard');

        // Provider CRUD
        Route::get('/', [WebProviderController::class, 'index'])->name('index');
        Route::get('/create', [WebProviderController::class, 'create'])->name('create');
        Route::post('/', [WebProviderController::class, 'store'])->name('store');
        Route::get('/{provider}', [WebProviderController::class, 'show'])->name('show');
        Route::get('/{provider}/edit', [WebProviderController::class, 'edit'])->name('edit');
        Route::put('/{provider}', [WebProviderController::class, 'update'])->name('update');
        Route::delete('/{provider}', [WebProviderController::class, 'destroy'])->name('destroy');

        // Provider status management
        Route::post('/{provider}/activate', [WebProviderController::class, 'activate'])->name('activate');
        Route::post('/{provider}/deactivate', [WebProviderController::class, 'deactivate'])->name('deactivate');
        Route::post('/{provider}/suspend', [WebProviderController::class, 'suspend'])->name('suspend');

        // Provider analytics and management
        Route::get('/analytics', [WebProviderController::class, 'analytics'])->name('analytics');
        Route::get('/import-export', [WebProviderController::class, 'importExport'])->name('import-export');
        Route::post('/import', [WebProviderController::class, 'import'])->name('import');
        Route::get('/export', [WebProviderController::class, 'export'])->name('export');

        // Provider performance management
        Route::get('/performance', [WebProviderController::class, 'performance'])->name('performance');

        // Provider contract management
        Route::get('/contracts', [WebProviderController::class, 'contracts'])->name('contracts');

        // Provider financial management
        Route::get('/financials', [WebProviderController::class, 'financials'])->name('financials');

        // Provider quality management
        Route::get('/quality', [WebProviderController::class, 'quality'])->name('quality');

        // Provider qualification management
        Route::get('/qualifications', [WebProviderController::class, 'qualifications'])->name('qualifications');

        // Provider directory
        Route::get('/directory', [WebProviderController::class, 'directory'])->name('directory');

        // Provider rating management
        Route::post('/{provider}/rating', [WebProviderController::class, 'updateRating'])->name('update-rating');

        // Provider credit limit management
        Route::post('/{provider}/credit-limit', [WebProviderController::class, 'updateCreditLimit'])->name('update-credit-limit');

        // Provider contract extension
        Route::post('/{provider}/extend-contract', [WebProviderController::class, 'extendContract'])->name('extend-contract');

        // AJAX endpoints
        Route::get('/{provider}/data', [WebProviderController::class, 'getProviderData'])->name('get-data');
        Route::get('/stats', [WebProviderController::class, 'getProviderStats'])->name('get-stats');
        Route::get('/search', [WebProviderController::class, 'search'])->name('search');
    });

    // Employee-specific performance review routes
    Route::prefix('employees/{employee}')->name('employees.')->middleware(['auth', 'permission:employees.*'])->group(function () {
        // Employee performance reviews
        Route::get('/performance-reviews', [\Fereydooni\Shopping\app\Http\Controllers\Web\EmployeePerformanceReviewController::class, 'employeeReviews'])->name('performance-reviews');
        Route::get('/performance-reviews/create', [\Fereydooni\Shopping\app\Http\Controllers\Web\EmployeePerformanceReviewController::class, 'createForEmployee'])->name('performance-reviews.create');
        Route::post('/performance-reviews', [\Fereydooni\Shopping\app\Http\Controllers\Web\EmployeePerformanceReviewController::class, 'storeForEmployee'])->name('performance-reviews.store');
        Route::get('/performance-reviews/statistics', [\Fereydooni\Shopping\app\Http\Controllers\Web\EmployeePerformanceReviewController::class, 'employeeStatistics'])->name('performance-reviews.statistics');
    });

    // Department-specific performance review routes
    Route::prefix('departments/{department}')->name('departments.')->middleware(['auth', 'permission:departments.*'])->group(function () {
        // Department performance review statistics
        Route::get('/performance-reviews/statistics', [\Fereydooni\Shopping\app\Http\Controllers\Web\EmployeePerformanceReviewController::class, 'departmentStatistics'])->name('performance-reviews.statistics');
    });

    // Employee Note management routes (authenticated)
    Route::prefix('employee-notes')->name('employee-notes.')->middleware(['auth', 'permission:employee-notes.*'])->group(function () {
        // Employee note dashboard
        Route::get('/', [\Fereydooni\Shopping\app\Http\Controllers\Web\EmployeeNoteController::class, 'index'])->name('index');
        Route::get('/create', [\Fereydooni\Shopping\app\Http\Controllers\Web\EmployeeNoteController::class, 'create'])->name('create');
        Route::post('/', [\Fereydooni\Shopping\app\Http\Controllers\Web\EmployeeNoteController::class, 'store'])->name('store');
        Route::get('/{employeeNote}', [\Fereydooni\Shopping\app\Http\Controllers\Web\EmployeeNoteController::class, 'show'])->name('show');
        Route::get('/{employeeNote}/edit', [\Fereydooni\Shopping\app\Http\Controllers\Web\EmployeeNoteController::class, 'edit'])->name('edit');
        Route::put('/{employeeNote}', [\Fereydooni\Shopping\app\Http\Controllers\Web\EmployeeNoteController::class, 'update'])->name('update');
        Route::delete('/{employeeNote}', [\Fereydooni\Shopping\app\Http\Controllers\Web\EmployeeNoteController::class, 'destroy'])->name('destroy');

        // Employee note management
        Route::post('/{employeeNote}/archive', [\Fereydooni\Shopping\app\Http\Controllers\Web\EmployeeNoteController::class, 'archive'])->name('archive');
        Route::post('/{employeeNote}/unarchive', [\Fereydooni\Shopping\app\Http\Controllers\Web\EmployeeNoteController::class, 'unarchive'])->name('unarchive');
        Route::post('/{employeeNote}/make-private', [\Fereydooni\Shopping\app\Http\Controllers\Web\EmployeeNoteController::class, 'makePrivate'])->name('make-private');
        Route::post('/{employeeNote}/make-public', [\Fereydooni\Shopping\app\Http\Controllers\Web\EmployeeNoteController::class, 'makePublic'])->name('make-public');

        // Employee note tagging
        Route::post('/{employeeNote}/tags', [\Fereydooni\Shopping\app\Http\Controllers\Web\EmployeeNoteController::class, 'addTags'])->name('add-tags');
        Route::delete('/{employeeNote}/tags', [\Fereydooni\Shopping\app\Http\Controllers\Web\EmployeeNoteController::class, 'removeTags'])->name('remove-tags');

        // Employee note attachments
        Route::post('/{employeeNote}/attachments', [\Fereydooni\Shopping\app\Http\Controllers\Web\EmployeeNoteController::class, 'addAttachment'])->name('add-attachment');
        Route::delete('/{employeeNote}/attachments/{attachment}', [\Fereydooni\Shopping\app\Http\Controllers\Web\EmployeeNoteController::class, 'removeAttachment'])->name('remove-attachment');

        // Employee note analytics and reporting
        Route::get('/statistics', [\Fereydooni\Shopping\app\Http\Controllers\Web\EmployeeNoteController::class, 'statistics'])->name('statistics');

        // Import/Export
        Route::post('/export', [\Fereydooni\Shopping\app\Http\Controllers\Web\EmployeeNoteController::class, 'export'])->name('export');
        Route::post('/import', [\Fereydooni\Shopping\app\Http\Controllers\Web\EmployeeNoteController::class, 'import'])->name('import');

        // Bulk operations
        Route::post('/bulk-archive', [\Fereydooni\Shopping\app\Http\Controllers\Web\EmployeeNoteController::class, 'bulkArchive'])->name('bulk-archive');
        Route::post('/bulk-delete', [\Fereydooni\Shopping\app\Http\Controllers\Web\EmployeeNoteController::class, 'bulkDelete'])->name('bulk-delete');

        // Search functionality
        Route::get('/search', [\Fereydooni\Shopping\app\Http\Controllers\Web\EmployeeNoteController::class, 'search'])->name('search');
    });

    // Employee-specific note routes
    Route::prefix('employees/{employee}')->name('employees.')->middleware(['auth', 'permission:employees.*'])->group(function () {
        // Employee notes
        Route::get('/notes', [\Fereydooni\Shopping\app\Http\Controllers\Web\EmployeeNoteController::class, 'employeeNotes'])->name('notes');
        Route::get('/notes/create', [\Fereydooni\Shopping\app\Http\Controllers\Web\EmployeeNoteController::class, 'createForEmployee'])->name('notes.create');
        Route::post('/notes', [\Fereydooni\Shopping\app\Http\Controllers\Web\EmployeeNoteController::class, 'storeForEmployee'])->name('notes.store');
        Route::get('/notes/statistics', [\Fereydooni\Shopping\app\Http\Controllers\Web\EmployeeNoteController::class, 'employeeStatistics'])->name('notes.statistics');
    });
});

/*
|--------------------------------------------------------------------------
| Web Routes for Provider Location Management
|--------------------------------------------------------------------------
|
| These routes handle web-based provider location management operations.
| They include CRUD operations, geocoding, search, analytics, and map functionality.
|
*/

// Provider Location Management Routes
Route::prefix('provider-locations')->name('provider-locations.')->middleware(['auth', 'verified'])->group(function () {

    // Index/List Routes
    Route::get('/', [ProviderLocationController::class, 'index'])->name('index');
    Route::get('/list', [ProviderLocationController::class, 'index'])->name('list');
    Route::get('/table', [ProviderLocationController::class, 'table'])->name('table');

    // Create Routes
    Route::get('/create', [ProviderLocationController::class, 'create'])->name('create');
    Route::post('/', [ProviderLocationController::class, 'store'])->name('store');

    // Show/View Routes
    Route::get('/{providerLocation}', [ProviderLocationController::class, 'show'])->name('show');
    Route::get('/{providerLocation}/view', [ProviderLocationController::class, 'show'])->name('view');
    Route::get('/{providerLocation}/details', [ProviderLocationController::class, 'details'])->name('details');

    // Edit/Update Routes
    Route::get('/{providerLocation}/edit', [ProviderLocationController::class, 'edit'])->name('edit');
    Route::put('/{providerLocation}', [ProviderLocationController::class, 'update'])->name('update');
    Route::patch('/{providerLocation}', [ProviderLocationController::class, 'update'])->name('update');

    // Delete Routes
    Route::delete('/{providerLocation}', [ProviderLocationController::class, 'destroy'])->name('destroy');
    Route::post('/{providerLocation}/delete', [ProviderLocationController::class, 'destroy'])->name('delete');

    // Primary Location Management
    Route::post('/{providerLocation}/set-primary', [ProviderLocationController::class, 'setPrimary'])->name('set-primary');
    Route::post('/{providerLocation}/unset-primary', [ProviderLocationController::class, 'unsetPrimary'])->name('unset-primary');

    // Status Management
    Route::post('/{providerLocation}/activate', [ProviderLocationController::class, 'activate'])->name('activate');
    Route::post('/{providerLocation}/deactivate', [ProviderLocationController::class, 'deactivate'])->name('deactivate');

    // Coordinates Management
    Route::get('/{providerLocation}/coordinates', [ProviderLocationController::class, 'coordinates'])->name('coordinates');
    Route::put('/{providerLocation}/coordinates', [ProviderLocationController::class, 'updateCoordinates'])->name('update-coordinates');
    Route::post('/{providerLocation}/geocode', [ProviderLocationController::class, 'geocode'])->name('geocode');

    // Operating Hours Management
    Route::get('/{providerLocation}/operating-hours', [ProviderLocationController::class, 'operatingHours'])->name('operating-hours');
    Route::put('/{providerLocation}/operating-hours', [ProviderLocationController::class, 'updateOperatingHours'])->name('update-operating-hours');

    // Search and Filter Routes
    Route::get('/search', [ProviderLocationController::class, 'search'])->name('search');
    Route::post('/search', [ProviderLocationController::class, 'search'])->name('search.post');
    Route::get('/filter', [ProviderLocationController::class, 'filter'])->name('filter');
    Route::post('/filter', [ProviderLocationController::class, 'filter'])->name('filter.post');

    // Geocoding Routes
    Route::get('/geocoding', [ProviderLocationController::class, 'geocoding'])->name('geocoding');
    Route::post('/geocoding', [ProviderLocationController::class, 'geocoding'])->name('geocoding.post');
    Route::get('/geocoding/batch', [ProviderLocationController::class, 'batchGeocoding'])->name('batch-geocoding');
    Route::post('/geocoding/batch', [ProviderLocationController::class, 'batchGeocoding'])->name('batch-geocoding.post');

    // Analytics Routes
    Route::get('/analytics', [ProviderLocationController::class, 'analytics'])->name('analytics');
    Route::get('/analytics/overview', [ProviderLocationController::class, 'analyticsOverview'])->name('analytics.overview');
    Route::get('/analytics/geographic', [ProviderLocationController::class, 'geographicAnalytics'])->name('analytics.geographic');
    Route::get('/analytics/types', [ProviderLocationController::class, 'typeAnalytics'])->name('analytics.types');
    Route::get('/analytics/performance', [ProviderLocationController::class, 'performanceAnalytics'])->name('analytics.performance');

    // Map Routes
    Route::get('/map', [ProviderLocationController::class, 'map'])->name('map');
    Route::get('/map/overview', [ProviderLocationController::class, 'mapOverview'])->name('map.overview');
    Route::get('/map/clusters', [ProviderLocationController::class, 'mapClusters'])->name('map.clusters');
    Route::get('/map/bounds', [ProviderLocationController::class, 'mapBounds'])->name('map.bounds');

    // Export/Import Routes
    Route::get('/export', [ProviderLocationController::class, 'export'])->name('export');
    Route::post('/import', [ProviderLocationController::class, 'import'])->name('import');
    Route::get('/import/template', [ProviderLocationController::class, 'importTemplate'])->name('import.template');

    // Bulk Operations
    Route::post('/bulk/activate', [ProviderLocationController::class, 'bulkActivate'])->name('bulk.activate');
    Route::post('/bulk/deactivate', [ProviderLocationController::class, 'bulkDeactivate'])->name('bulk.deactivate');
    Route::post('/bulk/delete', [ProviderLocationController::class, 'bulkDelete'])->name('bulk.delete');
    Route::post('/bulk/geocode', [ProviderLocationController::class, 'bulkGeocode'])->name('bulk.geocode');

    // Settings and Configuration
    Route::get('/settings', [ProviderLocationController::class, 'settings'])->name('settings');
    Route::post('/settings', [ProviderLocationController::class, 'updateSettings'])->name('settings.update');
    Route::get('/settings/geocoding', [ProviderLocationController::class, 'geocodingSettings'])->name('settings.geocoding');
    Route::post('/settings/geocoding', [ProviderLocationController::class, 'updateGeocodingSettings'])->name('settings.geocoding.update');

    // Audit and History
    Route::get('/{providerLocation}/history', [ProviderLocationController::class, 'history'])->name('history');
    Route::get('/{providerLocation}/audit', [ProviderLocationController::class, 'audit'])->name('audit');
    Route::get('/{providerLocation}/changes', [ProviderLocationController::class, 'changes'])->name('changes');

    // Provider-Specific Routes
    Route::prefix('provider/{provider}')->name('provider.')->group(function () {
        Route::get('/', [ProviderLocationController::class, 'providerLocations'])->name('locations');
        Route::get('/primary', [ProviderLocationController::class, 'providerPrimaryLocation'])->name('primary');
        Route::get('/active', [ProviderLocationController::class, 'providerActiveLocations'])->name('active');
        Route::get('/inactive', [ProviderLocationController::class, 'providerInactiveLocations'])->name('inactive');
        Route::get('/by-type/{type}', [ProviderLocationController::class, 'providerLocationsByType'])->name('by-type');
        Route::get('/by-country/{country}', [ProviderLocationController::class, 'providerLocationsByCountry'])->name('by-country');
        Route::get('/by-state/{state}', [ProviderLocationController::class, 'providerLocationsByState'])->name('by-state');
        Route::get('/by-city/{city}', [ProviderLocationController::class, 'providerLocationsByCity'])->name('by-city');
    });

    // Geographic Routes
    Route::prefix('geographic')->name('geographic.')->group(function () {
        Route::get('/countries', [ProviderLocationController::class, 'countries'])->name('countries');
        Route::get('/states/{country}', [ProviderLocationController::class, 'states'])->name('states');
        Route::get('/cities/{state}', [ProviderLocationController::class, 'cities'])->name('cities');
        Route::get('/postal-codes/{city}', [ProviderLocationController::class, 'postalCodes'])->name('postal-codes');
    });

    // Location Type Routes
    Route::prefix('types')->name('types.')->group(function () {
        Route::get('/', [ProviderLocationController::class, 'locationTypes'])->name('index');
        Route::get('/{type}', [ProviderLocationController::class, 'locationsByType'])->name('show');
        Route::get('/{type}/analytics', [ProviderLocationController::class, 'typeAnalytics'])->name('analytics');
    });

    // Nearby and Proximity Routes
    Route::prefix('nearby')->name('nearby.')->group(function () {
        Route::get('/', [ProviderLocationController::class, 'nearby'])->name('index');
        Route::get('/coordinates', [ProviderLocationController::class, 'nearbyByCoordinates'])->name('coordinates');
        Route::get('/address', [ProviderLocationController::class, 'nearbyByAddress'])->name('address');
        Route::get('/radius/{radius}', [ProviderLocationController::class, 'nearbyByRadius'])->name('radius');
    });

    // Time-based Routes
    Route::prefix('time')->name('time.')->group(function () {
        Route::get('/now-open', [ProviderLocationController::class, 'nowOpen'])->name('now-open');
        Route::get('/open-now', [ProviderLocationController::class, 'openNow'])->name('open-now');
        Route::get('/by-day/{day}', [ProviderLocationController::class, 'byDay'])->name('by-day');
        Route::get('/by-time/{time}', [ProviderLocationController::class, 'byTime'])->name('by-time');
    });

    // Contact Information Routes
    Route::prefix('contact')->name('contact.')->group(function () {
        Route::get('/{providerLocation}/info', [ProviderLocationController::class, 'contactInfo'])->name('info');
        Route::put('/{providerLocation}/info', [ProviderLocationController::class, 'updateContactInfo'])->name('update');
        Route::get('/{providerLocation}/form', [ProviderLocationController::class, 'contactForm'])->name('form');
        Route::post('/{providerLocation}/form', [ProviderLocationController::class, 'submitContactForm'])->name('submit');
    });

    // Validation and Verification Routes
    Route::prefix('validation')->name('validation.')->group(function () {
        Route::get('/{providerLocation}/verify', [ProviderLocationController::class, 'verifyLocation'])->name('verify');
        Route::post('/{providerLocation}/verify', [ProviderLocationController::class, 'verifyLocation'])->name('verify.post');
        Route::get('/{providerLocation}/validate', [ProviderLocationController::class, 'validateLocation'])->name('validate');
        Route::post('/{providerLocation}/validate', [ProviderLocationController::class, 'validateLocation'])->name('validate.post');
    });

    // Reporting Routes
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/', [ProviderLocationController::class, 'reports'])->name('index');
        Route::get('/summary', [ProviderLocationController::class, 'summaryReport'])->name('summary');
        Route::get('/geographic', [ProviderLocationController::class, 'geographicReport'])->name('geographic');
        Route::get('/performance', [ProviderLocationController::class, 'performanceReport'])->name('performance');
        Route::get('/export/{type}', [ProviderLocationController::class, 'exportReport'])->name('export');
    });
});

// Public Routes (no authentication required)
Route::prefix('locations')->name('locations.')->group(function () {
    Route::get('/', [ProviderLocationController::class, 'publicIndex'])->name('index');
    Route::get('/{providerLocation}', [ProviderLocationController::class, 'publicShow'])->name('show');
    Route::get('/search', [ProviderLocationController::class, 'publicSearch'])->name('search');
    Route::get('/nearby', [ProviderLocationController::class, 'publicNearby'])->name('nearby');
    Route::get('/map', [ProviderLocationController::class, 'publicMap'])->name('map');
});

// Admin Routes (admin middleware)
Route::prefix('admin/provider-locations')->name('admin.provider-locations.')->middleware(['auth', 'admin'])->group(function () {
    Route::get('/', [ProviderLocationController::class, 'adminIndex'])->name('index');
    Route::get('/dashboard', [ProviderLocationController::class, 'adminDashboard'])->name('dashboard');
    Route::get('/statistics', [ProviderLocationController::class, 'adminStatistics'])->name('statistics');
    Route::get('/system', [ProviderLocationController::class, 'adminSystem'])->name('system');
    Route::get('/approval', [ProviderLocationController::class, 'adminApproval'])->name('approval');
    Route::post('/approve/{providerLocation}', [ProviderLocationController::class, 'adminApprove'])->name('approve');
    Route::post('/reject/{providerLocation}', [ProviderLocationController::class, 'adminReject'])->name('reject');
});
