<?php

namespace Fereydooni\Shopping\app\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Fereydooni\Shopping\app\Models\Product;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        /** @var Product $this */
        return [
            'id' => $this->id,
            'category_id' => $this->category_id,
            'brand_id' => $this->brand_id,
            'sku' => $this->sku,
            'title' => $this->title,
            'slug' => $this->slug,
            'description' => $this->description,
            'weight' => $this->weight,
            'dimensions' => $this->dimensions,
            'status' => $this->status?->value,
            'status_label' => $this->status?->label(),
            'product_type' => $this->product_type?->value,
            'product_type_label' => $this->product_type?->label(),
            'price' => $this->price,
            'sale_price' => $this->sale_price,
            'cost_price' => $this->cost_price,
            'stock_quantity' => $this->stock_quantity,
            'min_stock_level' => $this->min_stock_level,
            'max_stock_level' => $this->max_stock_level,
            'is_featured' => $this->is_featured,
            'is_active' => $this->is_active,
            'sort_order' => $this->sort_order,
            'meta_title' => $this->meta_title,
            'meta_description' => $this->meta_description,
            'meta_keywords' => $this->meta_keywords,
            'seo_url' => $this->seo_url,
            'canonical_url' => $this->canonical_url,
            'og_image' => $this->og_image,
            'twitter_image' => $this->twitter_image,
            'video_url' => $this->video_url,
            'warranty_info' => $this->warranty_info,
            'shipping_info' => $this->shipping_info,
            'return_policy' => $this->return_policy,
            'tags' => $this->tags,
            'attributes' => $this->attributes,
            'variants_count' => $this->variants_count,
            'reviews_count' => $this->reviews_count,
            'average_rating' => $this->average_rating,
            'total_sales' => $this->total_sales,
            'view_count' => $this->view_count,
            'wishlist_count' => $this->wishlist_count,
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),

            // Relationships
            'category' => $this->whenLoaded('category', function () {
                return [
                    'id' => $this->category->id,
                    'name' => $this->category->name,
                    'slug' => $this->category->slug,
                ];
            }),
            'brand' => $this->whenLoaded('brand', function () {
                return [
                    'id' => $this->brand->id,
                    'name' => $this->brand->name,
                    'slug' => $this->brand->slug,
                ];
            }),
            'media' => $this->whenLoaded('media', function () {
                return $this->media->map(function ($media) {
                    return [
                        'id' => $media->id,
                        'name' => $media->name,
                        'file_name' => $media->file_name,
                        'mime_type' => $media->mime_type,
                        'size' => $media->size,
                        'url' => $media->getUrl(),
                        'thumbnail_url' => $media->getUrl('thumbnail'),
                        'collection_name' => $media->collection_name,
                    ];
                });
            }),
            'variants' => $this->whenLoaded('variants', function () {
                return $this->variants->map(function ($variant) {
                    return [
                        'id' => $variant->id,
                        'name' => $variant->name,
                        'sku' => $variant->sku,
                        'price' => $variant->price,
                        'stock_quantity' => $variant->stock_quantity,
                    ];
                });
            }),
            'reviews' => $this->whenLoaded('reviews', function () {
                return $this->reviews->map(function ($review) {
                    return [
                        'id' => $review->id,
                        'rating' => $review->rating,
                        'comment' => $review->comment,
                        'created_at' => $review->created_at?->toISOString(),
                    ];
                });
            }),

            // Computed fields
            'is_in_stock' => $this->stock_quantity > 0,
            'is_low_stock' => $this->stock_quantity <= $this->min_stock_level,
            'is_on_sale' => $this->sale_price && $this->sale_price > 0,
            'discount_percentage' => $this->when($this->sale_price && $this->price > 0, function () {
                return round((($this->price - $this->sale_price) / $this->price) * 100, 2);
            }),
            'final_price' => $this->sale_price ?: $this->price,
        ];
    }
}
