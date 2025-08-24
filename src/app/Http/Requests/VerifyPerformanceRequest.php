<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VerifyPerformanceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('verify', $this->route('providerPerformance'));
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'notes' => [
                'nullable',
                'string',
                'max:1000'
            ],
            'verification_date' => [
                'nullable',
                'date',
                'before_or_equal:today'
            ]
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'notes.max' => 'Verification notes cannot exceed 1000 characters.',
            'verification_date.before_or_equal' => 'Verification date cannot be in the future.'
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'notes' => 'verification notes',
            'verification_date' => 'verification date'
        ];
    }
}
