<?php

namespace Fereydooni\Shopping\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductAttributeValue extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'attribute_id',
        'value',
        'slug',
        'description',
        'sort_order',
        'is_active',
        'is_default',
        'color_code',
        'image_url',
        'meta_data',
        'usage_count',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'meta_data' => 'array',
        'is_active' => 'boolean',
        'is_default' => 'boolean',
        'sort_order' => 'integer',
        'usage_count' => 'integer',
        'created_by' => 'integer',
        'updated_by' => 'integer',
    ];

    protected $attributes = [
        'is_active' => true,
        'is_default' => false,
        'sort_order' => 0,
        'usage_count' => 0,
    ];

    public function attribute(): BelongsTo
    {
        return $this->belongsTo(ProductAttribute::class, 'attribute_id');
    }

    public function variants(): BelongsToMany
    {
        return $this->belongsToMany(ProductVariant::class, 'product_variant_values', 'attribute_value_id', 'variant_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'updated_by');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }

    public function scopeByAttribute($query, int $attributeId)
    {
        return $query->where('attribute_id', $attributeId);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('value');
    }

    public function scopeMostUsed($query, int $limit = 10)
    {
        return $query->orderBy('usage_count', 'desc')->limit($limit);
    }

    public function scopeLeastUsed($query, int $limit = 10)
    {
        return $query->orderBy('usage_count', 'asc')->limit($limit);
    }

    public function scopeUnused($query)
    {
        return $query->where('usage_count', 0);
    }

    public function scopeByUsageRange($query, int $minUsage, int $maxUsage)
    {
        return $query->whereBetween('usage_count', [$minUsage, $maxUsage]);
    }

    // Accessors
    public function getFormattedColorCodeAttribute(): ?string
    {
        return $this->color_code ? strtoupper($this->color_code) : null;
    }

    public function getIsUsedAttribute(): bool
    {
        return $this->usage_count > 0;
    }

    public function getDisplayValueAttribute(): string
    {
        return $this->description ?: $this->value;
    }

    // Mutators
    public function setValueAttribute($value)
    {
        $this->attributes['value'] = trim($value);
    }

    public function setSlugAttribute($value)
    {
        $this->attributes['slug'] = $value ? strtolower(trim($value)) : null;
    }

    public function setColorCodeAttribute($value)
    {
        $this->attributes['color_code'] = $value ? strtolower(trim($value)) : null;
    }

    // Methods
    public function incrementUsage(): bool
    {
        return $this->increment('usage_count') > 0;
    }

    public function decrementUsage(): bool
    {
        if ($this->usage_count > 0) {
            return $this->decrement('usage_count') > 0;
        }
        return false;
    }

    public function toggleActive(): bool
    {
        return $this->update(['is_active' => !$this->is_active]);
    }

    public function toggleDefault(): bool
    {
        return $this->update(['is_default' => !$this->is_default]);
    }

    public function setAsDefault(): bool
    {
        // Remove default from other values in the same attribute
        static::where('attribute_id', $this->attribute_id)
            ->where('id', '!=', $this->id)
            ->update(['is_default' => false]);

        return $this->update(['is_default' => true]);
    }

    public function isUniqueInAttribute(): bool
    {
        return !static::where('attribute_id', $this->attribute_id)
            ->where('value', $this->value)
            ->where('id', '!=', $this->id)
            ->exists();
    }

    public function isSlugUnique(): bool
    {
        return !static::where('slug', $this->slug)
            ->where('id', '!=', $this->id)
            ->exists();
    }
}
