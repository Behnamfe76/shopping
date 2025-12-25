<?php

namespace App\Http\Requests;

use App\Enums\PerformanceGrade;
use App\Enums\PeriodType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProviderPerformanceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('providerPerformance'));
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $performance = $this->route('providerPerformance');

        return [
            'provider_id' => [
                'sometimes',
                'integer',
                'exists:providers,id',
            ],
            'period_start' => [
                'sometimes',
                'date',
                'before_or_equal:period_end',
            ],
            'period_end' => [
                'sometimes',
                'date',
                'after_or_equal:period_start',
            ],
            'period_type' => [
                'sometimes',
                Rule::in(PeriodType::values()),
            ],
            'total_orders' => [
                'sometimes',
                'integer',
                'min:0',
            ],
            'total_revenue' => [
                'sometimes',
                'numeric',
                'min:0',
            ],
            'average_order_value' => [
                'sometimes',
                'numeric',
                'min:0',
            ],
            'on_time_delivery_rate' => [
                'sometimes',
                'numeric',
                'min:0',
                'max:100',
            ],
            'return_rate' => [
                'sometimes',
                'numeric',
                'min:0',
                'max:100',
            ],
            'defect_rate' => [
                'sometimes',
                'numeric',
                'min:0',
                'max:100',
            ],
            'customer_satisfaction_score' => [
                'sometimes',
                'numeric',
                'min:0',
                'max:10',
            ],
            'response_time_avg' => [
                'sometimes',
                'numeric',
                'min:0',
            ],
            'quality_rating' => [
                'sometimes',
                'numeric',
                'min:0',
                'max:10',
            ],
            'delivery_rating' => [
                'sometimes',
                'numeric',
                'min:0',
                'max:10',
            ],
            'communication_rating' => [
                'sometimes',
                'numeric',
                'min:0',
                'max:10',
            ],
            'cost_efficiency_score' => [
                'sometimes',
                'numeric',
                'min:0',
                'max:10',
            ],
            'inventory_turnover_rate' => [
                'sometimes',
                'numeric',
                'min:0',
            ],
            'lead_time_avg' => [
                'sometimes',
                'numeric',
                'min:0',
            ],
            'fill_rate' => [
                'sometimes',
                'numeric',
                'min:0',
                'max:100',
            ],
            'accuracy_rate' => [
                'sometimes',
                'numeric',
                'min:0',
                'max:100',
            ],
            'performance_score' => [
                'sometimes',
                'numeric',
                'min:0',
                'max:100',
            ],
            'performance_grade' => [
                'sometimes',
                Rule::in(PerformanceGrade::values()),
            ],
            'is_verified' => [
                'sometimes',
                'boolean',
            ],
            'verified_by' => [
                'sometimes',
                'integer',
                'exists:users,id',
            ],
            'verified_at' => [
                'sometimes',
                'date',
            ],
            'notes' => [
                'sometimes',
                'nullable',
                'string',
                'max:1000',
            ],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'provider_id.exists' => 'The selected provider does not exist.',
            'period_start.before_or_equal' => 'The period start date must be before or equal to the period end date.',
            'period_end.after_or_equal' => 'The period end date must be after or equal to the period start date.',
            'period_type.in' => 'The selected period type is invalid.',
            'total_orders.min' => 'Total orders must be at least 0.',
            'total_revenue.min' => 'Total revenue must be at least 0.',
            'average_order_value.min' => 'Average order value must be at least 0.',
            'on_time_delivery_rate.min' => 'On-time delivery rate must be at least 0.',
            'on_time_delivery_rate.max' => 'On-time delivery rate cannot exceed 100%.',
            'return_rate.min' => 'Return rate must be at least 0.',
            'return_rate.max' => 'Return rate cannot exceed 100%.',
            'defect_rate.min' => 'Defect rate must be at least 0.',
            'defect_rate.max' => 'Defect rate cannot exceed 100%.',
            'customer_satisfaction_score.min' => 'Customer satisfaction score must be at least 0.',
            'customer_satisfaction_score.max' => 'Customer satisfaction score cannot exceed 10.',
            'response_time_avg.min' => 'Response time average must be at least 0.',
            'quality_rating.min' => 'Quality rating must be at least 0.',
            'quality_rating.max' => 'Quality rating cannot exceed 10.',
            'delivery_rating.min' => 'Delivery rating must be at least 0.',
            'delivery_rating.max' => 'Delivery rating cannot exceed 10.',
            'communication_rating.min' => 'Communication rating must be at least 0.',
            'communication_rating.max' => 'Communication rating cannot exceed 10.',
            'cost_efficiency_score.min' => 'Cost efficiency score must be at least 0.',
            'cost_efficiency_score.max' => 'Cost efficiency score cannot exceed 10.',
            'inventory_turnover_rate.min' => 'Inventory turnover rate must be at least 0.',
            'lead_time_avg.min' => 'Lead time average must be at least 0.',
            'fill_rate.min' => 'Fill rate must be at least 0.',
            'fill_rate.max' => 'Fill rate cannot exceed 100%.',
            'accuracy_rate.min' => 'Accuracy rate must be at least 0.',
            'accuracy_rate.max' => 'Accuracy rate cannot exceed 100%.',
            'performance_score.min' => 'Performance score must be at least 0.',
            'performance_score.max' => 'Performance score cannot exceed 100.',
            'performance_grade.in' => 'The selected performance grade is invalid.',
            'verified_by.exists' => 'The selected verifier does not exist.',
            'notes.max' => 'Notes cannot exceed 1000 characters.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'provider_id' => 'provider',
            'period_start' => 'period start date',
            'period_end' => 'period end date',
            'period_type' => 'period type',
            'total_orders' => 'total orders',
            'total_revenue' => 'total revenue',
            'average_order_value' => 'average order value',
            'on_time_delivery_rate' => 'on-time delivery rate',
            'return_rate' => 'return rate',
            'defect_rate' => 'defect rate',
            'customer_satisfaction_score' => 'customer satisfaction score',
            'response_time_avg' => 'response time average',
            'quality_rating' => 'quality rating',
            'delivery_rating' => 'delivery rating',
            'communication_rating' => 'communication rating',
            'cost_efficiency_score' => 'cost efficiency score',
            'inventory_turnover_rate' => 'inventory turnover rate',
            'lead_time_avg' => 'lead time average',
            'fill_rate' => 'fill rate',
            'accuracy_rate' => 'accuracy rate',
            'performance_score' => 'performance score',
            'performance_grade' => 'performance grade',
            'is_verified' => 'verification status',
            'verified_by' => 'verifier',
            'verified_at' => 'verification date',
            'notes' => 'notes',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Convert empty strings to null for numeric fields
        $numericFields = [
            'total_orders', 'total_revenue', 'average_order_value',
            'on_time_delivery_rate', 'return_rate', 'defect_rate',
            'customer_satisfaction_score', 'response_time_avg',
            'quality_rating', 'delivery_rating', 'communication_rating',
            'cost_efficiency_score', 'inventory_turnover_rate',
            'lead_time_avg', 'fill_rate', 'accuracy_rate', 'performance_score',
        ];

        foreach ($numericFields as $field) {
            if ($this->has($field) && $this->get($field) === '') {
                $this->merge([$field => null]);
            }
        }

        // Handle boolean fields
        if ($this->has('is_verified')) {
            $this->merge(['is_verified' => (bool) $this->get('is_verified')]);
        }
    }
}
