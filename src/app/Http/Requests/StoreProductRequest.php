<?php

namespace Fereydooni\Shopping\app\Http\Requests;

use Fereydooni\Shopping\app\Enums\ProductStatus;
use Fereydooni\Shopping\app\Enums\ProductType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {

        $productAttributeRules = ['nullable'];
        $productMultiAttributeRules = ['nullable'];
        if ($this->has('has_variant')) {
            if ($this->input('has_variant') === 'one') {
                $productAttributeRules = ['required_if:has_variant,one', 'string', 'max:64'];
            } elseif ($this->input('has_variant') === 'more_than_one') {
                $productMultiAttributeRules = ['required_if:has_variant,more_than_one', 'array', 'min:1'];
            }
        }

        $additionalRules = [];

        if ($this->has('product_multi_attributes')) {
            $attrs = $this->input('product_multi_attributes');
            if (is_array($attrs) && count($attrs) > 0) {
                // Rules for the base variant fields
                $additionalRules['product_multiple_variants.*.variant_stock'] = ['required_with:product_multi_attributes', 'integer', 'min:0'];
                $additionalRules['product_multiple_variants.*.variant_price'] = ['required_with:product_multi_attributes', 'numeric', 'min:0', 'decimal:0,10'];
                $additionalRules['product_multiple_variants.*.variant_sale_price'] = ['nullable', 'numeric', 'min:0', 'decimal:0,10'];
                $additionalRules['product_multiple_variants.*.variant_cost_price'] = ['nullable', 'numeric', 'min:0', 'decimal:0,10'];
                $additionalRules['product_multiple_variants.*.variant_description'] = ['nullable', 'string', 'max:65535'];

                // Rules for each attribute
                foreach ($attrs as $attribute) {
                    $additionalRules["product_multiple_variants.*.$attribute"] = ['required_with:product_multi_attributes', 'array'];
                    $additionalRules["product_multiple_variants.*.$attribute.variant_name"] = ['required_with:product_multi_attributes', 'string', 'max:64'];
                }
            }
        }

        $images = $this->file('images', []);
        if (! empty($images)) {
            if (is_array($images)) {
                $additionalRules['images'] = ['nullable', 'max:45', 'array'];
                $additionalRules['images.*'] = ['required_with:images', 'max:15200', 'image'];
            } else {
                $additionalRules['images'] = ['nullable', 'max:15200', 'image'];
            }
        }
        $videos = $this->file('videos', []);

        if (! empty($videos)) {
            if (is_array($videos)) {
                $additionalRules['videos'] = ['nullable', 'array', 'max:45'];
                $additionalRules['videos.*'] = [
                    'required_with:videos',
                    'file',
                    'max:51200', // in kilobytes (50 MB)
                    'mimetypes:video/mp4,video/avi,video/mpeg,video/quicktime',
                ];
            } else {
                $additionalRules['videos'] = [
                    'nullable',
                    'file',
                    'max:51200', // 50 MB
                    'mimetypes:video/mp4,video/avi,video/mpeg,video/quicktime',
                ];
            }
        }

        return array_merge([
            'main_image' => ['required', 'max:15200', 'image'],
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', 'unique:products,slug'],
            'description' => ['nullable', 'string', 'max:65535'],
            'category_id' => ['required', 'integer', 'exists:categories,id'],
            'brand_id' => ['nullable', 'integer', 'exists:brands,id'],
            'status' => ['required', 'integer', Rule::enum(ProductStatus::class)],
            'product_type' => ['required', 'integer', Rule::enum(ProductType::class)],
            'has_variant' => ['required', 'in:none,one,more_than_one'],
            'stock_quantity' => ['required_if::has_variant,none', 'integer', 'min:0'],
            'price' => ['required_if:has_variant,none', 'numeric', 'min:0', 'decimal:0,10'],
            'sale_price' => ['nullable', 'numeric', 'min:0', 'decimal:0,10'],
            'cost_price' => ['nullable', 'numeric', 'min:0', 'decimal:0,10'],
            'specifications' => ['nullable', 'array', 'max:256'],

            'meta_title' => ['nullable', 'string', 'max:60'],
            'meta_description' => ['nullable', 'string', 'max:160'],
            'meta_keywords' => ['nullable', 'string'],
            'product_tags' => ['nullable', 'array'],
            'product_tags.*' => ['integer', 'exists:product_tags,id'],
            'product_attribute' => $productAttributeRules,
            'product_multi_attributes' => $productMultiAttributeRules,

            'product_single_variants' => ['required_with:product_attribute', 'array', 'min:1'],
            'product_single_variants.*.variant_name' => ['required_with:product_attribute', 'string', 'max:64'],
            'product_single_variants.*.variant_description' => ['nullable', 'string', 'max:65535'],
            'product_single_variants.*.variant_stock' => ['required_with:product_attribute', 'integer', 'min:0'],
            'product_single_variants.*.variant_price' => ['required_with:product_attribute', 'numeric', 'min:0', 'decimal:0,10'],
            'product_single_variants.*.variant_sale_price' => ['nullable', 'numeric', 'min:0', 'decimal:0,10'],
            'product_single_variants.*.variant_cost_price' => ['nullable', 'numeric', 'min:0', 'decimal:0,10'],

            'product_multiple_variants' => ['required_with:product_multi_attributes', 'array', 'min:1'],
        ], $additionalRules);
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
            'product_tags' => 'product tags',
            'attributes' => 'attributes',
        ];
    }

    public function prepareForValidation(): void
    {
        if ($this->has('specifications')) {
            $this->merge([
                'specifications' => json_decode($this->input('specifications'), true),
            ]);
        }

        if ($this->has('status')) {
            $this->merge([
                'status' => ProductStatus::fromString($this->input('status'))->value,
            ]);
        }

        if ($this->has('product_type')) {
            $this->merge([
                'product_type' => ProductType::fromString($this->input('product_type'))->value,
            ]);
        }
    }
}
