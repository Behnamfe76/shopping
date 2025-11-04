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
            'title' => $this->title,
            'slug' => $this->slug,
            'description' => $this->description,
            'status' => [
                'id' => $this->status->toString(),
                'label' => __("products.statuses." . $this->status->toString())
            ],
            'product_type' => [
                'id' => $this->product_type->toString(),
                'label' => __("products.types." . $this->product_type->toString())
            ],
            'specifications' => json_encode($this->specifications),
            'main_image' => $this->main_image,
            'images' => $this->images,
            'videos' => $this->videos,
            'price' => $this->price,
            'sale_price' => $this->sale_price,
            'cost_price' => $this->cost_price,
            'stock_quantity' => $this->stock_quantity,
            'has_variant' => !$this->has_variant ? 'none' : ($this->multi_variant ? 'more_than_one' : 'one'),
            'product_attribute' => $this->has_variant && !$this->multi_variant
                ? [
                    'id' => $this->productAttribute()->value('slug'),
                    'label' => $this->productAttribute()->value('name')
                ]
                : null,
            'product_multi_attributes' => $this->has_variant && $this->multi_variant
                ?
                $this->productAttributes()->select('slug', 'name')->get()->map(function ($attr) {
                    return [
                        'id' => $attr->slug,
                        'label' => $attr->name
                    ];
                })
                : null,
            // 'min_stock_level' => $this->min_stock_level,
            // 'max_stock_level' => $this->max_stock_level,
            // 'is_featured' => $this->is_featured,
            // 'is_active' => $this->is_active,
            // 'sort_order' => $this->sort_order,
            'meta_title' => $this->meta_title,
            'meta_description' => $this->meta_description,
            'meta_keywords' => $this->meta_keywords,
            // 'seo_url' => $this->seo_url,
            // 'canonical_url' => $this->canonical_url,
            // 'og_image' => $this->og_image,
            // 'twitter_image' => $this->twitter_image,
            // 'video_url' => $this->video_url,
            // 'warranty_info' => $this->warranty_info,
            // 'shipping_info' => $this->shipping_info,
            // 'return_policy' => $this->return_policy,
            // 'attributes' => $this->attributes,
            // 'variants_count' => $this->variants_count,
            // 'reviews_count' => $this->reviews_count,
            // 'average_rating' => $this->average_rating,
            // 'total_sales' => $this->total_sales,
            // 'view_count' => $this->view_count,
            // 'wishlist_count' => $this->wishlist_count,
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),

            // Relationships
            'category' => $this->whenLoaded('category', function () {
                return [
                    'id' => $this->category->id,
                    'label' => $this->category->name,
                ];
            }),
            'product_tags' => $this->whenLoaded('tags', function () {
                return $this->tags->map(function ($tag) {
                    return [
                        'id' => $tag->id,
                        'label' => $tag->name,
                    ];
                });
            }),
            'brand' => $this->whenLoaded('brand', function () {
                return [
                    'id' => $this->brand->id,
                    'label' => $this->brand->name,
                ];
            }),



            // 'media' => $this->whenLoaded('media', function () {
            //     return $this->media->map(function ($media) {
            //         return [
            //             'id' => $media->id,
            //             'name' => $media->name,
            //             'file_name' => $media->file_name,
            //             'mime_type' => $media->mime_type,
            //             'size' => $media->size,
            //             'url' => $media->getUrl(),
            //             'thumbnail_url' => $media->getUrl('thumbnail'),
            //             'collection_name' => $media->collection_name,
            //         ];
            //     });
            // }),

            'product_single_variants' => $this->when($this->has_variant && !$this->multi_variant, function () {
                return $this->variants->map(function ($variant) {
                    return [
                        'id' => $variant->id,
                        'variant_name' => $variant->name,
                        // 'variant_sku' => $variant->sku,
                        'variant_price' => $variant->price,
                        'variant_sale_price' => $variant->sale_price,
                        'variant_cost_price' => $variant->cost_price,
                        'variant_stock' => $variant->stock_quantity,
                        'variant_description' => $variant->description,
                        // 'variant_in_stock' => $variant->in_stock,
                        'attribute_values' => $variant->attributeValues->map(function ($value) {
                            return [
                                'id' => $value->id,
                                'name' => $value->name,
                                'value' => $value->value,
                            ];
                        }),
                    ];
                });
            }),

            'product_multiple_variants' => $this->when($this->has_variant && $this->multi_variant, function () {
                return $this->variants->map(function ($variant) {
                    $variantNames = $variant->attributeValues->map(function ($item) {
                        return [$item->attribute->slug => $item->value];
                    })->collapse()->toArray();

                    return array_merge($variantNames, [
                        'id' => $variant->id,
                        'variant_name' => $variant->name,
                        // 'variant_sku' => $variant->sku,
                        'variant_price' => $variant->price,
                        'variant_sale_price' => $variant->sale_price,
                        'variant_cost_price' => $variant->cost_price,
                        'variant_stock' => $variant->stock_quantity,
                        'variant_description' => $variant->description,
                        'repeater_dependencies' => array_keys($variantNames),
                        // 'variant_in_stock' => $variant->in_stock,
                        // 'attribute_values' => $variant->attributeValues->map(function ($value) {
                        //     return [
                        //         'id' => $value->id,
                        //         'name' => $value->value,
                        //         'value' => $value->value,
                        //     ];
                        // }),
                    ]);
                });
            }),

            // 'variants' => $this->whenLoaded('variants', function () {
            //     return $this->variants->map(function ($variant) {
            //         return [
            //             'id' => $variant->id,
            //             'name' => $variant->name,
            //             'sku' => $variant->sku,
            //             'price' => $variant->price,
            //             'stock_quantity' => $variant->stock_quantity,
            //         ];
            //     });
            // }),

            // 'reviews' => $this->whenLoaded('reviews', function () {
            //     return $this->reviews->map(function ($review) {
            //         return [
            //             'id' => $review->id,
            //             'rating' => $review->rating,
            //             'comment' => $review->comment,
            //             'created_at' => $review->created_at?->toISOString(),
            //         ];
            //     });
            // }),

            // // Computed fields
            // 'is_in_stock' => $this->stock_quantity > 0,
            // 'is_low_stock' => $this->stock_quantity <= $this->min_stock_level,
            // 'is_on_sale' => $this->sale_price && $this->sale_price > 0,
            // 'discount_percentage' => $this->when($this->sale_price && $this->price > 0, function () {
            //     return round((($this->price - $this->sale_price) / $this->price) * 100, 2);
            // }),
            // 'final_price' => $this->sale_price ?: $this->price,
        ];
    }
}
