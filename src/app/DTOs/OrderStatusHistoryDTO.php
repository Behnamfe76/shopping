<?php

namespace Fereydooni\Shopping\app\DTOs;

use Fereydooni\Shopping\app\Enums\OrderStatus;
use Illuminate\Support\Carbon;
use Spatie\LaravelData\Data;

class OrderStatusHistoryDTO extends Data
{
    public function __construct(
        public ?int $id,
        public int $order_id,
        public ?string $old_status,
        public string $new_status,
        public int $changed_by,
        public Carbon $changed_at,
        public ?string $note,
        public ?string $reason,
        public ?string $ip_address,
        public ?string $user_agent,
        public ?array $metadata,
        public bool $is_system_change,
        public ?string $change_type,
        public ?string $change_category,
        public ?Carbon $created_at = null,
        public ?Carbon $updated_at = null,
        public ?array $order = null,
        public ?array $changed_by_user = null,
    ) {}

    public static function fromModel($history): static
    {
        return new static(
            id: $history->id,
            order_id: $history->order_id,
            old_status: $history->old_status,
            new_status: $history->new_status,
            changed_by: $history->changed_by,
            changed_at: $history->changed_at,
            note: $history->note,
            reason: $history->reason,
            ip_address: $history->ip_address,
            user_agent: $history->user_agent,
            metadata: $history->metadata,
            is_system_change: $history->is_system_change,
            change_type: $history->change_type,
            change_category: $history->change_category,
            created_at: $history->created_at,
            updated_at: $history->updated_at,
            order: $history->relationLoaded('order') ? $history->order->toArray() : null,
            changed_by_user: $history->relationLoaded('changedByUser') ? $history->changedByUser->toArray() : null,
        );
    }

    public static function rules(): array
    {
        return [
            'order_id' => 'required|integer|exists:orders,id',
            'old_status' => 'nullable|string|in:'.implode(',', array_column(OrderStatus::cases(), 'value')),
            'new_status' => 'required|string|in:'.implode(',', array_column(OrderStatus::cases(), 'value')),
            'changed_by' => 'required|integer|exists:users,id',
            'changed_at' => 'required|date',
            'note' => 'nullable|string|max:1000',
            'reason' => 'nullable|string|max:255',
            'ip_address' => 'nullable|ip',
            'user_agent' => 'nullable|string|max:500',
            'metadata' => 'nullable|array',
            'is_system_change' => 'boolean',
            'change_type' => 'nullable|string|max:50',
            'change_category' => 'nullable|string|max:50',
        ];
    }

    public static function messages(): array
    {
        return [
            'order_id.required' => 'Order ID is required.',
            'order_id.exists' => 'The specified order does not exist.',
            'new_status.required' => 'New status is required.',
            'new_status.in' => 'The new status must be a valid order status.',
            'old_status.in' => 'The old status must be a valid order status.',
            'changed_by.required' => 'Changed by user ID is required.',
            'changed_by.exists' => 'The specified user does not exist.',
            'changed_at.required' => 'Change timestamp is required.',
            'changed_at.date' => 'Change timestamp must be a valid date.',
            'note.max' => 'Note cannot exceed 1000 characters.',
            'reason.max' => 'Reason cannot exceed 255 characters.',
            'ip_address.ip' => 'IP address must be a valid IP address.',
            'user_agent.max' => 'User agent cannot exceed 500 characters.',
            'metadata.array' => 'Metadata must be an array.',
            'change_type.max' => 'Change type cannot exceed 50 characters.',
            'change_category.max' => 'Change category cannot exceed 50 characters.',
        ];
    }

    /**
     * Get the status transition description
     */
    public function getStatusTransitionDescription(): string
    {
        if ($this->old_status === null) {
            return "Order status set to {$this->new_status}";
        }

        return "Order status changed from {$this->old_status} to {$this->new_status}";
    }

    /**
     * Check if this is a system-generated change
     */
    public function isSystemChange(): bool
    {
        return $this->is_system_change;
    }

    /**
     * Check if this is a user-generated change
     */
    public function isUserChange(): bool
    {
        return ! $this->is_system_change;
    }

    /**
     * Get the change type label
     */
    public function getChangeTypeLabel(): ?string
    {
        return match ($this->change_type) {
            'manual' => 'Manual Change',
            'automatic' => 'Automatic Change',
            'system' => 'System Change',
            'api' => 'API Change',
            'webhook' => 'Webhook Change',
            default => $this->change_type,
        };
    }

    /**
     * Get the change category label
     */
    public function getChangeCategoryLabel(): ?string
    {
        return match ($this->change_category) {
            'payment' => 'Payment Related',
            'shipping' => 'Shipping Related',
            'cancellation' => 'Cancellation',
            'refund' => 'Refund',
            'status_update' => 'Status Update',
            'system_maintenance' => 'System Maintenance',
            default => $this->change_category,
        };
    }

    /**
     * Get metadata value by key
     */
    public function getMetadataValue(string $key, mixed $default = null): mixed
    {
        return $this->metadata[$key] ?? $default;
    }

    /**
     * Check if metadata has a specific key
     */
    public function hasMetadataKey(string $key): bool
    {
        return isset($this->metadata[$key]);
    }

    /**
     * Get all metadata keys
     */
    public function getMetadataKeys(): array
    {
        return $this->metadata ? array_keys($this->metadata) : [];
    }
}
