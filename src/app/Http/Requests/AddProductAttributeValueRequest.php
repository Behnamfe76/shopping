<?php

namespace Fereydooni\Shopping\app\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Fereydooni\Shopping\app\Models\ProductAttribute;

class AddProductAttributeValueRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $attribute = $this->route('attribute');
        return $this->user()->can('manageValues', $attribute);
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'value' => 'required|string|max:255',
            'metadata' => 'nullable|array',
            'metadata.*' => 'nullable|string|max:500',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'value.required' => 'The attribute value is required.',
            'value.max' => 'The attribute value cannot exceed 255 characters.',
            'metadata.array' => 'Metadata must be an array.',
            'metadata.*.max' => 'Metadata values cannot exceed 500 characters.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'value' => 'attribute value',
            'metadata' => 'metadata',
        ];
    }
}

