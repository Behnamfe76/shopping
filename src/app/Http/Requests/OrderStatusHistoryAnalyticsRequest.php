<?php

namespace Fereydooni\Shopping\app\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OrderStatusHistoryAnalyticsRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('order-status-history.analytics.view');
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'start_date' => 'required|date|before_or_equal:end_date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'group_by' => 'sometimes|string|in:day,week,month,quarter,year',
            'filters' => 'sometimes|array',
            'filters.order_id' => 'sometimes|integer|exists:orders,id',
            'filters.status' => 'sometimes|string',
            'filters.changed_by' => 'sometimes|integer|exists:users,id',
            'filters.is_system_change' => 'sometimes|boolean',
            'filters.change_type' => 'sometimes|string',
            'filters.change_category' => 'sometimes|string',
            'include_breakdown' => 'sometimes|boolean',
            'include_trends' => 'sometimes|boolean',
            'include_comparison' => 'sometimes|boolean',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'start_date.required' => 'Start date is required.',
            'start_date.date' => 'Start date must be a valid date.',
            'start_date.before_or_equal' => 'Start date must be before or equal to end date.',
            'end_date.required' => 'End date is required.',
            'end_date.date' => 'End date must be a valid date.',
            'end_date.after_or_equal' => 'End date must be after or equal to start date.',
            'group_by.in' => 'Group by must be one of: day, week, month, quarter, year.',
            'filters.array' => 'Filters must be an array.',
            'filters.order_id.integer' => 'Order ID filter must be an integer.',
            'filters.order_id.exists' => 'The specified order does not exist.',
            'filters.changed_by.integer' => 'Changed by filter must be an integer.',
            'filters.changed_by.exists' => 'The specified user does not exist.',
            'filters.is_system_change.boolean' => 'System change filter must be a boolean.',
            'include_breakdown.boolean' => 'Include breakdown must be a boolean.',
            'include_trends.boolean' => 'Include trends must be a boolean.',
            'include_comparison.boolean' => 'Include comparison must be a boolean.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'start_date' => 'start date',
            'end_date' => 'end date',
            'group_by' => 'group by',
            'filters' => 'filters',
            'filters.order_id' => 'order ID filter',
            'filters.status' => 'status filter',
            'filters.changed_by' => 'changed by filter',
            'filters.is_system_change' => 'system change filter',
            'filters.change_type' => 'change type filter',
            'filters.change_category' => 'change category filter',
            'include_breakdown' => 'include breakdown',
            'include_trends' => 'include trends',
            'include_comparison' => 'include comparison',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'start_date' => $this->start_date ?? now()->subDays(30)->toDateString(),
            'end_date' => $this->end_date ?? now()->toDateString(),
            'group_by' => $this->group_by ?? 'day',
            'include_breakdown' => $this->boolean('include_breakdown', true),
            'include_trends' => $this->boolean('include_trends', true),
            'include_comparison' => $this->boolean('include_comparison', false),
        ]);
    }
}
