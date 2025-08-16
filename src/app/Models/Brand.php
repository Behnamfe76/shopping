<?php

namespace Fereydooni\Shopping\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Fereydooni\Shopping\app\Enums\BrandStatus;

class Brand extends Model implements HasMedia
{
    use InteractsWithMedia;

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
        'is_active',
        'is_featured',
        'sort_order',
    ];

    protected $casts = [
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
     * Get the brand's status.
     */
    public function getStatusAttribute(): BrandStatus
    {
        if (!$this->is_active) {
            return BrandStatus::INACTIVE;
        }

        return BrandStatus::ACTIVE;
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
