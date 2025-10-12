<?php

namespace Fereydooni\Shopping\app\Models;

use Laravel\Scout\Searchable;
use Spatie\MediaLibrary\HasMedia;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Spatie\MediaLibrary\InteractsWithMedia;
use Fereydooni\Shopping\app\Enums\BrandStatus;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Brand extends Model implements HasMedia
{
    use InteractsWithMedia;
    use Searchable;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'website',
        'email',
        'phone',
        'founded_year',
        'headquarters',
        'logo_url',
        'banner_url',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'status',
        'is_active',
        'is_featured',
        'sort_order',
    ];

    protected $casts = [
        'status' => BrandStatus::class,
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'founded_year' => 'integer',
        'sort_order' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected $attributes = [
        'is_active' => true,
        'is_featured' => false,
        'sort_order' => 0,
    ];

    public static function toScoutModelSettings(): array
    {
        return [
            self::class => [
                'collection-schema' => self::getTypesenseCollectionSchema(),
                'search-parameters' => [
                    'query_by' => implode(',', self::searchableFields())
                ]
            ]
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
            'website' => $this->website ?? '',
            'email' => $this->email ?? '',
            'phone' => $this->phone ?? '',
            'founded_year' => $this->founded_year,
            'headquarters' => $this->headquarters ?? '',
            'is_active' => $this->is_active,
            'is_featured' => $this->is_featured,
            'sort_order' => $this->sort_order,
            'meta_title' => $this->meta_title ?? '',
            'meta_description' => $this->meta_description ?? '',
            'meta_keywords' => $this->meta_keywords ?? '',
            'status' => $this->status?->value ?? null,
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
                    'name' => 'website',
                    'type' => 'string',
                    'facet' => false,
                ],
                [
                    'name' => 'email',
                    'type' => 'string',
                    'facet' => false,
                ],
                [
                    'name' => 'phone',
                    'type' => 'string',
                    'facet' => false,
                ],
                [
                    'name' => 'founded_year',
                    'type' => 'int32',
                    'facet' => true,
                ],
                [
                    'name' => 'headquarters',
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
                    'name' => 'meta_title',
                    'type' => 'string',
                    'facet' => false,
                ],
                [
                    'name' => 'meta_description',
                    'type' => 'string',
                    'facet' => false,
                ],
                [
                    'name' => 'meta_keywords',
                    'type' => 'string',
                    'facet' => false,
                ],
                [
                    'name' => 'status',
                    'type' => 'string',
                    'facet' => true,
                ],
                [
                    'name' => 'created_at',
                    'type' => 'int64',
                    'facet' => false,
                ],
                [
                    "name" => "embedding",
                    "type" => "float[]",
                    "embed" => [
                        "from" => self::searchableFields(),
                        "model_config" => [
                            "model_name" => "ts/all-MiniLM-L12-v2"
                        ]
                    ]
                ]
            ],
            'default_sorting_field' => 'created_at',
        ];
    }

    /**
     * Get the products for the brand.
     */
    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    /**
     * Scope a query to only include active brands.
     */
    public function scopeActive(Builder $query): void
    {
        $query->where('is_active', true);
    }

    /**
     * Scope a query to only include featured brands.
     */
    public function scopeFeatured(Builder $query): void
    {
        $query->where('is_featured', true);
    }

    /**
     * Scope a query to order brands by sort order.
     */
    public function scopeOrdered(Builder $query): void
    {
        $query->orderBy('sort_order', 'asc')->orderBy('name', 'asc');
    }

    /**
     * Scope a query to search brands by name or description.
     */
    public function scopeSearch(Builder $query, string $search): void
    {
        $query->where(function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
                ->orWhere('description', 'like', "%{$search}%")
                ->orWhere('meta_keywords', 'like', "%{$search}%");
        });
    }

    /**
     * Scope a query to filter brands by first letter of name.
     */
    public function scopeByFirstLetter(Builder $query, string $letter): void
    {
        $query->where('name', 'like', "{$letter}%");
    }

    /**
     * Check if the brand is active.
     */
    public function isActive(): bool
    {
        return $this->is_active;
    }

    /**
     * Check if the brand is featured.
     */
    public function isFeatured(): bool
    {
        return $this->is_featured;
    }

    /**
     * Get the brand's logo media.
     */
    public function getLogoMedia()
    {
        return $this->getFirstMedia('logo');
    }

    /**
     * Get the brand's banner media.
     */
    public function getBannerMedia()
    {
        return $this->getFirstMedia('banner');
    }

    /**
     * Get the brand's logo URL.
     */
    public function getLogoUrlAttribute(): ?string
    {
        $media = $this->getLogoMedia();
        return $media ? $media->getUrl() : $this->attributes['logo_url'] ?? null;
    }

    /**
     * Get the brand's banner URL.
     */
    public function getBannerUrlAttribute(): ?string
    {
        $media = $this->getBannerMedia();
        return $media ? $media->getUrl() : $this->attributes['banner_url'] ?? null;
    }
}
