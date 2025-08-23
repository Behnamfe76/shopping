<?php

namespace Fereydooni\Shopping\App\Enums;

enum PaymentTerms: string
{
    case IMMEDIATE = 'immediate';
    case NET_15 = 'net_15';
    case NET_30 = 'net_30';
    case NET_45 = 'net_45';
    case NET_60 = 'net_60';
    case NET_90 = 'net_90';
    case CUSTOM = 'custom';

    /**
     * Get all payment terms
     */
    public static function all(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * Get payment terms label
     */
    public function label(): string
    {
        return match($this) {
            self::IMMEDIATE => 'Immediate',
            self::NET_15 => 'Net 15',
            self::NET_30 => 'Net 30',
            self::NET_45 => 'Net 45',
            self::NET_60 => 'Net 60',
            self::NET_90 => 'Net 90',
            self::CUSTOM => 'Custom',
        };
    }

    /**
     * Get days for payment terms
     */
    public function days(): int
    {
        return match($this) {
            self::IMMEDIATE => 0,
            self::NET_15 => 15,
            self::NET_30 => 30,
            self::NET_45 => 45,
            self::NET_60 => 60,
            self::NET_90 => 90,
            self::CUSTOM => 0,
        };
    }

    /**
     * Check if payment terms are immediate
     */
    public function isImmediate(): bool
    {
        return $this === self::IMMEDIATE;
    }

    /**
     * Check if payment terms are custom
     */
    public function isCustom(): bool
    {
        return $this === self::CUSTOM;
    }

    /**
     * Get description for payment terms
     */
    public function description(): string
    {
        if ($this->isImmediate()) {
            return 'Payment due immediately';
        }

        if ($this->isCustom()) {
            return 'Custom payment terms';
        }

        return "Payment due within {$this->days()} days";
    }
}
