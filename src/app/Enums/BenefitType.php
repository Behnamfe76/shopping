<?php

namespace Fereydooni\Shopping\app\Enums;

enum BenefitType: string
{
    case HEALTH = 'health';
    case DENTAL = 'dental';
    case VISION = 'vision';
    case LIFE = 'life';
    case DISABILITY = 'disability';
    case RETIREMENT = 'retirement';
    case OTHER = 'other';

    public function label(): string
    {
        return match ($this) {
            self::HEALTH => 'Health Insurance',
            self::DENTAL => 'Dental Insurance',
            self::VISION => 'Vision Insurance',
            self::LIFE => 'Life Insurance',
            self::DISABILITY => 'Disability Insurance',
            self::RETIREMENT => 'Retirement Plan',
            self::OTHER => 'Other Benefits',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::HEALTH => 'Medical, hospital, and prescription drug coverage',
            self::DENTAL => 'Dental care and oral health coverage',
            self::VISION => 'Eye care and vision correction coverage',
            self::LIFE => 'Life insurance coverage for dependents',
            self::DISABILITY => 'Income protection during disability',
            self::RETIREMENT => '401(k), pension, or retirement savings',
            self::OTHER => 'Additional employee benefits',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function labels(): array
    {
        return array_reduce(self::cases(), function ($carry, $case) {
            $carry[$case->value] = $case->label();

            return $carry;
        }, []);
    }
}
