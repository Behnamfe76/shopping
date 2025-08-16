<?php

namespace Fereydooni\Shopping\app\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SearchOrderStatusHistoryRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('order-status-history.search');
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'query' => 'required|string|min:2|max:255',
            'per_page' => 'sometimes|integer|min:1|max:100',
            'order_by' => 'sometimes|string|in:changed_at,order_id,new_status,changed_by',
            'order_direction' => 'sometimes|string|in:asc,desc',
            'filters' => 'sometimes|array',
            'filters.order_id' => 'sometimes|integer|exists:orders,id',
            'filters.status' => 'sometimes|string',
            'filters.changed_by' => 'sometimes|integer|exists:users,id',
            'filters.date_from' => 'sometimes|date',
            'filters.date_to' => 'sometimes|date|after_or_equal:filters.date_from',
            'filters.is_system_change' => 'sometimes|boolean',
            'filters.change_type' => 'sometimes|string',
            'filters.change_category' => 'sometimes|string',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'query.required' => 'Search query is required.',
            'query.min' => 'Search query must be at least 2 characters.',
            'query.max' => 'Search query cannot exceed 255 characters.',
            'per_page.integer' => 'Per page must be an integer.',
            'per_page.min' => 'Per page must be at least 1.',
            'per_page.max' => 'Per page cannot exceed 100.',
            'order_by.in' => 'Invalid order by field.',
            'order_direction.in' => 'Order direction must be asc or desc.',
            'filters.array' => 'Filters must be an array.',
            'filters.order_id.integer' => 'Order ID filter must be an integer.',
            'filters.order_id.exists' => 'The specified order does not exist.',
            'filters.changed_by.integer' => 'Changed by filter must be an integer.',
            'filters.changed_by.exists' => 'The specified user does not exist.',
            'filters.date_from.date' => 'Date from must be a valid date.',
            'filters.date_to.date' => 'Date to must be a valid date.',
            'filters.date_to.after_or_equal' => 'Date to must be after or equal to date from.',
            'filters.is_system_change.boolean' => 'System change filter must be a boolean.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'query' => 'search query',
            'per_page' => 'per page',
            'order_by' => 'order by',
            'order_direction' => 'order direction',
            'filters' => 'filters',
            'filters.order_id' => 'order ID filter',
            'filters.status' => 'status filter',
            'filters.changed_by' => 'changed by filter',
            'filters.date_from' => 'date from filter',
            'filters.date_to' => 'date to filter',
            'filters.is_system_change' => 'system change filter',
            'filters.change_type' => 'change type filter',
            'filters.change_category' => 'change category filter',
        ];
    }
}
