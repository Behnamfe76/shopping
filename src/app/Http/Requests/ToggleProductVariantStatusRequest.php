<?php

namespace Fereydooni\Shopping\app\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ToggleProductVariantStatusRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $variant = $this->route('variant');

        return $this->user()->can('toggleActive', $variant) || $this->user()->can('toggleFeatured', $variant);
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'status_type' => ['required', 'string', 'in:active,featured'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'status_type.required' => 'Status type is required.',
            'status_type.in' => 'Status type must be either active or featured.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'status_type' => 'status type',
        ];
    }
}
