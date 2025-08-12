<?php

namespace Fereydooni\Shopping\app\Enums;

enum AddressType: string
{
    case SHIPPING = 'shipping';
    case BILLING = 'billing';

    public function label(): string
    {
        return match($this) {
            self::SHIPPING => 'Shipping',
            self::BILLING => 'Billing',
        };
    }
}
