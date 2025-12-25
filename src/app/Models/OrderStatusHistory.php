<?php

namespace Fereydooni\Shopping\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderStatusHistory extends Model
{
    protected $fillable = [
        'order_id',
        'old_status',
        'new_status',
        'changed_by',
        'changed_at',
        'note',
        'reason',
        'ip_address',
        'user_agent',
        'metadata',
        'is_system_change',
        'change_type',
        'change_category',
    ];

    protected $casts = [
        'changed_at' => 'datetime',
        'metadata' => 'array',
        'is_system_change' => 'boolean',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function changedByUser(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'changed_by');
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
