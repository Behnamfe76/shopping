<?php

namespace Fereydooni\Shopping\app\Http\Requests;

use Fereydooni\Shopping\app\Models\ProductDiscount;
use Illuminate\Foundation\Http\FormRequest;

class SearchProductDiscountRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('search', ProductDiscount::class);
    }

    public function rules(): array
    {
        return [
            'query' => 'required|string|min:2|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'query.required' => 'Search query is required.',
            'query.string' => 'Search query must be a string.',
            'query.min' => 'Search query must be at least 2 characters.',
            'query.max' => 'Search query cannot exceed 255 characters.',
        ];
    }
}
