<?php

namespace Fereydooni\Shopping\app\Enums;

enum WishlistPriority: string
{
    case LOW = 'low';
    case MEDIUM = 'medium';
    case HIGH = 'high';
    case URGENT = 'urgent';

    /**
     * Get all priority values
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * Get priority label
     */
    public function label(): string
    {
        return match ($this) {
            self::LOW => 'Low',
            self::MEDIUM => 'Medium',
            self::HIGH => 'High',
            self::URGENT => 'Urgent',
        };
    }

    /**
     * Get priority color for UI
     */
    public function color(): string
    {
        return match ($this) {
            self::LOW => 'gray',
            self::MEDIUM => 'blue',
            self::HIGH => 'orange',
            self::URGENT => 'red',
        };
    }

    /**
     * Get priority level for sorting
     */
    public function level(): int
    {
        return match ($this) {
            self::LOW => 1,
            self::MEDIUM => 2,
            self::HIGH => 3,
            self::URGENT => 4,
        };
    }

    /**
     * Check if priority is high or urgent
     */
    public function isHighPriority(): bool
    {
        return in_array($this, [self::HIGH, self::URGENT]);
    }

    /**
     * Get next priority level
     */
    public function next(): ?self
    {
        return match ($this) {
            self::LOW => self::MEDIUM,
            self::MEDIUM => self::HIGH,
            self::HIGH => self::URGENT,
            self::URGENT => null,
        };
    }

    /**
     * Get previous priority level
     */
    public function previous(): ?self
    {
        return match ($this) {
            self::LOW => null,
            self::MEDIUM => self::LOW,
            self::HIGH => self::MEDIUM,
            self::URGENT => self::HIGH,
        };
    }
}
