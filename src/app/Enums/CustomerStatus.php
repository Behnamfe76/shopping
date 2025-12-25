<?php

namespace Fereydooni\Shopping\app\Enums;

enum CustomerStatus: string
{
    case ACTIVE = 'active';
    case INACTIVE = 'inactive';
    case SUSPENDED = 'suspended';
    case PENDING = 'pending';

    public function label(): string
    {
        return match ($this) {
            self::ACTIVE => 'Active',
            self::INACTIVE => 'Inactive',
            self::SUSPENDED => 'Suspended',
            self::PENDING => 'Pending',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::ACTIVE => 'green',
            self::INACTIVE => 'gray',
            self::SUSPENDED => 'red',
            self::PENDING => 'yellow',
        };
    }

    public function isActive(): bool
    {
        return $this === self::ACTIVE;
    }

    public function canOrder(): bool
    {
        return in_array($this, [self::ACTIVE, self::PENDING]);
    }
}
