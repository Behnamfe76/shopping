<?php

namespace Fereydooni\Shopping\app\Enums;

enum CustomerNotePriority: string
{
    case LOW = 'low';
    case MEDIUM = 'medium';
    case HIGH = 'high';
    case URGENT = 'urgent';

    public function label(): string
    {
        return match ($this) {
            self::LOW => 'Low',
            self::MEDIUM => 'Medium',
            self::HIGH => 'High',
            self::URGENT => 'Urgent',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::LOW => 'gray',
            self::MEDIUM => 'yellow',
            self::HIGH => 'orange',
            self::URGENT => 'red',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::LOW => 'arrow-down',
            self::MEDIUM => 'minus',
            self::HIGH => 'arrow-up',
            self::URGENT => 'exclamation-triangle',
        };
    }

    public function numericValue(): int
    {
        return match ($this) {
            self::LOW => 1,
            self::MEDIUM => 2,
            self::HIGH => 3,
            self::URGENT => 4,
        };
    }

    public function isHigh(): bool
    {
        return in_array($this, [self::HIGH, self::URGENT]);
    }

    public function isUrgent(): bool
    {
        return $this === self::URGENT;
    }

    public function requiresImmediateAttention(): bool
    {
        return $this === self::URGENT;
    }
}
