<?php

namespace Fereydooni\Shopping\app\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Fereydooni\Shopping\app\Events\ProductTagCreated;
use Fereydooni\Shopping\app\Events\ProductTagUpdated;
use Fereydooni\Shopping\app\Events\ProductTagDeleted;
use Fereydooni\Shopping\app\Events\ProductTagStatusChanged;
use Fereydooni\Shopping\app\Events\ProductTagBulkOperation;
use Fereydooni\Shopping\app\Events\ProductTagImported;
use Fereydooni\Shopping\app\Events\ProductTagExported;
use Fereydooni\Shopping\app\Events\ProductTagSynced;
use Fereydooni\Shopping\app\Listeners\SendProductTagCreatedNotification;
use Fereydooni\Shopping\app\Listeners\UpdateProductTagCache;
use Fereydooni\Shopping\app\Listeners\UpdateProductSearchIndex;
use Fereydooni\Shopping\app\Listeners\UpdateProductFilterCache;
use Fereydooni\Shopping\app\Listeners\UpdateProductTagIndex;
use Fereydooni\Shopping\app\Listeners\GenerateProductTagReport;
use Fereydooni\Shopping\app\Events\ProductVariantCreated;
use Fereydooni\Shopping\app\Events\ProductVariantUpdated;
use Fereydooni\Shopping\app\Events\ProductVariantDeleted;
use Fereydooni\Shopping\app\Events\ProductVariantStatusChanged;
use Fereydooni\Shopping\app\Events\ProductVariantStockUpdated;
use Fereydooni\Shopping\app\Events\ProductVariantPriceUpdated;
use Fereydooni\Shopping\app\Events\ProductVariantLowStock;
use Fereydooni\Shopping\app\Events\ProductVariantOutOfStock;
use Fereydooni\Shopping\app\Listeners\SendProductVariantCreatedNotification;
use Fereydooni\Shopping\app\Listeners\UpdateProductVariantCache;
use Fereydooni\Shopping\app\Listeners\UpdateProductInventoryCache;
use Fereydooni\Shopping\app\Listeners\UpdateProductPricingCache;
use Fereydooni\Shopping\app\Listeners\SendLowStockNotification;
use Fereydooni\Shopping\app\Listeners\SendOutOfStockNotification;
use Fereydooni\Shopping\app\Listeners\GenerateProductVariantReport;
use Fereydooni\Shopping\app\Events\Customer\CustomerCreated;
use Fereydooni\Shopping\app\Events\Customer\CustomerUpdated;
use Fereydooni\Shopping\app\Events\Customer\CustomerDeleted;
use Fereydooni\Shopping\app\Events\Customer\CustomerActivated;
use Fereydooni\Shopping\app\Events\Customer\CustomerDeactivated;
use Fereydooni\Shopping\app\Events\Customer\CustomerSuspended;
use Fereydooni\Shopping\app\Events\Customer\LoyaltyPointsAdded;
use Fereydooni\Shopping\app\Events\Customer\LoyaltyPointsDeducted;
use Fereydooni\Shopping\app\Listeners\Customer\SendWelcomeEmail;
use Fereydooni\Shopping\app\Listeners\Customer\UpdateCustomerAnalytics;
use Fereydooni\Shopping\app\Listeners\Customer\NotifyCustomerStatusChange;
use Fereydooni\Shopping\app\Listeners\Customer\UpdateLoyaltyProgram;
use Fereydooni\Shopping\app\Listeners\Customer\LogCustomerActivity;

// EmployeeDepartment Events
use Fereydooni\Shopping\app\Events\EmployeeDepartment\EmployeeDepartmentCreated;
use Fereydooni\Shopping\app\Events\EmployeeDepartment\EmployeeDepartmentUpdated;
use Fereydooni\Shopping\app\Events\EmployeeDepartment\EmployeeDepartmentManagerAssigned;
use Fereydooni\Shopping\app\Events\EmployeeDepartment\EmployeeDepartmentMoved;
use Fereydooni\Shopping\app\Events\EmployeeDepartment\EmployeeDepartmentArchived;

