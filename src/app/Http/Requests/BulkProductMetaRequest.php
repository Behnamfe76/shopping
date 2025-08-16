<?php

namespace Fereydooni\Shopping\app\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BulkProductMetaRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('bulkManage', \Fereydooni\Shopping\app\Models\ProductMeta::class);
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'meta_data' => 'required|array|min:1',
            'meta_data.*.meta_key' => 'required|string|max:255',
            'meta_data.*.meta_value' => 'required|string|max:65535',
            'meta_data.*.meta_type' => 'sometimes|string|max:50',
            'meta_data.*.is_public' => 'sometimes|boolean',
            'meta_data.*.is_searchable' => 'sometimes|boolean',
            'meta_data.*.is_filterable' => 'sometimes|boolean',
            'meta_data.*.sort_order' => 'sometimes|integer|min:0',
            'meta_data.*.description' => 'sometimes|string|max:1000',
            'meta_data.*.validation_rules' => 'sometimes|string|max:1000',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'meta_data.required' => 'Meta data is required.',
            'meta_data.array' => 'Meta data must be an array.',
            'meta_data.min' => 'At least one meta item is required.',
            'meta_data.*.meta_key.required' => 'Meta key is required.',
            'meta_data.*.meta_key.max' => 'Meta key cannot exceed 255 characters.',
            'meta_data.*.meta_value.required' => 'Meta value is required.',
            'meta_data.*.meta_value.max' => 'Meta value cannot exceed 65535 characters.',
            'meta_data.*.meta_type.max' => 'Meta type cannot exceed 50 characters.',
            'meta_data.*.is_public.boolean' => 'Public flag must be true or false.',
            'meta_data.*.is_searchable.boolean' => 'Searchable flag must be true or false.',
            'meta_data.*.is_filterable.boolean' => 'Filterable flag must be true or false.',
            'meta_data.*.sort_order.integer' => 'Sort order must be an integer.',
            'meta_data.*.sort_order.min' => 'Sort order cannot be negative.',
            'meta_data.*.description.max' => 'Description cannot exceed 1000 characters.',
            'meta_data.*.validation_rules.max' => 'Validation rules cannot exceed 1000 characters.',
        ];
    }
}
