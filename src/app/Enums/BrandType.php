<?php

namespace Fereydooni\Shopping\app\Enums;

enum BrandType: string
{
    case LOCAL = 'local';
    case NATIONAL = 'national';
    case INTERNATIONAL = 'international';
    case PREMIUM = 'premium';
    case BUDGET = 'budget';

    /**
     * Get all type values.
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * Get type label.
     */
    public function label(): string
    {
        return match ($this) {
            self::LOCAL => 'Local',
            self::NATIONAL => 'National',
            self::INTERNATIONAL => 'International',
            self::PREMIUM => 'Premium',
            self::BUDGET => 'Budget',
        };
    }

    /**
     * Get type description.
     */
    public function description(): string
    {
        return match ($this) {
            self::LOCAL => 'Local brand operating in a specific region',
            self::NATIONAL => 'National brand operating across the country',
            self::INTERNATIONAL => 'International brand with global presence',
            self::PREMIUM => 'Premium brand with high-end products',
            self::BUDGET => 'Budget-friendly brand with affordable products',
        };
    }

    /**
     * Check if type is premium.
     */
    public function isPremium(): bool
    {
        return $this === self::PREMIUM;
    }

    /**
     * Check if type is budget.
     */
    public function isBudget(): bool
    {
        return $this === self::BUDGET;
    }

    /**
     * Check if type is international.
     */
    public function isInternational(): bool
    {
        return $this === self::INTERNATIONAL;
    }
}
