<?php

namespace Fereydooni\Shopping\app\Enums;

enum LoyaltyTransactionType: string
{
    case EARNED = 'earned';
    case REDEEMED = 'redeemed';
    case EXPIRED = 'expired';
    case REVERSED = 'reversed';
    case BONUS = 'bonus';
    case ADJUSTMENT = 'adjustment';

    public function label(): string
    {
        return match($this) {
            self::EARNED => 'Earned',
            self::REDEEMED => 'Redeemed',
            self::EXPIRED => 'Expired',
            self::REVERSED => 'Reversed',
            self::BONUS => 'Bonus',
            self::ADJUSTMENT => 'Adjustment',
        };
    }

    public function description(): string
    {
        return match($this) {
            self::EARNED => 'Points earned from purchases or activities',
            self::REDEEMED => 'Points redeemed for rewards or discounts',
            self::EXPIRED => 'Points that have expired',
            self::REVERSED => 'Transaction that has been reversed',
            self::BONUS => 'Bonus points from promotions or campaigns',
            self::ADJUSTMENT => 'Manual adjustment of points balance',
        };
    }

    public function isPositive(): bool
    {
        return in_array($this, [self::EARNED, self::BONUS, self::ADJUSTMENT]);
    }

    public function isNegative(): bool
    {
        return in_array($this, [self::REDEEMED, self::EXPIRED, self::REVERSED]);
    }
}
