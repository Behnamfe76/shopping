<?php

namespace Fereydooni\Shopping\app\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('create', \Fereydooni\Shopping\app\Models\Product::class);
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'category_id' => ['required', 'integer', 'exists:categories,id'],
            'brand_id' => ['nullable', 'integer', 'exists:brands,id'],
            'sku' => ['required', 'string', 'max:100', 'unique:products,sku'],
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', 'unique:products,slug'],
            'description' => ['nullable', 'string'],
            'weight' => ['nullable', 'numeric', 'min:0', 'decimal:0,10'],
            'dimensions' => ['nullable', 'string', 'max:255'],
            'status' => ['required', 'string', 'in:draft,published,archived'],
            'product_type' => ['required', 'string', 'in:physical,digital,subscription'],
            'price' => ['required', 'numeric', 'min:0', 'decimal:0,10'],
            'sale_price' => ['nullable', 'numeric', 'min:0', 'decimal:0,10'],
            'cost_price' => ['nullable', 'numeric', 'min:0', 'decimal:0,10'],
            'stock_quantity' => ['required', 'integer', 'min:0'],
            'min_stock_level' => ['required', 'integer', 'min:0'],
            'max_stock_level' => ['nullable', 'integer', 'min:0'],
            'is_featured' => ['required', 'boolean'],
            'is_active' => ['required', 'boolean'],
            'sort_order' => ['required', 'integer', 'min:0'],
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
            'category_id.required' => 'Category is required.',
            'category_id.exists' => 'Selected category does not exist.',
            'brand_id.exists' => 'Selected brand does not exist.',
            'sku.required' => 'SKU is required.',
            'sku.unique' => 'SKU must be unique.',
            'title.required' => 'Title is required.',
            'slug.required' => 'Slug is required.',
            'slug.unique' => 'Slug must be unique.',
            'status.required' => 'Status is required.',
            'status.in' => 'Status must be draft, published, or archived.',
            'product_type.required' => 'Product type is required.',
            'product_type.in' => 'Product type must be physical, digital, or subscription.',
            'price.required' => 'Price is required.',
            'price.min' => 'Price must be greater than or equal to 0.',
            'stock_quantity.required' => 'Stock quantity is required.',
            'stock_quantity.min' => 'Stock quantity must be greater than or equal to 0.',
            'min_stock_level.required' => 'Minimum stock level is required.',
            'min_stock_level.min' => 'Minimum stock level must be greater than or equal to 0.',
            'is_featured.required' => 'Featured status is required.',
            'is_active.required' => 'Active status is required.',
            'sort_order.required' => 'Sort order is required.',
            'sort_order.min' => 'Sort order must be greater than or equal to 0.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'category_id' => 'category',
            'brand_id' => 'brand',
            'sku' => 'SKU',
            'title' => 'title',
            'slug' => 'slug',
            'description' => 'description',
            'weight' => 'weight',
            'dimensions' => 'dimensions',
            'status' => 'status',
            'product_type' => 'product type',
            'price' => 'price',
            'sale_price' => 'sale price',
            'cost_price' => 'cost price',
            'stock_quantity' => 'stock quantity',
            'min_stock_level' => 'minimum stock level',
            'max_stock_level' => 'maximum stock level',
            'is_featured' => 'featured status',
            'is_active' => 'active status',
            'sort_order' => 'sort order',
            'meta_title' => 'meta title',
            'meta_description' => 'meta description',
            'meta_keywords' => 'meta keywords',
            'seo_url' => 'SEO URL',
            'canonical_url' => 'canonical URL',
            'og_image' => 'Open Graph image',
            'twitter_image' => 'Twitter image',
            'video_url' => 'video URL',
            'warranty_info' => 'warranty information',
            'shipping_info' => 'shipping information',
            'return_policy' => 'return policy',
            'tags' => 'tags',
            'attributes' => 'attributes',
        ];
    }
}
