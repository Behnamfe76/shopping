<?php

namespace Fereydooni\Shopping\app\Enums;

enum CustomerType: string
{
    case INDIVIDUAL = 'individual';
    case BUSINESS = 'business';
    case WHOLESALE = 'wholesale';
    case VIP = 'vip';

    public function label(): string
    {
        return match($this) {
            self::INDIVIDUAL => 'Individual',
            self::BUSINESS => 'Business',
            self::WHOLESALE => 'Wholesale',
            self::VIP => 'VIP',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::INDIVIDUAL => 'blue',
            self::BUSINESS => 'purple',
            self::WHOLESALE => 'orange',
            self::VIP => 'gold',
        };
    }

    public function hasBusinessFields(): bool
    {
        return in_array($this, [self::BUSINESS, self::WHOLESALE]);
    }

    public function hasSpecialPricing(): bool
    {
        return in_array($this, [self::WHOLESALE, self::VIP]);
    }
}
