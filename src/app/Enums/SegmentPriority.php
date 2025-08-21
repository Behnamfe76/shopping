<?php

namespace Fereydooni\Shopping\app\Enums;

enum SegmentPriority: string
{
    case LOW = 'low';
    case NORMAL = 'normal';
    case HIGH = 'high';
    case CRITICAL = 'critical';

    public function label(): string
    {
        return match($this) {
            self::LOW => 'Low',
            self::NORMAL => 'Normal',
            self::HIGH => 'High',
            self::CRITICAL => 'Critical',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::LOW => 'gray',
            self::NORMAL => 'blue',
            self::HIGH => 'orange',
            self::CRITICAL => 'red',
        };
    }

    public function description(): string
    {
        return match($this) {
            self::LOW => 'Low priority segment with minimal impact',
            self::NORMAL => 'Standard priority segment',
            self::HIGH => 'High priority segment requiring attention',
            self::CRITICAL => 'Critical priority segment with immediate impact',
        };
    }

    public function value(): int
    {
        return match($this) {
            self::LOW => 1,
            self::NORMAL => 2,
            self::HIGH => 3,
            self::CRITICAL => 4,
        };
    }

    public function isLow(): bool
    {
        return $this === self::LOW;
    }

    public function isNormal(): bool
    {
        return $this === self::NORMAL;
    }

    public function isHigh(): bool
    {
        return $this === self::HIGH;
    }

    public function isCritical(): bool
    {
        return $this === self::CRITICAL;
    }

    public function isUrgent(): bool
    {
        return in_array($this, [self::HIGH, self::CRITICAL]);
    }

    public function requiresImmediateAttention(): bool
    {
        return $this === self::CRITICAL;
    }
}
