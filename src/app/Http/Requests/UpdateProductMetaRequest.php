<?php

namespace Fereydooni\Shopping\app\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Fereydooni\Shopping\app\DTOs\ProductMetaDTO;

class UpdateProductMetaRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('meta'));
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $rules = ProductMetaDTO::rules();

        // Make product_id optional for updates
        unset($rules['product_id']);

        return $rules;
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
