<?php

namespace App\Enums;

enum PeriodType: string
{
    case DAILY = 'daily';
    case WEEKLY = 'weekly';
    case MONTHLY = 'monthly';
    case QUARTERLY = 'quarterly';
    case YEARLY = 'yearly';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public function getDescription(): string
    {
        return match ($this) {
            self::DAILY => 'Daily Performance',
            self::WEEKLY => 'Weekly Performance',
            self::MONTHLY => 'Monthly Performance',
            self::QUARTERLY => 'Quarterly Performance',
            self::YEARLY => 'Yearly Performance',
        };
    }

    public function getDays(): int
    {
        return match ($this) {
            self::DAILY => 1,
            self::WEEKLY => 7,
            self::MONTHLY => 30,
            self::QUARTERLY => 90,
            self::YEARLY => 365,
        };
    }

    public function getLabel(): string
    {
        return match ($this) {
            self::DAILY => 'Day',
            self::WEEKLY => 'Week',
            self::MONTHLY => 'Month',
            self::QUARTERLY => 'Quarter',
            self::YEARLY => 'Year',
        };
    }
}