// EmployeeDepartment Listeners
use Fereydooni\Shopping\app\Listeners\EmployeeDepartment\SendDepartmentNotification;
use Fereydooni\Shopping\app\Listeners\EmployeeDepartment\UpdateDepartmentHierarchy;
use Fereydooni\Shopping\app\Listeners\EmployeeDepartment\LogDepartmentActivity;
use Fereydooni\Shopping\app\Listeners\EmployeeDepartment\UpdateEmployeeDepartmentRecords;
use Fereydooni\Shopping\app\Listeners\EmployeeDepartment\UpdateDepartmentMetrics;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        // Product Tag Events
        ProductTagCreated::class => [
            SendProductTagCreatedNotification::class,
            UpdateProductTagCache::class,
            UpdateProductSearchIndex::class,
            UpdateProductFilterCache::class,
            UpdateProductTagIndex::class,
            GenerateProductTagReport::class,
        ],
        ProductTagUpdated::class => [
            UpdateProductTagCache::class,
            UpdateProductSearchIndex::class,
            UpdateProductFilterCache::class,
            UpdateProductTagIndex::class,
            GenerateProductTagReport::class,
        ],
        ProductTagDeleted::class => [
            UpdateProductTagCache::class,
            UpdateProductSearchIndex::class,
            UpdateProductFilterCache::class,
            UpdateProductTagIndex::class,
            GenerateProductTagReport::class,
        ],
        ProductTagStatusChanged::class => [
            UpdateProductTagCache::class,
            UpdateProductSearchIndex::class,
            UpdateProductFilterCache::class,
            UpdateProductTagIndex::class,
            GenerateProductTagReport::class,
        ],
        ProductTagBulkOperation::class => [
            UpdateProductTagCache::class,
            UpdateProductSearchIndex::class,
            UpdateProductFilterCache::class,
            UpdateProductTagIndex::class,
            GenerateProductTagReport::class,
        ],
        ProductTagImported::class => [
            UpdateProductTagCache::class,
            UpdateProductSearchIndex::class,
            UpdateProductFilterCache::class,
            UpdateProductTagIndex::class,
            GenerateProductTagReport::class,
        ],
        ProductTagExported::class => [
            GenerateProductTagReport::class,
        ],
        ProductTagSynced::class => [
            UpdateProductTagCache::class,
            UpdateProductSearchIndex::class,
            UpdateProductFilterCache::class,
            UpdateProductTagIndex::class,
            GenerateProductTagReport::class,
        ],

        // Product Variant Events
        ProductVariantCreated::class => [
            SendProductVariantCreatedNotification::class,
            UpdateProductVariantCache::class,
            UpdateProductInventoryCache::class,
            UpdateProductPricingCache::class,
            GenerateProductVariantReport::class,
        ],
        ProductVariantUpdated::class => [
            UpdateProductVariantCache::class,
            UpdateProductInventoryCache::class,
            UpdateProductPricingCache::class,
            GenerateProductVariantReport::class,
        ],
        ProductVariantDeleted::class => [
            UpdateProductVariantCache::class,
            UpdateProductInventoryCache::class,
            UpdateProductPricingCache::class,
            GenerateProductVariantReport::class,
        ],
        ProductVariantStatusChanged::class => [
            UpdateProductVariantCache::class,
            UpdateProductInventoryCache::class,
            UpdateProductPricingCache::class,
            GenerateProductVariantReport::class,
        ],
        ProductVariantStockUpdated::class => [
            UpdateProductVariantCache::class,
            UpdateProductInventoryCache::class,
            GenerateProductVariantReport::class,
        ],
        ProductVariantPriceUpdated::class => [
            UpdateProductVariantCache::class,
            UpdateProductPricingCache::class,
            GenerateProductVariantReport::class,
        ],
        ProductVariantLowStock::class => [
            SendLowStockNotification::class,
            UpdateProductInventoryCache::class,
            GenerateProductVariantReport::class,
        ],
        ProductVariantOutOfStock::class => [
            SendOutOfStockNotification::class,
            UpdateProductInventoryCache::class,
            GenerateProductVariantReport::class,
        ],

        // Customer Events
        CustomerCreated::class => [
            SendWelcomeEmail::class,
            UpdateCustomerAnalytics::class,
            LogCustomerActivity::class,
        ],
        CustomerUpdated::class => [
            UpdateCustomerAnalytics::class,
            LogCustomerActivity::class,
        ],
        CustomerDeleted::class => [
            UpdateCustomerAnalytics::class,
            LogCustomerActivity::class,
        ],
        CustomerActivated::class => [
            UpdateCustomerAnalytics::class,
            NotifyCustomerStatusChange::class,
            LogCustomerActivity::class,
        ],
        CustomerDeactivated::class => [
            UpdateCustomerAnalytics::class,
            NotifyCustomerStatusChange::class,
            LogCustomerActivity::class,
        ],
        CustomerSuspended::class => [
            UpdateCustomerAnalytics::class,
            NotifyCustomerStatusChange::class,
            LogCustomerActivity::class,
        ],
        LoyaltyPointsAdded::class => [
            UpdateLoyaltyProgram::class,
            LogCustomerActivity::class,
        ],
        LoyaltyPointsDeducted::class => [
            UpdateLoyaltyProgram::class,
            LogCustomerActivity::class,
        ],

        // EmployeeDepartment Events
        EmployeeDepartmentCreated::class => [
            SendDepartmentNotification::class,
            UpdateDepartmentHierarchy::class,
            LogDepartmentActivity::class,
            UpdateEmployeeDepartmentRecords::class,
            UpdateDepartmentMetrics::class,
        ],
        EmployeeDepartmentUpdated::class => [
            SendDepartmentNotification::class,
            UpdateDepartmentHierarchy::class,
            LogDepartmentActivity::class,
            UpdateEmployeeDepartmentRecords::class,
            UpdateDepartmentMetrics::class,
        ],
        EmployeeDepartmentManagerAssigned::class => [
            SendDepartmentNotification::class,
            UpdateDepartmentHierarchy::class,
            LogDepartmentActivity::class,
            UpdateEmployeeDepartmentRecords::class,
            UpdateDepartmentMetrics::class,
        ],
        EmployeeDepartmentMoved::class => [
            SendDepartmentNotification::class,
            UpdateDepartmentHierarchy::class,
            LogDepartmentActivity::class,
            UpdateEmployeeDepartmentRecords::class,
            UpdateDepartmentMetrics::class,
        ],
        EmployeeDepartmentArchived::class => [
            SendDepartmentNotification::class,
            UpdateDepartmentHierarchy::class,
            LogDepartmentActivity::class,
            UpdateEmployeeDepartmentRecords::class,
            UpdateDepartmentMetrics::class,
        ],
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        parent::boot();
    }
}
