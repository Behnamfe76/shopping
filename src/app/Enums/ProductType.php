<?php

namespace Fereydooni\Shopping\app\Enums;

enum ProductType: int
{
    case PHYSICAL = 1;
    case DIGITAL = 2;
    case SUBSCRIPTION = 3;

    public function toString(): string
    {
        return match ($this) {
            self::PHYSICAL => 'physical',
            self::DIGITAL => 'digital',
            self::SUBSCRIPTION => 'subscription',
        };
    }

    public static function fromString(string $value): self
    {
        return match ($value) {
            'physical' => self::PHYSICAL,
            'digital' => self::DIGITAL,
            'subscription' => self::SUBSCRIPTION,
            default => throw new \InvalidArgumentException("Invalid product type: $value"),
        };
    }

    public function label(): string
    {
        return match ($this) {
            self::PHYSICAL => 'Physical',
            self::DIGITAL => 'Digital',
            self::SUBSCRIPTION => 'Subscription',
        };
    }
}
