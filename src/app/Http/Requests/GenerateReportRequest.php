<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GenerateReportRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('generateReport', $this->route('providerPerformance'));
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'report_type' => [
                'required',
                'string',
                'in:summary,detailed,trend,comparison,analytics,export'
            ],
            'provider_id' => [
                'sometimes',
                'integer',
                'exists:providers,id'
            ],
            'period_start' => [
                'sometimes',
                'date'
            ],
            'period_end' => [
                'sometimes',
                'date',
                'after_or_equal:period_start'
            ],
            'format' => [
                'sometimes',
                'string',
                'in:json,pdf,csv,excel'
            ],
            'include_charts' => [
                'sometimes',
                'boolean'
            ],
            'include_benchmarks' => [
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
            'report_type.required' => 'Report type is required.',
            'report_type.in' => 'Invalid report type selected.',
            'provider_id.exists' => 'The selected provider does not exist.',
            'period_end.after_or_equal' => 'Period end date must be after or equal to period start date.',
            'format.in' => 'Invalid format selected.',
            'include_charts.boolean' => 'Include charts must be true or false.',
            'include_benchmarks.boolean' => 'Include benchmarks must be true or false.'
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'report_type' => 'report type',
            'provider_id' => 'provider',
            'period_start' => 'period start date',
            'period_end' => 'period end date',
            'format' => 'format',
            'include_charts' => 'include charts',
            'include_benchmarks' => 'include benchmarks'
        ];
    }
}
