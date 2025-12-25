<?php

namespace Fereydooni\Shopping\app\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MarkOrderStatusRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $order = $this->route('order');
        $action = $this->route()->getActionMethod();

        switch ($action) {
            case 'markPaid':
                return $this->user()->can('markPaid', $order);
            case 'markShipped':
                return $this->user()->can('markShipped', $order);
            case 'markCompleted':
                return $this->user()->can('markCompleted', $order);
            default:
                return false;
        }
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $rules = [];

        // Add tracking number validation for shipping
        if ($this->route()->getActionMethod() === 'markShipped') {
            $rules['tracking_number'] = 'nullable|string|max:255';
        }

        return $rules;
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'tracking_number.max' => 'Tracking number must not exceed 255 characters.',
        ];
    }
}
