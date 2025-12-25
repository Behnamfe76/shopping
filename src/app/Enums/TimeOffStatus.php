<?php

namespace Fereydooni\Shopping\app\Enums;

enum TimeOffStatus: string
{
    case PENDING = 'pending';
    case APPROVED = 'approved';
    case REJECTED = 'rejected';
    case CANCELLED = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'Pending',
            self::APPROVED => 'Approved',
            self::REJECTED => 'Rejected',
            self::CANCELLED => 'Cancelled',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::PENDING => 'Time-off request is pending approval',
            self::APPROVED => 'Time-off request has been approved',
            self::REJECTED => 'Time-off request has been rejected',
            self::CANCELLED => 'Time-off request has been cancelled',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::PENDING => 'yellow',
            self::APPROVED => 'green',
            self::REJECTED => 'red',
            self::CANCELLED => 'gray',
        };
    }

    public function isActive(): bool
    {
        return match ($this) {
            self::PENDING, self::APPROVED => true,
            self::REJECTED, self::CANCELLED => false,
        };
    }

    public function canBeModified(): bool
    {
        return match ($this) {
            self::PENDING => true,
            self::APPROVED, self::REJECTED, self::CANCELLED => false,
        };
    }

    public function requiresAction(): bool
    {
        return match ($this) {
            self::PENDING => true,
            self::APPROVED, self::REJECTED, self::CANCELLED => false,
        };
    }
}
