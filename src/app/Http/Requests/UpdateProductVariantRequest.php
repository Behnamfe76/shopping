<?php

namespace Fereydooni\Shopping\app\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Fereydooni\Shopping\app\DTOs\ProductVariantDTO;
use Fereydooni\Shopping\app\Models\ProductVariant;

class UpdateProductVariantRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $variant = $this->route('variant');
        return $this->user()->can('update', $variant);
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $variant = $this->route('variant');
        $rules = ProductVariantDTO::rules();

        // Update unique rules to exclude current variant
        $rules['sku'] = ['required', 'string', 'max:255', 'unique:product_variants,sku,' . $variant->id];
        $rules['barcode'] = ['nullable', 'string', 'max:255', 'unique:product_variants,barcode,' . $variant->id];

        return $rules;
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
            'updated_by' => 'updated by',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'updated_by' => $this->user()->id,
        ]);
    }
}
