<?php

namespace Fereydooni\Shopping\app\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Fereydooni\Shopping\app\DTOs\OrderStatusHistoryDTO;

class StoreOrderStatusHistoryRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('order-status-history.create');
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return OrderStatusHistoryDTO::rules();
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
            'changed_at' => $this->changed_at ?? now(),
            'is_system_change' => $this->boolean('is_system_change', false),
            'metadata' => $this->metadata ?? [],
        ]);
    }
}
