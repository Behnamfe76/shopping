<?php

namespace Fereydooni\Shopping\app\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ToggleProductTagStatusRequest extends FormRequest
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
        return [
            'status' => ['sometimes', 'string', 'in:active,inactive,featured,unfeatured'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'status.in' => 'Status must be active, inactive, featured, or unfeatured.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'status' => 'status',
        ];
    }
}
