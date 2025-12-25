<?php

namespace Fereydooni\Shopping\app\Enums;

enum TransactionStatus: string
{
    case INITIATED = 'initiated';
    case SUCCESS = 'success';
    case FAILED = 'failed';
    case REFUNDED = 'refunded';

    public function label(): string
    {
        return match ($this) {
            self::INITIATED => 'Initiated',
            self::SUCCESS => 'Success',
            self::FAILED => 'Failed',
            self::REFUNDED => 'Refunded',
        };
    }
}
