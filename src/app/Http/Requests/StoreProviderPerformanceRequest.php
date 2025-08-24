<?php

namespace App\Http\Requests;

use App\Enums\PerformanceGrade;
use App\Enums\PeriodType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreProviderPerformanceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('create', \App\Models\ProviderPerformance::class);
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'provider_id' => [
                'required',
                'integer',
                'exists:providers,id'
            ],
            'period_start' => [
                'required',
                'date',
                'before_or_equal:period_end'
            ],
            'period_end' => [
                'required',
                'date',
                'after_or_equal:period_start'
            ],
            'period_type' => [
                'required',
                'string',
                Rule::in(PeriodType::values())
            ],
            'total_orders' => [
                'required',
                'integer',
                'min:0'
            ],
            'total_revenue' => [
                'required',
                'numeric',
                'min:0',
                'decimal:0,2'
            ],
            'average_order_value' => [
                'required',
                'numeric',
                'min:0',
                'decimal:0,2'
            ],
            'on_time_delivery_rate' => [
                'required',
                'numeric',
                'between:0,100',
                'decimal:0,2'
            ],
            'return_rate' => [
                'required',
                'numeric',
                'between:0,100',
                'decimal:0,2'
            ],
            'defect_rate' => [
                'required',
                'numeric',
                'between:0,100',
                'decimal:0,2'
            ],
            'customer_satisfaction_score' => [
                'required',
                'numeric',
                'between:1,10',
                'decimal:0,2'
            ],
            'response_time_avg' => [
                'required',
                'numeric',
                'min:0',
                'decimal:0,2'
            ],
            'quality_rating' => [
                'required',
                'numeric',
                'between:1,10',
                'decimal:0,2'
            ],
            'delivery_rating' => [
                'required',
                'numeric',
                'between:1,10',
                'decimal:0,2'
            ],
            'communication_rating' => [
                'required',
                'numeric',
                'between:1,10',
                'decimal:0,2'
            ],
            'cost_efficiency_score' => [
                'required',
                'numeric',
                'between:0,100',
                'decimal:0,2'
            ],
            'inventory_turnover_rate' => [
                'required',
                'numeric',
                'min:0',
                'decimal:0,2'
            ],
            'lead_time_avg' => [
                'required',
                'numeric',
                'min:0',
                'decimal:0,2'
            ],
            'fill_rate' => [
                'required',
                'numeric',
                'between:0,100',
                'decimal:0,2'
            ],
            'accuracy_rate' => [
                'required',
                'numeric',
                'between:0,100',
                'decimal:0,2'
            ],
            'performance_score' => [
                'nullable',
                'numeric',
                'between:0,100',
                'decimal:0,2'
            ],
            'performance_grade' => [
                'nullable',
                'string',
                Rule::in(PerformanceGrade::values())
            ],
            'is_verified' => [
                'boolean'
            ],
            'verified_by' => [
                'nullable',
                'integer',
                'exists:users,id'
            ],
            'verified_at' => [
                'nullable',
                'date'
            ],
            'notes' => [
                'nullable',
                'string',
                'max:1000'
            ],
        ];
    }

    /**
     * Get custom validation messages for the request.
     */
    public function messages(): array
    {
        return [
            'provider_id.required' => 'Provider ID is required.',
            'provider_id.integer' => 'Provider ID must be an integer.',
            'provider_id.exists' => 'Selected provider does not exist.',

            'period_start.required' => 'Period start date is required.',
            'period_start.date' => 'Period start must be a valid date.',
            'period_start.before_or_equal' => 'Period start must be before or equal to period end.',

            'period_end.required' => 'Period end date is required.',
            'period_end.date' => 'Period end must be a valid date.',
            'period_end.after_or_equal' => 'Period end must be after or equal to period start.',

            'period_type.required' => 'Period type is required.',
            'period_type.string' => 'Period type must be a string.',
            'period_type.in' => 'Invalid period type selected.',

            'total_orders.required' => 'Total orders is required.',
            'total_orders.integer' => 'Total orders must be an integer.',
            'total_orders.min' => 'Total orders cannot be negative.',

            'total_revenue.required' => 'Total revenue is required.',
            'total_revenue.numeric' => 'Total revenue must be a number.',
            'total_revenue.min' => 'Total revenue cannot be negative.',
            'total_revenue.decimal' => 'Total revenue can have up to 2 decimal places.',

            'average_order_value.required' => 'Average order value is required.',
            'average_order_value.numeric' => 'Average order value must be a number.',
            'average_order_value.min' => 'Average order value cannot be negative.',
            'average_order_value.decimal' => 'Average order value can have up to 2 decimal places.',

            'on_time_delivery_rate.required' => 'On-time delivery rate is required.',
            'on_time_delivery_rate.numeric' => 'On-time delivery rate must be a number.',
            'on_time_delivery_rate.between' => 'On-time delivery rate must be between 0 and 100.',
            'on_time_delivery_rate.decimal' => 'On-time delivery rate can have up to 2 decimal places.',

            'return_rate.required' => 'Return rate is required.',
            'return_rate.numeric' => 'Return rate must be a number.',
            'return_rate.between' => 'Return rate must be between 0 and 100.',
            'return_rate.decimal' => 'Return rate can have up to 2 decimal places.',

            'defect_rate.required' => 'Defect rate is required.',
            'defect_rate.numeric' => 'Defect rate must be a number.',
            'defect_rate.between' => 'Defect rate must be between 0 and 100.',
            'defect_rate.decimal' => 'Defect rate can have up to 2 decimal places.',

            'customer_satisfaction_score.required' => 'Customer satisfaction score is required.',
            'customer_satisfaction_score.numeric' => 'Customer satisfaction score must be a number.',
            'customer_satisfaction_score.between' => 'Customer satisfaction score must be between 1 and 10.',
            'customer_satisfaction_score.decimal' => 'Customer satisfaction score can have up to 2 decimal places.',

            'response_time_avg.required' => 'Average response time is required.',
            'response_time_avg.numeric' => 'Average response time must be a number.',
            'response_time_avg.min' => 'Average response time cannot be negative.',
            'response_time_avg.decimal' => 'Average response time can have up to 2 decimal places.',

            'quality_rating.required' => 'Quality rating is required.',
            'quality_rating.numeric' => 'Quality rating must be a number.',
            'quality_rating.between' => 'Quality rating must be between 1 and 10.',
            'quality_rating.decimal' => 'Quality rating can have up to 2 decimal places.',

            'delivery_rating.required' => 'Delivery rating is required.',
            'delivery_rating.numeric' => 'Delivery rating must be a number.',
            'delivery_rating.between' => 'Delivery rating must be between 1 and 10.',
            'delivery_rating.decimal' => 'Delivery rating can have up to 2 decimal places.',

            'communication_rating.required' => 'Communication rating is required.',
            'communication_rating.numeric' => 'Communication rating must be a number.',
            'communication_rating.between' => 'Communication rating must be between 1 and 10.',
            'communication_rating.decimal' => 'Communication rating can have up to 2 decimal places.',

            'cost_efficiency_score.required' => 'Cost efficiency score is required.',
            'cost_efficiency_score.numeric' => 'Cost efficiency score must be a number.',
            'cost_efficiency_score.between' => 'Cost efficiency score must be between 0 and 100.',
            'cost_efficiency_score.decimal' => 'Cost efficiency score can have up to 2 decimal places.',

            'inventory_turnover_rate.required' => 'Inventory turnover rate is required.',
            'inventory_turnover_rate.numeric' => 'Inventory turnover rate must be a number.',
            'inventory_turnover_rate.min' => 'Inventory turnover rate cannot be negative.',
            'inventory_turnover_rate.decimal' => 'Inventory turnover rate can have up to 2 decimal places.',

            'lead_time_avg.required' => 'Average lead time is required.',
            'lead_time_avg.numeric' => 'Average lead time must be a number.',
            'lead_time_avg.min' => 'Average lead time cannot be negative.',
            'lead_time_avg.decimal' => 'Average lead time can have up to 2 decimal places.',

            'fill_rate.required' => 'Fill rate is required.',
            'fill_rate.numeric' => 'Fill rate must be a number.',
            'fill_rate.between' => 'Fill rate must be between 0 and 100.',
            'fill_rate.decimal' => 'Fill rate can have up to 2 decimal places.',

            'accuracy_rate.required' => 'Accuracy rate is required.',
            'accuracy_rate.numeric' => 'Accuracy rate must be a number.',
            'accuracy_rate.between' => 'Accuracy rate must be between 0 and 100.',
            'accuracy_rate.decimal' => 'Accuracy rate can have up to 2 decimal places.',

            'performance_score.numeric' => 'Performance score must be a number.',
            'performance_score.between' => 'Performance score must be between 0 and 100.',
            'performance_score.decimal' => 'Performance score can have up to 2 decimal places.',

            'performance_grade.string' => 'Performance grade must be a string.',
            'performance_grade.in' => 'Invalid performance grade selected.',

            'is_verified.boolean' => 'Verification status must be true or false.',

            'verified_by.integer' => 'Verifier ID must be an integer.',
            'verified_by.exists' => 'Selected verifier does not exist.',

            'verified_at.date' => 'Verification date must be a valid date.',

            'notes.string' => 'Notes must be a string.',
            'notes.max' => 'Notes cannot exceed 1000 characters.',
        ];
    }

    /**
     * Get custom validation attributes for the request.
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
            'response_time_avg' => 'average response time',
            'quality_rating' => 'quality rating',
            'delivery_rating' => 'delivery rating',
            'communication_rating' => 'communication rating',
            'cost_efficiency_score' => 'cost efficiency score',
            'inventory_turnover_rate' => 'inventory turnover rate',
            'lead_time_avg' => 'average lead time',
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
        // Convert empty strings to null for optional fields
        $this->merge([
            'performance_score' => $this->performance_score ?: null,
            'performance_grade' => $this->performance_grade ?: null,
            'verified_by' => $this->verified_by ?: null,
            'verified_at' => $this->verified_at ?: null,
            'notes' => $this->notes ?: null,
        ]);
    }

    /**
     * Handle a failed validation attempt.
     */
    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator): void
    {
        // You can customize the response here if needed
        parent::failedValidation($validator);
    }

    /**
     * Handle a failed authorization attempt.
     */
    protected function failedAuthorization(): void
    {
        abort(403, 'You are not authorized to create provider performance records.');
    }
}
