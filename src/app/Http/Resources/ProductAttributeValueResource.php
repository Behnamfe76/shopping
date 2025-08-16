<?php

namespace Fereydooni\Shopping\app\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductAttributeValueResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'attribute_id' => $this->attribute_id,
            'value' => $this->value,
            'slug' => $this->slug,
            'description' => $this->description,
            'sort_order' => $this->sort_order,
            'is_active' => $this->is_active,
            'is_default' => $this->is_default,
            'color_code' => $this->color_code,
            'image_url' => $this->image_url,
            'meta_data' => $this->meta_data,
            'usage_count' => $this->usage_count,
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),

            // Relationships
            'attribute' => $this->whenLoaded('attribute', function () {
                return new ProductAttributeResource($this->attribute);
            }),
            'creator' => $this->whenLoaded('creator', function () {
                return [
                    'id' => $this->creator->id,
                    'name' => $this->creator->name,
                    'email' => $this->creator->email,
                ];
            }),
            'updater' => $this->whenLoaded('updater', function () {
                return [
                    'id' => $this->updater->id,
                    'name' => $this->updater->name,
                    'email' => $this->updater->email,
                ];
            }),
            'variants' => $this->whenLoaded('variants', function () {
                return $this->variants->map(function ($variant) {
                    return [
                        'id' => $variant->id,
                        'name' => $variant->name,
                        'sku' => $variant->sku,
                    ];
                });
            }),

            // Computed attributes
            'formatted_color_code' => $this->formatted_color_code,
            'is_used' => $this->is_used,
            'display_value' => $this->display_value,

            // Links
            'links' => [
                'self' => route('api.v1.product-attribute-values.show', $this->id),
                'edit' => route('api.v1.product-attribute-values.update', $this->id),
                'delete' => route('api.v1.product-attribute-values.destroy', $this->id),
                'toggle_active' => route('api.v1.product-attribute-values.toggle-active', $this->id),
                'toggle_default' => route('api.v1.product-attribute-values.toggle-default', $this->id),
                'set_default' => route('api.v1.product-attribute-values.set-default', $this->id),
            ],
        ];
    }
}
