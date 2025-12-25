<?php

namespace Fereydooni\Shopping\app\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SendCommunicationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('send', \Fereydooni\Shopping\app\Models\ProviderCommunication::class);
    }

    public function rules(): array
    {
        return [
            'provider_id' => ['required', 'integer', 'exists:providers,id'],
            'communication_type' => ['required', 'string', Rule::in([
                'email', 'phone', 'chat', 'sms', 'video_call', 'in_person',
                'support_ticket', 'complaint', 'inquiry', 'order_update',
                'payment_notification', 'quality_issue', 'delivery_update',
                'contract_discussion', 'general',
            ])],
            'subject' => ['required', 'string', 'max:255'],
            'message' => ['required', 'string', 'max:10000'],
            'priority' => ['sometimes', 'string', Rule::in(['low', 'normal', 'high', 'urgent'])],
            'is_urgent' => ['sometimes', 'boolean'],
            'attachments' => ['sometimes', 'array'],
            'attachments.*' => ['string', 'max:500'],
            'tags' => ['sometimes', 'array'],
            'tags.*' => ['string', 'max:100'],
            'thread_id' => ['sometimes', 'string', 'max:255'],
            'parent_id' => ['sometimes', 'integer', 'exists:provider_communications,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'provider_id.required' => 'Provider ID is required.',
            'provider_id.exists' => 'The selected provider does not exist.',
            'communication_type.required' => 'Communication type is required.',
            'communication_type.in' => 'Invalid communication type.',
            'subject.required' => 'Subject is required.',
            'subject.max' => 'Subject cannot exceed 255 characters.',
            'message.required' => 'Message is required.',
            'message.max' => 'Message cannot exceed 10,000 characters.',
            'priority.in' => 'Invalid priority.',
            'is_urgent.boolean' => 'Urgent flag must be true or false.',
            'attachments.array' => 'Attachments must be an array.',
            'attachments.*.max' => 'Attachment path cannot exceed 500 characters.',
            'tags.array' => 'Tags must be an array.',
            'tags.*.max' => 'Tag cannot exceed 100 characters.',
            'thread_id.max' => 'Thread ID cannot exceed 255 characters.',
            'parent_id.exists' => 'The selected parent communication does not exist.',
        ];
    }
}
