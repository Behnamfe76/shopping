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

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
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
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        parent::boot();
    }
}
