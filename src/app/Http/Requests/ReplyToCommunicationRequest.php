<?php

namespace Fereydooni\Shopping\app\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ReplyToCommunicationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('reply', $this->route('providerCommunication'));
    }

    public function rules(): array
    {
        return [
            'message' => ['required', 'string', 'max:10000'],
            'priority' => ['sometimes', 'string', Rule::in(['low', 'normal', 'high', 'urgent'])],
            'is_urgent' => ['sometimes', 'boolean'],
            'attachments' => ['sometimes', 'array'],
            'attachments.*' => ['string', 'max:500'],
            'tags' => ['sometimes', 'array'],
            'tags.*' => ['string', 'max:100'],
        ];
    }

    public function messages(): array
    {
        return [
            'message.required' => 'Reply message is required.',
            'message.max' => 'Reply message cannot exceed 10,000 characters.',
            'priority.in' => 'Invalid priority.',
            'is_urgent.boolean' => 'Urgent flag must be true or false.',
            'attachments.array' => 'Attachments must be an array.',
            'attachments.*.max' => 'Attachment path cannot exceed 500 characters.',
            'tags.array' => 'Tags must be an array.',
            'tags.*.max' => 'Tag cannot exceed 100 characters.',
        ];
    }
}
