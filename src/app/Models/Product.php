<?php

namespace Fereydooni\Shopping\app\Models;

use Fereydooni\Shopping\app\Enums\ProductStatus;
use Fereydooni\Shopping\app\Enums\ProductType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Product extends Model implements HasMedia
{
    use InteractsWithMedia, SoftDeletes;

    protected $table = 'products';

    protected $fillable = [
        'category_id',
        'brand_id',
        'title',
        'slug',
        'description',
        'specifications',
        'status',
        'product_type',
        'has_variant',
        'multi_variant',
        'stock_quantity',
        'price',
        'sale_price',
        'cost_price',
        'meta_title',
        'meta_description',
        'meta_keywords',
    ];

    protected $casts = [
        'status' => ProductStatus::class,
        'product_type' => ProductType::class,
        'specifications' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function registerMediaConversions(?Media $media = null): void
    {
        $this
            ->addMediaConversion('thumb')
            ->width(150)
            ->height(150)
            ->nonQueued(); // For immediate generation

        $this
            ->addMediaConversion('medium')
            ->width(600)
            ->optimize(); // use optimizer

        $this
            ->addMediaConversion('large')
            ->width(1280)
            ->quality(90)
            ->optimize();
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(ProductReview::class);
    }

    public function discounts(): HasMany
    {
        return $this->hasMany(ProductDiscount::class);
    }

    public function meta(): HasMany
    {
        return $this->hasMany(ProductMeta::class);
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(
            ProductTag::class,
            'product_tag_products',
            'product_id',
            'tag_id',
            'id',
            'id'
        );
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function productAttribute(): HasOneThrough
    {
        return $this->hasOneThrough(
            ProductAttribute::class,
            ProductVariantValue::class,
            'product_id', // Foreign key on ProductVariantValue table...
            'id', // Foreign key on Attribute table...
            'id', // Local key on Product table...
            'attribute_id' // Local key on ProductVariantValue table...
        );
    }

    public function productAttributes(): HasManyThrough
    {
        return $this->hasManyThrough(
            ProductAttribute::class,
            ProductVariantValue::class,
            'product_id', // Foreign key on ProductVariantValue table...
            'id', // Foreign key on ProductAttribute table...
            'id', // Local key on Product table...
            'attribute_id' // Local key on ProductVariantValue table...
        )->distinct();
    }

    public function mainMedia()
    {
        return $this->getMedia('product-images')
            ->first(function ($mediaItem) {
                return $mediaItem->getCustomProperty('is_main') === true;
            });
    }

    // Accessors
    public function getMainImageAttribute()
    {
        $media = $this->mainMedia();

        return $media ? $media->getUrl('large') : null;
    }

    public function getImagesAttribute()
    {
        return $this->getMedia('product-images')->map(function ($media) {
            return [
                'id' => $media->id,
                'name' => $media->file_name,
                'size' => $media->size,
                'type' => $media->mime_type,
                'url' => $media->getUrl('large'),
                'thumbnail' => $media->getUrl('thumb'),
                'is_main' => $media->getCustomProperty('is_main', false),
            ];
        })->values();
    }

    public function getVideosAttribute()
    {
        return $this->getMedia('product-videos')->map(function ($media) {
            return [
                'id' => $media->id,
                'name' => $media->file_name,
                'size' => $media->size,
                'type' => $media->mime_type,
                'url' => $media->getUrl(),
                'thumbnail' => $media->getUrl('thumb'),
            ];
        })->values();
    }
}
