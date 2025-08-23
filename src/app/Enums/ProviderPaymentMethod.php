<?php

namespace Fereydooni\Shopping\App\Enums;

enum ProviderPaymentMethod: string
{
    case BANK_TRANSFER = 'bank_transfer';
    case CHECK = 'check';
    case CREDIT_CARD = 'credit_card';
    case WIRE_TRANSFER = 'wire_transfer';
    case CASH = 'cash';
    case OTHER = 'other';

    public function label(): string
    {
        return match($this) {
            self::BANK_TRANSFER => 'Bank Transfer',
            self::CHECK => 'Check',
            self::CREDIT_CARD => 'Credit Card',
            self::WIRE_TRANSFER => 'Wire Transfer',
            self::CASH => 'Cash',
            self::OTHER => 'Other',
        };
    }

    public function isElectronic(): bool
    {
        return in_array($this, [self::BANK_TRANSFER, self::CREDIT_CARD, self::WIRE_TRANSFER]);
    }

    public function isPhysical(): bool
    {
        return in_array($this, [self::CHECK, self::CASH]);
    }

    public function requiresReference(): bool
    {
        return in_array($this, [self::BANK_TRANSFER, self::WIRE_TRANSFER, self::CREDIT_CARD]);
    }

    public function icon(): string
    {
        return match($this) {
            self::BANK_TRANSFER => 'bank',
            self::CHECK => 'file-text',
            self::CREDIT_CARD => 'credit-card',
            self::WIRE_TRANSFER => 'wifi',
            self::CASH => 'dollar-sign',
            self::OTHER => 'more-horizontal',
        };
    }
}
