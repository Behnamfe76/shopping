<?php

namespace Fereydooni\Shopping\app\Http\Requests;

use Fereydooni\Shopping\app\DTOs\OrderStatusHistoryDTO;
use Illuminate\Foundation\Http\FormRequest;

class UpdateOrderStatusHistoryRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $history = $this->route('history');

        return $this->user()->can('update', $history);
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $rules = OrderStatusHistoryDTO::rules();

        // Make some fields optional for updates
        $rules['order_id'] = 'sometimes|required|integer|exists:orders,id';
        $rules['new_status'] = 'sometimes|required|string|in:'.implode(',', array_column(\Fereydooni\Shopping\app\Enums\OrderStatus::cases(), 'value'));
        $rules['changed_by'] = 'sometimes|required|integer|exists:users,id';
        $rules['changed_at'] = 'sometimes|required|date';

        return $rules;
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return OrderStatusHistoryDTO::messages();
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'order_id' => 'order',
            'old_status' => 'old status',
            'new_status' => 'new status',
            'changed_by' => 'changed by',
            'changed_at' => 'changed at',
            'note' => 'note',
            'reason' => 'reason',
            'ip_address' => 'IP address',
            'user_agent' => 'user agent',
            'metadata' => 'metadata',
            'is_system_change' => 'system change',
            'change_type' => 'change type',
            'change_category' => 'change category',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_system_change' => $this->boolean('is_system_change'),
        ]);
    }
}
