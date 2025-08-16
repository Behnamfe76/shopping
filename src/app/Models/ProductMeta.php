<?php

namespace Fereydooni\Shopping\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Fereydooni\Shopping\app\Enums\MetaType;
use Fereydooni\Shopping\app\Events\ProductMetaCreated;
use Fereydooni\Shopping\app\Events\ProductMetaUpdated;
use Fereydooni\Shopping\app\Events\ProductMetaDeleted;

class ProductMeta extends Model
{
    protected $fillable = [
        'product_id',
        'meta_key',
        'meta_value',
        'meta_type',
        'is_public',
        'is_searchable',
        'is_filterable',
        'sort_order',
        'description',
        'validation_rules',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'is_public' => 'boolean',
        'is_searchable' => 'boolean',
        'is_filterable' => 'boolean',
        'sort_order' => 'integer',
        'created_by' => 'integer',
        'updated_by' => 'integer',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function getMetaTypeEnum(): MetaType
    {
        return MetaType::from($this->meta_type ?? MetaType::TEXT->value);
    }

    public function isPublic(): bool
    {
        return $this->is_public ?? true;
    }

    public function isSearchable(): bool
    {
        return $this->is_searchable ?? false;
    }

    public function isFilterable(): bool
    {
        return $this->is_filterable ?? false;
    }

    /**
     * The event map for the model.
     */
    protected $dispatchesEvents = [
        'created' => ProductMetaCreated::class,
        'updated' => ProductMetaUpdated::class,
        'deleted' => ProductMetaDeleted::class,
    ];

    /**
     * Boot the model and register event listeners.
     */
    protected static function boot()
    {
        parent::boot();

        static::updated(function ($productMeta) {
            event(new ProductMetaUpdated($productMeta, $productMeta->getChanges()));
        });
    }
}
