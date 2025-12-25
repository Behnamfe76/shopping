<?php

namespace Fereydooni\Shopping\app\Http\Requests;

use Fereydooni\Shopping\app\DTOs\ProductVariantDTO;
use Illuminate\Foundation\Http\FormRequest;

class StoreProductVariantRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('product-variant.create');
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return ProductVariantDTO::rules();
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return ProductVariantDTO::messages();
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'product_id' => 'product',
            'sku' => 'SKU',
            'price' => 'price',
            'stock_quantity' => 'stock quantity',
            'weight' => 'weight',
            'dimensions' => 'dimensions',
            'barcode' => 'barcode',
            'is_active' => 'active status',
            'is_featured' => 'featured status',
            'sort_order' => 'sort order',
            'cost_price' => 'cost price',
            'sale_price' => 'sale price',
            'compare_price' => 'compare price',
            'inventory_tracking' => 'inventory tracking',
            'low_stock_threshold' => 'low stock threshold',
            'reserved_stock' => 'reserved stock',
            'available_stock' => 'available stock',
            'created_by' => 'created by',
            'updated_by' => 'updated by',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'created_by' => $this->user()->id,
            'updated_by' => $this->user()->id,
        ]);
    }
}
