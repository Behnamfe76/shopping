<?php

namespace Fereydooni\Shopping\app\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CalculateProductDiscountRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('calculate', $this->route('discount'));
    }

    public function rules(): array
    {
        return [
            'original_price' => 'required|numeric|min:0',
            'quantity' => 'nullable|numeric|min:1',
        ];
    }

    public function messages(): array
    {
        return [
            'original_price.required' => 'Original price is required.',
            'original_price.numeric' => 'Original price must be a number.',
            'original_price.min' => 'Original price must be at least 0.',
            'quantity.numeric' => 'Quantity must be a number.',
            'quantity.min' => 'Quantity must be at least 1.',
        ];
    }
}
