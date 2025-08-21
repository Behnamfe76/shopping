<?php

namespace Fereydooni\Shopping\app\Enums;

enum LoyaltyTransactionStatus: string
{
    case PENDING = 'pending';
    case COMPLETED = 'completed';
    case FAILED = 'failed';
    case REVERSED = 'reversed';
    case EXPIRED = 'expired';

    public function label(): string
    {
        return match($this) {
            self::PENDING => 'Pending',
            self::COMPLETED => 'Completed',
            self::FAILED => 'Failed',
            self::REVERSED => 'Reversed',
            self::EXPIRED => 'Expired',
        };
    }

    public function description(): string
    {
        return match($this) {
            self::PENDING => 'Transaction is pending processing',
            self::COMPLETED => 'Transaction has been completed successfully',
            self::FAILED => 'Transaction has failed',
            self::REVERSED => 'Transaction has been reversed',
            self::EXPIRED => 'Transaction has expired',
        };
    }

    public function isActive(): bool
    {
        return in_array($this, [self::PENDING, self::COMPLETED]);
    }

    public function isFinal(): bool
    {
        return in_array($this, [self::COMPLETED, self::FAILED, self::REVERSED, self::EXPIRED]);
    }
}
