<?php

namespace Fereydooni\Shopping\app\Enums;

enum BrandStatus: string
{
    case ACTIVE = 'active';
    case INACTIVE = 'inactive';
    case PENDING = 'pending';
    case SUSPENDED = 'suspended';

    /**
     * Get all status values.
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * Get status label.
     */
    public function label(): string
    {
        return match ($this) {
            self::ACTIVE => 'Active',
            self::INACTIVE => 'Inactive',
            self::PENDING => 'Pending',
            self::SUSPENDED => 'Suspended',
        };
    }

    /**
     * Get status color for UI.
     */
    public function color(): string
    {
        return match ($this) {
            self::ACTIVE => 'green',
            self::INACTIVE => 'gray',
            self::PENDING => 'yellow',
            self::SUSPENDED => 'red',
        };
    }

    /**
     * Check if status is active.
     */
    public function isActive(): bool
    {
        return $this === self::ACTIVE;
    }

    /**
     * Check if status allows operations.
     */
    public function allowsOperations(): bool
    {
        return in_array($this, [self::ACTIVE, self::PENDING]);
    }
}
