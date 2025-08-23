<?php

namespace Fereydooni\Shopping\App\Enums;

enum ProviderStatus: string
{
    case ACTIVE = 'active';
    case INACTIVE = 'inactive';
    case SUSPENDED = 'suspended';
    case PENDING = 'pending';
    case BLACKLISTED = 'blacklisted';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function labels(): array
    {
        return [
            self::ACTIVE->value => 'Active',
            self::INACTIVE->value => 'Inactive',
            self::SUSPENDED->value => 'Suspended',
            self::PENDING->value => 'Pending',
            self::BLACKLISTED->value => 'Blacklisted',
        ];
    }

    public function label(): string
    {
        return self::labels()[$this->value] ?? $this->value;
    }

    public function isActive(): bool
    {
        return $this === self::ACTIVE;
    }

    public function isInactive(): bool
    {
        return $this === self::INACTIVE;
    }

    public function isSuspended(): bool
    {
        return $this === self::SUSPENDED;
    }

    public function isPending(): bool
    {
        return $this === self::PENDING;
    }

    public function isBlacklisted(): bool
    {
        return $this === self::BLACKLISTED;
    }

    public function canReceiveOrders(): bool
    {
        return in_array($this, [self::ACTIVE]);
    }

    public function canBeContacted(): bool
    {
        return in_array($this, [self::ACTIVE, self::PENDING]);
    }
}
