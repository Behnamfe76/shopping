<?php

namespace Fereydooni\Shopping\app\Enums;

enum LoyaltyReferenceType: string
{
    case ORDER = 'order';
    case PRODUCT = 'product';
    case CAMPAIGN = 'campaign';
    case MANUAL = 'manual';
    case SYSTEM = 'system';

    public function label(): string
    {
        return match ($this) {
            self::ORDER => 'Order',
            self::PRODUCT => 'Product',
            self::CAMPAIGN => 'Campaign',
            self::MANUAL => 'Manual',
            self::SYSTEM => 'System',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::ORDER => 'Points earned from order purchase',
            self::PRODUCT => 'Points earned from specific product',
            self::CAMPAIGN => 'Points earned from marketing campaign',
            self::MANUAL => 'Manual adjustment by admin',
            self::SYSTEM => 'System-generated transaction',
        };
    }

    public function requiresReferenceId(): bool
    {
        return in_array($this, [self::ORDER, self::PRODUCT, self::CAMPAIGN]);
    }
}
