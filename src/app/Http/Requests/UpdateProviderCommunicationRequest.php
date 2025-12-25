<?php

namespace Fereydooni\Shopping\app\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProviderCommunicationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('providerCommunication'));
    }

    public function rules(): array
    {
        return [
            'provider_id' => ['sometimes', 'integer', 'exists:providers,id'],
            'user_id' => ['sometimes', 'integer', 'exists:users,id'],
            'communication_type' => ['sometimes', 'string', Rule::in([
                'email', 'phone', 'chat', 'sms', 'video_call', 'in_person',
                'support_ticket', 'complaint', 'inquiry', 'order_update',
                'payment_notification', 'quality_issue', 'delivery_update',
                'contract_discussion', 'general',
            ])],
            'subject' => ['sometimes', 'string', 'max:255'],
            'message' => ['sometimes', 'string', 'max:10000'],
            'direction' => ['sometimes', 'string', Rule::in(['inbound', 'outbound'])],
            'status' => ['sometimes', 'string', Rule::in([
                'draft', 'sent', 'delivered', 'read', 'replied', 'closed', 'archived', 'failed',
            ])],
            'priority' => ['sometimes', 'string', Rule::in(['low', 'normal', 'high', 'urgent'])],
            'is_urgent' => ['sometimes', 'boolean'],
            'is_archived' => ['sometimes', 'boolean'],
            'attachments' => ['sometimes', 'array'],
            'attachments.*' => ['string', 'max:500'],
            'tags' => ['sometimes', 'array'],
            'tags.*' => ['string', 'max:100'],
            'thread_id' => ['sometimes', 'string', 'max:255'],
            'parent_id' => ['sometimes', 'integer', 'exists:provider_communications,id'],
            'notes' => ['sometimes', 'string', 'max:1000'],
            'satisfaction_rating' => ['sometimes', 'numeric', 'min:1', 'max:5'],
        ];
    }

    public function messages(): array
    {
        return [
            'provider_id.exists' => 'The selected provider does not exist.',
            'user_id.exists' => 'The selected user does not exist.',
            'communication_type.in' => 'Invalid communication type.',
            'subject.max' => 'Subject cannot exceed 255 characters.',
            'message.max' => 'Message cannot exceed 10,000 characters.',
            'direction.in' => 'Invalid direction.',
            'status.in' => 'Invalid status.',
            'priority.in' => 'Invalid priority.',
            'is_urgent.boolean' => 'Urgent flag must be true or false.',
            'is_archived.boolean' => 'Archived flag must be true or false.',
            'attachments.array' => 'Attachments must be an array.',
            'attachments.*.max' => 'Attachment path cannot exceed 500 characters.',
            'tags.array' => 'Tags must be an array.',
            'tags.*.max' => 'Tag cannot exceed 100 characters.',
            'thread_id.max' => 'Thread ID cannot exceed 255 characters.',
            'parent_id.exists' => 'The selected parent communication does not exist.',
            'notes.max' => 'Notes cannot exceed 1,000 characters.',
            'satisfaction_rating.numeric' => 'Satisfaction rating must be a number.',
            'satisfaction_rating.min' => 'Satisfaction rating must be at least 1.',
            'satisfaction_rating.max' => 'Satisfaction rating cannot exceed 5.',
        ];
    }
}
