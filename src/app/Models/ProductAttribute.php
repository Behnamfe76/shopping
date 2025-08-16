<?php

namespace Fereydooni\Shopping\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Fereydooni\Shopping\app\Enums\ProductAttributeType;
use Fereydooni\Shopping\app\Enums\ProductAttributeInputType;

class ProductAttribute extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
        'type',
        'input_type',
        'is_required',
        'is_searchable',
        'is_filterable',
        'is_comparable',
        'is_visible',
        'sort_order',
        'validation_rules',
        'default_value',
        'unit',
        'group',
        'is_system',
        'is_active',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'type' => ProductAttributeType::class,
        'input_type' => ProductAttributeInputType::class,
        'is_required' => 'boolean',
        'is_searchable' => 'boolean',
        'is_filterable' => 'boolean',
        'is_comparable' => 'boolean',
        'is_visible' => 'boolean',
        'is_system' => 'boolean',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function values(): HasMany
    {
        return $this->hasMany(ProductAttributeValue::class, 'attribute_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'updated_by');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeRequired($query)
    {
        return $query->where('is_required', true);
    }

    public function scopeSearchable($query)
    {
        return $query->where('is_searchable', true);
    }

    public function scopeFilterable($query)
    {
        return $query->where('is_filterable', true);
    }

    public function scopeComparable($query)
    {
        return $query->where('is_comparable', true);
    }

    public function scopeVisible($query)
    {
        return $query->where('is_visible', true);
    }

    public function scopeSystem($query)
    {
        return $query->where('is_system', true);
    }

    public function scopeCustom($query)
    {
        return $query->where('is_system', false);
    }

    public function scopeByType($query, string $type)
    {
        return $query->where('type', $type);
    }

    public function scopeByInputType($query, string $inputType)
    {
        return $query->where('input_type', $inputType);
    }

    public function scopeByGroup($query, string $group)
    {
        return $query->where('group', $group);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order', 'asc')->orderBy('name', 'asc');
    }
}
