<?php

namespace Fereydooni\Shopping\app\Models;

use Fereydooni\Shopping\app\Enums\ProductStatus;
use Fereydooni\Shopping\app\Events\ProductTagCreated;
use Fereydooni\Shopping\app\Events\ProductTagDeleted;
use Fereydooni\Shopping\app\Events\ProductTagUpdated;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Laravel\Scout\Searchable;

class ProductTag extends Model
{
    use Searchable;

    protected $table = 'product_tags';

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

    public static function toScoutModelSettings(): array
    {
        return [
            self::class => [
                'collection-schema' => self::getTypesenseCollectionSchema(),
                'search-parameters' => [
                    'query_by' => implode(',', self::searchableFields()),
                ],
            ],
        ];
    }

    public static function searchableFields(): array
    {
        return ['name', 'description', 'slug'];
    }

    /**
     * Get the indexable data array for the model.
     */
    public function toSearchableArray(): array
    {
        return [
            'id' => (string) $this->id,
            'id_numeric' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description ?? '',
            'color' => $this->color ?? '',
            'icon' => $this->icon ?? '',
            'is_active' => $this->is_active,
            'is_featured' => $this->is_featured,
            'sort_order' => $this->sort_order,
            'usage_count' => $this->usage_count,
            'created_at' => $this->created_at?->timestamp,
        ];
    }

    /**
     * Define the Typesense collection schema.
     */
    public static function getTypesenseCollectionSchema(): array
    {
        return [
            'fields' => [
                [
                    'name' => 'id',
                    'type' => 'string',
                    'facet' => false,
                ],
                [
                    'name' => 'id_numeric',
                    'type' => 'int64',
                    'facet' => false,
                ],
                [
                    'name' => 'name',
                    'type' => 'string',
                    'facet' => true,
                ],
                [
                    'name' => 'slug',
                    'type' => 'string',
                    'facet' => false,
                ],
                [
                    'name' => 'description',
                    'type' => 'string',
                    'facet' => false,
                ],
                [
                    'name' => 'color',
                    'type' => 'string',
                    'facet' => true,
                ],
                [
                    'name' => 'icon',
                    'type' => 'string',
                    'facet' => false,
                ],
                [
                    'name' => 'is_active',
                    'type' => 'bool',
                    'facet' => true,
                ],
                [
                    'name' => 'is_featured',
                    'type' => 'bool',
                    'facet' => true,
                ],
                [
                    'name' => 'sort_order',
                    'type' => 'int32',
                    'facet' => false,
                ],
                [
                    'name' => 'usage_count',
                    'type' => 'int32',
                    'facet' => false,
                ],
                [
                    'name' => 'created_at',
                    'type' => 'int64',
                    'facet' => false,
                ],
                [
                    'name' => 'embedding',
                    'type' => 'float[]',
                    'embed' => [
                        'from' => self::searchableFields(),
                        'model_config' => [
                            'model_name' => 'ts/all-MiniLM-L12-v2',
                        ],
                    ],
                ],
            ],
            'default_sorting_field' => 'created_at',
        ];
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(
            Product::class,
            'product_tag_product',
            'tag_id',
            'product_id',
            'id',
            'id'
        );
    }

    public function activeProducts(): BelongsToMany
    {
        return $this->products()->where('status', ProductStatus::ACTIVE);
    }

    public function inactiveProducts(): BelongsToMany
    {
        return $this->products()->where('status', ProductStatus::INACTIVE);
    }

    /**
     * Scope a query to only include active tags.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to only include featured tags.
     */
    public function scopeFeatured(Builder $query): Builder
    {
        return $query->where('is_featured', true);
    }

    /**
     * Scope a query to order by sort order.
     */
    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order', 'asc')->orderBy('name', 'asc');
    }

    /**
     * Scope a query to order by usage count.
     */
    public function scopePopular(Builder $query): Builder
    {
        return $query->orderBy('usage_count', 'desc');
    }

    /**
     * Scope a query to search by name or description.
     */
    public function scopeSearch(Builder $query, string $search): Builder
    {
        return $query->where(function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
                ->orWhere('description', 'like', "%{$search}%");
        });
    }

    /**
     * Get the tag's display name with color if available.
     */
    public function getDisplayNameAttribute(): string
    {
        return $this->name;
    }

    /**
     * Get the tag's color with fallback.
     */
    public function getDisplayColorAttribute(): string
    {
        return $this->color ?? '#6c757d';
    }

    /**
     * Increment the usage count for this tag.
     */
    public function incrementUsage(): void
    {
        $this->increment('usage_count');
    }

    /**
     * Decrement the usage count for this tag.
     */
    public function decrementUsage(): void
    {
        $this->decrement('usage_count');
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
