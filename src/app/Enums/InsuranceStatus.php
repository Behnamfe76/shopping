<?php

namespace Fereydooni\Shopping\App\Enums;

enum InsuranceStatus: string
{
    case ACTIVE = 'active';
    case EXPIRED = 'expired';
    case CANCELLED = 'cancelled';
    case PENDING = 'pending';
    case SUSPENDED = 'suspended';

    public function label(): string
    {
        return match ($this) {
            self::ACTIVE => 'Active',
            self::EXPIRED => 'Expired',
            self::CANCELLED => 'Cancelled',
            self::PENDING => 'Pending',
            self::SUSPENDED => 'Suspended',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::ACTIVE => 'Insurance is currently active and provides coverage',
            self::EXPIRED => 'Insurance has expired and no longer provides coverage',
            self::CANCELLED => 'Insurance has been cancelled and coverage terminated',
            self::PENDING => 'Insurance is pending approval or activation',
            self::SUSPENDED => 'Insurance is temporarily suspended',
        };
    }

    public function isActive(): bool
    {
        return $this === self::ACTIVE;
    }

    public function isExpired(): bool
    {
        return $this === self::EXPIRED;
    }

    public function isCancelled(): bool
    {
        return $this === self::CANCELLED;
    }

    public function isPending(): bool
    {
        return $this === self::PENDING;
    }

    public function isSuspended(): bool
    {
        return $this === self::SUSPENDED;
    }

    public function requiresAction(): bool
    {
        return in_array($this, [self::EXPIRED, self::PENDING, self::SUSPENDED]);
    }

    public function canBeRenewed(): bool
    {
        return in_array($this, [self::ACTIVE, self::EXPIRED]);
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function options(): array
    {
        return [
            self::ACTIVE->value => 'Active',
            self::EXPIRED->value => 'Expired',
            self::CANCELLED->value => 'Cancelled',
            self::PENDING->value => 'Pending',
            self::SUSPENDED->value => 'Suspended',
        ];
    }
}
