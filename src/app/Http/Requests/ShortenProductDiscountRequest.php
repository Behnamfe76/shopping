<?php

namespace Fereydooni\Shopping\app\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ShortenProductDiscountRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('shorten', $this->route('discount'));
    }

    public function rules(): array
    {
        return [
            'new_end_date' => 'required|date|after:today',
        ];
    }

    public function messages(): array
    {
        return [
            'new_end_date.required' => 'New end date is required.',
            'new_end_date.date' => 'New end date must be a valid date.',
            'new_end_date.after' => 'New end date must be after today.',
        ];
    }
}
