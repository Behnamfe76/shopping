<?php

namespace Fereydooni\Shopping\app\Enums;

enum ProductType: string
{
    case PHYSICAL = 'physical';
    case DIGITAL = 'digital';
    case SUBSCRIPTION = 'subscription';

    public function label(): string
    {
        return match($this) {
            self::PHYSICAL => 'Physical',
            self::DIGITAL => 'Digital',
            self::SUBSCRIPTION => 'Subscription',
        };
    }
}
