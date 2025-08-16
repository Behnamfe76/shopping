<?php

namespace Fereydooni\Shopping\app\DTOs;

use Spatie\LaravelData\Data;
use Fereydooni\Shopping\app\Models\ProductVariant;
use Illuminate\Support\Carbon;

class ProductVariantDTO extends Data
{
    public function __construct(
        public int $product_id,
        public string $sku,
        public float $price,
        public int $stock,
        public ?float $weight = null,
        public ?string $dimensions = null,
        public ?string $barcode = null,
        public bool $is_active = true,
        public bool $is_featured = false,
        public int $sort_order = 0,
        public ?float $cost_price = null,
        public ?float $sale_price = null,
        public ?float $compare_price = null,
        public string $inventory_tracking = 'track',
        public int $low_stock_threshold = 10,
        public int $reserved_stock = 0,
        public int $available_stock = 0,
        public ?int $created_by = null,
        public ?int $updated_by = null,
        public ?Carbon $created_at = null,
        public ?Carbon $updated_at = null,
        public ?int $id = null,
    ) {
    }

    public static function fromModel(ProductVariant $variant): self
    {
        return new self(
            product_id: $variant->product_id,
            sku: $variant->sku,
            price: $variant->price ?? 0.0,
            stock: $variant->stock ?? 0,
            weight: $variant->weight,
            dimensions: $variant->dimensions,
            barcode: $variant->barcode,
            is_active: $variant->is_active ?? true,
            is_featured: $variant->is_featured ?? false,
            sort_order: $variant->sort_order ?? 0,
            cost_price: $variant->cost_price,
            sale_price: $variant->sale_price,
            compare_price: $variant->compare_price,
            inventory_tracking: $variant->inventory_tracking ?? 'track',
            low_stock_threshold: $variant->low_stock_threshold ?? 10,
            reserved_stock: $variant->reserved_stock ?? 0,
            available_stock: $variant->available_stock ?? 0,
            created_by: $variant->created_by,
            updated_by: $variant->updated_by,
            created_at: $variant->created_at,
            updated_at: $variant->updated_at,
            id: $variant->id,
        );
    }

    public static function rules(): array
    {
        return [
            'product_id' => ['required', 'integer', 'min:1', 'exists:products,id'],
            'sku' => ['required', 'string', 'max:255', 'unique:product_variants,sku'],
            'price' => ['required', 'numeric', 'min:0'],
            'stock' => ['required', 'integer', 'min:0'],
            'weight' => ['nullable', 'numeric', 'min:0'],
            'dimensions' => ['nullable', 'string', 'max:255'],
            'barcode' => ['nullable', 'string', 'max:255', 'unique:product_variants,barcode'],
            'is_active' => ['boolean'],
            'is_featured' => ['boolean'],
            'sort_order' => ['integer', 'min:0'],
            'cost_price' => ['nullable', 'numeric', 'min:0'],
            'sale_price' => ['nullable', 'numeric', 'min:0'],
            'compare_price' => ['nullable', 'numeric', 'min:0'],
            'inventory_tracking' => ['string', 'max:50', 'in:track,untrack'],
            'low_stock_threshold' => ['integer', 'min:0'],
            'reserved_stock' => ['integer', 'min:0'],
            'available_stock' => ['integer', 'min:0'],
            'created_by' => ['nullable', 'integer', 'exists:users,id'],
            'updated_by' => ['nullable', 'integer', 'exists:users,id'],
        ];
    }

    public static function messages(): array
    {
        return [
            'product_id.required' => 'Product ID is required.',
            'product_id.exists' => 'The selected product does not exist.',
            'sku.required' => 'SKU is required.',
            'sku.unique' => 'This SKU is already in use.',
            'price.required' => 'Price is required.',
            'price.min' => 'Price must be at least 0.',
            'stock.required' => 'Stock quantity is required.',
            'stock.min' => 'Stock quantity must be at least 0.',
            'barcode.unique' => 'This barcode is already in use.',
            'inventory_tracking.in' => 'Inventory tracking must be either track or untrack.',
        ];
    }
}
