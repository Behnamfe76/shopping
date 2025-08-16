<?php

namespace Fereydooni\Shopping\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Fereydooni\Shopping\app\Events\ProductTagCreated;
use Fereydooni\Shopping\app\Events\ProductTagUpdated;
use Fereydooni\Shopping\app\Events\ProductTagDeleted;

class ProductTag extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
        'color',
        'icon',
        'is_active',
        'is_featured',
        'sort_order',
        'usage_count',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'sort_order' => 'integer',
        'usage_count' => 'integer',
        'created_by' => 'integer',
        'updated_by' => 'integer',
    ];

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'product_tag_product');
    }

    /**
     * The event map for the model.
     */
    protected $dispatchesEvents = [
        'created' => ProductTagCreated::class,
        'updated' => ProductTagUpdated::class,
        'deleted' => ProductTagDeleted::class,
    ];

    /**
     * Boot the model and register event listeners.
     */
    protected static function boot()
    {
        parent::boot();

        static::updated(function ($productTag) {
            event(new ProductTagUpdated($productTag, $productTag->getChanges()));
        });
    }
}
