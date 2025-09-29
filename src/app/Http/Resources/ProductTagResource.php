<?php

namespace Fereydooni\Shopping\app\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Fereydooni\Shopping\app\Models\ProductTag;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductTagResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        /** @var ProductTag $this */
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'color' => $this->color,
            'icon' => $this->icon,
            'is_active' => $this->is_active,
            'is_featured' => $this->is_featured,
            'sort_order' => $this->sort_order,
            'usage_count' => $this->usage_count,
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
            'created_at' => $this->created_at instanceof Carbon ? $this->created_at->toISOString() : $this->created_at,
            'updated_at'=> $this->updated_at instanceof Carbon ? $this->updated_at->toISOString() : $this->updated_at,

            // Conditional fields
            'status_label' => $this->when($this->is_active, 'Active', 'Inactive'),
            'featured_label' => $this->when($this->is_featured, 'Featured', 'Not Featured'),
            'usage_label' => $this->when($this->usage_count > 0, "Used {$this->usage_count} times", 'Not used'),

            // Relationships
            'products' => $this->whenLoaded('products', function () {
                return $this->products->map(function ($product) {
                    return [
                        'id' => $product->id,
                        'name' => $product->title,
                        'slug' => $product->slug,
                        'sku' => $product->sku,
                        'price' => $product->price,
                        'status' => $product->status?->value,
                    ];
                });
            }),

            // Analytics data (when available)
            'analytics' => $this->when($request->has('include_analytics'), function () {
                return [
                    'total_products' => $this->products_count ?? 0,
                    'popularity_score' => $this->usage_count,
                    'performance_rating' => $this->when($this->usage_count > 0,
                        min(100, ($this->usage_count / 10) * 100), 0),
                ];
            }),

            // Links
            // 'links' => [
            //     'self' => route('shopping.product-tags.show', $this->slug),
            //     'edit' => route('shopping.product-tags.edit', $this->slug),
            //     'delete' => route('shopping.product-tags.destroy', $this->slug),
            //     'toggle_active' => route('shopping.product-tags.toggle-active', $this->slug),
            //     'toggle_featured' => route('shopping.product-tags.toggle-featured', $this->slug),
            //     'related' => route('shopping.product-tags.related', $this->slug),
            //     'analytics' => route('shopping.product-tags.analytics', $this->slug),
            // ],
        ];
    }
}
