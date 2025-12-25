<?php

namespace Fereydooni\Shopping\app\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AssignProductAttributeValueRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('manageRelationships', \Fereydooni\Shopping\app\Models\ProductAttributeValue::class);
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'variant_id' => 'nullable|integer|exists:product_variants,id',
            'product_id' => 'nullable|integer|exists:products,id',
            'category_id' => 'nullable|integer|exists:categories,id',
            'brand_id' => 'nullable|integer|exists:brands,id',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'variant_id.integer' => 'Variant ID must be a number',
            'variant_id.exists' => 'Selected variant does not exist',
            'product_id.integer' => 'Product ID must be a number',
            'product_id.exists' => 'Selected product does not exist',
            'category_id.integer' => 'Category ID must be a number',
            'category_id.exists' => 'Selected category does not exist',
            'brand_id.integer' => 'Brand ID must be a number',
            'brand_id.exists' => 'Selected brand does not exist',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'variant_id' => 'variant',
            'product_id' => 'product',
            'category_id' => 'category',
            'brand_id' => 'brand',
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $hasAnyId = $this->input('variant_id') ||
                       $this->input('product_id') ||
                       $this->input('category_id') ||
                       $this->input('brand_id');

            if (! $hasAnyId) {
                $validator->errors()->add('general', 'At least one entity ID must be provided.');
            }
        });
    }
}
