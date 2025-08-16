<?php

namespace Fereydooni\Shopping\app\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('product'));
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $product = $this->route('product');

        return [
            'category_id' => ['sometimes', 'integer', 'exists:categories,id'],
            'brand_id' => ['nullable', 'integer', 'exists:brands,id'],
            'sku' => ['sometimes', 'string', 'max:100', Rule::unique('products', 'sku')->ignore($product->id)],
            'title' => ['sometimes', 'string', 'max:255'],
            'slug' => ['sometimes', 'string', 'max:255', Rule::unique('products', 'slug')->ignore($product->id)],
            'description' => ['nullable', 'string'],
            'weight' => ['nullable', 'numeric', 'min:0', 'decimal:0,10'],
            'dimensions' => ['nullable', 'string', 'max:255'],
            'status' => ['sometimes', 'string', 'in:draft,published,archived'],
            'product_type' => ['sometimes', 'string', 'in:physical,digital,subscription'],
            'price' => ['sometimes', 'numeric', 'min:0', 'decimal:0,10'],
            'sale_price' => ['nullable', 'numeric', 'min:0', 'decimal:0,10'],
            'cost_price' => ['nullable', 'numeric', 'min:0', 'decimal:0,10'],
            'stock_quantity' => ['sometimes', 'integer', 'min:0'],
            'min_stock_level' => ['sometimes', 'integer', 'min:0'],
            'max_stock_level' => ['nullable', 'integer', 'min:0'],
            'is_featured' => ['sometimes', 'boolean'],
            'is_active' => ['sometimes', 'boolean'],
            'sort_order' => ['sometimes', 'integer', 'min:0'],
            'meta_title' => ['nullable', 'string', 'max:60'],
            'meta_description' => ['nullable', 'string', 'max:160'],
            'meta_keywords' => ['nullable', 'string'],
            'seo_url' => ['nullable', 'string', 'max:255'],
            'canonical_url' => ['nullable', 'string', 'max:255', 'url'],
            'og_image' => ['nullable', 'string', 'max:255', 'url'],
            'twitter_image' => ['nullable', 'string', 'max:255', 'url'],
            'video_url' => ['nullable', 'string', 'max:255', 'url'],
            'warranty_info' => ['nullable', 'string'],
            'shipping_info' => ['nullable', 'string'],
            'return_policy' => ['nullable', 'string'],
            'tags' => ['nullable', 'string'],
            'attributes' => ['nullable', 'string'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'category_id.exists' => 'Selected category does not exist.',
            'brand_id.exists' => 'Selected brand does not exist.',
            'sku.unique' => 'SKU must be unique.',
            'slug.unique' => 'Slug must be unique.',
            'status.in' => 'Status must be draft, published, or archived.',
            'product_type.in' => 'Product type must be physical, digital, or subscription.',
            'price.min' => 'Price must be greater than or equal to 0.',
            'stock_quantity.min' => 'Stock quantity must be greater than or equal to 0.',
            'min_stock_level.min' => 'Minimum stock level must be greater than or equal to 0.',
            'sort_order.min' => 'Sort order must be greater than or equal to 0.',
        ];
    }
}
