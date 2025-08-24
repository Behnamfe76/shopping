<?php

namespace Fereydooni\Shopping\app\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SearchCommunicationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('search', \Fereydooni\Shopping\app\Models\ProviderCommunication::class);
    }

    public function rules(): array
    {
        return [
            'query' => ['required', 'string', 'min:2', 'max:255'],
            'provider_id' => ['sometimes', 'integer', 'exists:providers,id'],
            'communication_type' => ['sometimes', 'string', Rule::in([
                'email', 'phone', 'chat', 'sms', 'video_call', 'in_person',
                'support_ticket', 'complaint', 'inquiry', 'order_update',
                'payment_notification', 'quality_issue', 'delivery_update',
                'contract_discussion', 'general'
            ])],
            'direction' => ['sometimes', 'string', Rule::in(['inbound', 'outbound'])],
            'status' => ['sometimes', 'string', Rule::in([
                'draft', 'sent', 'delivered', 'read', 'replied', 'closed', 'archived', 'failed'
            ])],
            'priority' => ['sometimes', 'string', Rule::in(['low', 'normal', 'high', 'urgent'])],
            'is_urgent' => ['sometimes', 'boolean'],
            'is_archived' => ['sometimes', 'boolean'],
            'date_from' => ['sometimes', 'date'],
            'date_to' => ['sometimes', 'date', 'after_or_equal:date_from'],
            'per_page' => ['sometimes', 'integer', 'min:1', 'max:100'],
            'sort_by' => ['sometimes', 'string', Rule::in([
                'created_at', 'updated_at', 'sent_at', 'read_at', 'replied_at',
                'priority', 'status', 'subject', 'communication_type'
            ])],
            'sort_direction' => ['sometimes', 'string', Rule::in(['asc', 'desc'])],
        ];
    }

    public function messages(): array
    {
        return [
            'query.required' => 'Search query is required.',
            'query.min' => 'Search query must be at least 2 characters.',
            'query.max' => 'Search query cannot exceed 255 characters.',
            'provider_id.exists' => 'The selected provider does not exist.',
            'communication_type.in' => 'Invalid communication type.',
            'direction.in' => 'Invalid direction.',
            'status.in' => 'Invalid status.',
            'priority.in' => 'Invalid priority.',
            'is_urgent.boolean' => 'Urgent flag must be true or false.',
            'is_archived.boolean' => 'Archived flag must be true or false.',
            'date_from.date' => 'Start date must be a valid date.',
            'date_to.date' => 'End date must be a valid date.',
            'date_to.after_or_equal' => 'End date must be after or equal to start date.',
            'per_page.integer' => 'Per page must be an integer.',
            'per_page.min' => 'Per page must be at least 1.',
            'per_page.max' => 'Per page cannot exceed 100.',
            'sort_by.in' => 'Invalid sort field.',
            'sort_direction.in' => 'Sort direction must be ascending or descending.',
        ];
    }
}
