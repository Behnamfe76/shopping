<?php

namespace Fereydooni\Shopping\app\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BulkProductVariantRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('bulkManage', \Fereydooni\Shopping\app\Models\ProductVariant::class);
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'variants' => ['required', 'array', 'min:1'],
            'variants.*.sku' => ['required', 'string', 'max:255'],
            'variants.*.price' => ['required', 'numeric', 'min:0'],
            'variants.*.stock' => ['required', 'integer', 'min:0'],
            'variants.*.weight' => ['nullable', 'numeric', 'min:0'],
            'variants.*.dimensions' => ['nullable', 'string', 'max:255'],
            'variants.*.barcode' => ['nullable', 'string', 'max:255'],
            'variants.*.is_active' => ['boolean'],
            'variants.*.is_featured' => ['boolean'],
            'variants.*.sort_order' => ['integer', 'min:0'],
            'variants.*.cost_price' => ['nullable', 'numeric', 'min:0'],
            'variants.*.sale_price' => ['nullable', 'numeric', 'min:0'],
            'variants.*.compare_price' => ['nullable', 'numeric', 'min:0'],
            'variants.*.inventory_tracking' => ['string', 'max:50', 'in:track,untrack'],
            'variants.*.low_stock_threshold' => ['integer', 'min:0'],
            'variant_ids' => ['required_without:variants', 'array', 'min:1'],
            'variant_ids.*' => ['integer', 'exists:product_variants,id'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'variants.required' => 'Variants data is required.',
            'variants.array' => 'Variants must be an array.',
            'variants.min' => 'At least one variant is required.',
            'variants.*.sku.required' => 'SKU is required for each variant.',
            'variants.*.sku.max' => 'SKU must not exceed 255 characters.',
            'variants.*.price.required' => 'Price is required for each variant.',
            'variants.*.price.numeric' => 'Price must be a number.',
            'variants.*.price.min' => 'Price must be at least 0.',
            'variants.*.stock.required' => 'Stock quantity is required for each variant.',
            'variants.*.stock.integer' => 'Stock quantity must be a whole number.',
            'variants.*.stock.min' => 'Stock quantity must be at least 0.',
            'variants.*.weight.numeric' => 'Weight must be a number.',
            'variants.*.weight.min' => 'Weight must be at least 0.',
            'variants.*.dimensions.max' => 'Dimensions must not exceed 255 characters.',
            'variants.*.barcode.max' => 'Barcode must not exceed 255 characters.',
            'variants.*.sort_order.integer' => 'Sort order must be a whole number.',
            'variants.*.sort_order.min' => 'Sort order must be at least 0.',
            'variants.*.cost_price.numeric' => 'Cost price must be a number.',
            'variants.*.cost_price.min' => 'Cost price must be at least 0.',
            'variants.*.sale_price.numeric' => 'Sale price must be a number.',
            'variants.*.sale_price.min' => 'Sale price must be at least 0.',
            'variants.*.compare_price.numeric' => 'Compare price must be a number.',
            'variants.*.compare_price.min' => 'Compare price must be at least 0.',
            'variants.*.inventory_tracking.in' => 'Inventory tracking must be either track or untrack.',
            'variants.*.low_stock_threshold.integer' => 'Low stock threshold must be a whole number.',
            'variants.*.low_stock_threshold.min' => 'Low stock threshold must be at least 0.',
            'variant_ids.required_without' => 'Variant IDs are required when not providing variant data.',
            'variant_ids.array' => 'Variant IDs must be an array.',
            'variant_ids.min' => 'At least one variant ID is required.',
            'variant_ids.*.integer' => 'Variant ID must be a whole number.',
            'variant_ids.*.exists' => 'Selected variant does not exist.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'variants' => 'variants',
            'variants.*.sku' => 'SKU',
            'variants.*.price' => 'price',
            'variants.*.stock' => 'stock quantity',
            'variants.*.weight' => 'weight',
            'variants.*.dimensions' => 'dimensions',
            'variants.*.barcode' => 'barcode',
            'variants.*.is_active' => 'active status',
            'variants.*.is_featured' => 'featured status',
            'variants.*.sort_order' => 'sort order',
            'variants.*.cost_price' => 'cost price',
            'variants.*.sale_price' => 'sale price',
            'variants.*.compare_price' => 'compare price',
            'variants.*.inventory_tracking' => 'inventory tracking',
            'variants.*.low_stock_threshold' => 'low stock threshold',
            'variant_ids' => 'variant IDs',
            'variant_ids.*' => 'variant ID',
        ];
    }
}
