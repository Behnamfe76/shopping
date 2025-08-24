<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CalculatePerformanceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('calculate', $this->route('providerPerformance'));
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'recalculate_metrics' => [
                'sometimes',
                'boolean'
            ],
            'update_grade' => [
                'sometimes',
                'boolean'
            ],
            'force_recalculation' => [
                'sometimes',
                'boolean'
            ]
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'recalculate_metrics.boolean' => 'Recalculate metrics must be true or false.',
            'update_grade.boolean' => 'Update grade must be true or false.',
            'force_recalculation.boolean' => 'Force recalculation must be true or false.'
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'recalculate_metrics' => 'recalculate metrics',
            'update_grade' => 'update grade',
            'force_recalculation' => 'force recalculation'
        ];
    }
}
