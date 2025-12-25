<?php

namespace Fereydooni\Shopping\app\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddOrderNoteRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $order = $this->route('order');

        return $this->user()->can('addNote', $order);
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'note' => 'required|string|max:1000',
            'type' => 'nullable|string|in:general,customer,internal,system',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'note.required' => 'Note content is required.',
            'note.max' => 'Note content must not exceed 1000 characters.',
            'type.in' => 'Invalid note type.',
        ];
    }
}
