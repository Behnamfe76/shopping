<?php

namespace Fereydooni\Shopping\app\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductVariantResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'product_id' => $this->product_id,
            'sku' => $this->sku,
            'price' => $this->price,
            'stock' => $this->stock,
            'weight' => $this->when($this->weight, $this->weight),
            'dimensions' => $this->when($this->dimensions, $this->dimensions),
            'barcode' => $this->when($this->barcode, $this->barcode),
            'is_active' => $this->is_active,
            'is_featured' => $this->is_featured,
            'sort_order' => $this->sort_order,
            'cost_price' => $this->when($this->cost_price, $this->cost_price),
            'sale_price' => $this->when($this->sale_price, $this->sale_price),
            'compare_price' => $this->when($this->compare_price, $this->compare_price),
            'inventory_tracking' => $this->inventory_tracking,
            'low_stock_threshold' => $this->low_stock_threshold,
            'reserved_stock' => $this->reserved_stock,
            'available_stock' => $this->available_stock,
            'created_by' => $this->when($this->created_by, $this->created_by),
            'updated_by' => $this->when($this->updated_by, $this->updated_by),
            'created_at' => $this->when($this->created_at, $this->created_at?->toISOString()),
            'updated_at' => $this->when($this->updated_at, $this->updated_at?->toISOString()),

            // Relationships
            'product' => $this->whenLoaded('product', function () {
                return new ProductResource($this->product);
            }),
            'attribute_values' => $this->whenLoaded('attributeValues', function () {
                return ProductAttributeValueResource::collection($this->attributeValues);
            }),

            // Computed fields
            'is_in_stock' => $this->stock > 0,
            'is_low_stock' => $this->stock <= $this->low_stock_threshold && $this->stock > 0,
            'is_out_of_stock' => $this->stock <= 0,
            'current_price' => $this->sale_price ?? $this->price,
            'has_discount' => $this->sale_price && $this->sale_price < $this->price,
            'discount_percentage' => $this->when($this->sale_price && $this->price > 0, function () {
                return round((($this->price - $this->sale_price) / $this->price) * 100, 2);
            }),
        ];
    }
}
