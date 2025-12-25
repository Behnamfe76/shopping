<?php

namespace Fereydooni\Shopping\app\Http\Requests;

use Fereydooni\Shopping\app\DTOs\ProductMetaDTO;
use Illuminate\Foundation\Http\FormRequest;

class StoreProductMetaRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('create', \Fereydooni\Shopping\app\Models\ProductMeta::class);
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return ProductMetaDTO::rules();
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return ProductMetaDTO::messages();
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'product_id' => 'product',
            'meta_key' => 'meta key',
            'meta_value' => 'meta value',
            'meta_type' => 'meta type',
            'is_public' => 'public status',
            'is_searchable' => 'searchable status',
            'is_filterable' => 'filterable status',
            'sort_order' => 'sort order',
            'description' => 'description',
            'validation_rules' => 'validation rules',
        ];
    }
}
