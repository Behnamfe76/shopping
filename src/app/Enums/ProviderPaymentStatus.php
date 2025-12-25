<?php

namespace Fereydooni\Shopping\App\Enums;

enum ProviderPaymentStatus: string
{
    case PENDING = 'pending';
    case PROCESSED = 'processed';
    case COMPLETED = 'completed';
    case FAILED = 'failed';
    case CANCELLED = 'cancelled';
    case REFUNDED = 'refunded';

    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'Pending',
            self::PROCESSED => 'Processed',
            self::COMPLETED => 'Completed',
            self::FAILED => 'Failed',
            self::CANCELLED => 'Cancelled',
            self::REFUNDED => 'Refunded',
        };
    }

    public function isEditable(): bool
    {
        return in_array($this, [self::PENDING, self::PROCESSED]);
    }

    public function isProcessable(): bool
    {
        return $this === self::PENDING;
    }

    public function isCompletable(): bool
    {
        return $this === self::PROCESSED;
    }

    public function isReconcilable(): bool
    {
        return in_array($this, [self::COMPLETED, self::REFUNDED]);
    }

    public function color(): string
    {
        return match ($this) {
            self::PENDING => 'warning',
            self::PROCESSED => 'info',
            self::COMPLETED => 'success',
            self::FAILED => 'danger',
            self::CANCELLED => 'secondary',
            self::REFUNDED => 'info',
        };
    }
}
