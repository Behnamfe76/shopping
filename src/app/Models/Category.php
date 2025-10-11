<?php

namespace Fereydooni\Shopping\app\Models;

use Laravel\Scout\Searchable;
use Spatie\MediaLibrary\HasMedia;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Category extends Model implements HasMedia
{
    use InteractsWithMedia;
    use Searchable;

    protected $table = 'categories';

    protected $fillable = [
        'parent_id',
        'name',
        'slug',
        'description',
        'status',
        'sort_order',
        'is_default',
    ];

    protected $casts = [
        'status' => \Fereydooni\Shopping\app\Enums\CategoryStatus::class,
        'is_default' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
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
            'parent_id' => (int) $this->parent_id,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description ?? '',
            'status' => $this->status,
            'is_default' => (bool) $this->is_default,
            'sort_order' => (int) $this->sort_order,
            'created_at' => $this->created_at?->timestamp,
            'parent' => $this->parent ? (object) array(
                'id' => (string) $this->parent->id,
                'id_numeric' => $this->parent->id,
                'parent_id' => (int) $this->parent->parent_id,
                'name' => $this->parent->name,
                'slug' => $this->parent->slug,
                'description' => $this->parent->description ?? '',
                'is_default' => (bool) $this->parent->is_default,
                'sort_order' => (int) $this->parent->sort_order,
                'created_at' => $this->parent->created_at?->timestamp,
            ) : (object) [],
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
                    'name' => 'parent_id',
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
                    'name' => 'is_default',
                    'type' => 'bool',
                    'facet' => true,
                ],
                [
                    'name' => 'sort_order',
                    'type' => 'int32',
                    'facet' => false,
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
                ],
                // Parent object fields (nested)
                [
                    'name' => 'parent',
                    'type' => 'object',
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
                            'name' => 'parent_id',
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
                            'name' => 'is_default',
                            'type' => 'bool',
                            'facet' => true,
                        ],
                        [
                            'name' => 'sort_order',
                            'type' => 'int32',
                            'facet' => false,
                        ],
                        [
                            'name' => 'created_at',
                            'type' => 'int64',
                            'facet' => false,
                        ],
                    ],
                ],
            ],
            'default_sorting_field' => 'created_at',
            'enable_nested_fields' => true
        ];
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function allChildren(): HasMany
    {
        return $this->children()->with('allChildren');
    }

    public function allParents()
    {
        return $this->parent()->with('allParents');
    }
}
