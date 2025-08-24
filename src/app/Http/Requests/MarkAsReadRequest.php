<?php

namespace Fereydooni\Shopping\app\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MarkAsReadRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('read', $this->route('providerCommunication'));
    }

    public function rules(): array
    {
        return [
            'read_at' => ['sometimes', 'date'],
        ];
    }

    public function messages(): array
    {
        return [
            'read_at.date' => 'Read timestamp must be a valid date.',
        ];
    }
}
