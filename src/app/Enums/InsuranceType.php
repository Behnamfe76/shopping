<?php

namespace Fereydooni\Shopping\App\Enums;

use Illuminate\Support\Collection;

enum InsuranceType: string
{
    case GENERAL_LIABILITY = 'general_liability';
    case PROFESSIONAL_LIABILITY = 'professional_liability';
    case PRODUCT_LIABILITY = 'product_liability';
    case WORKERS_COMPENSATION = 'workers_compensation';
    case AUTO_INSURANCE = 'auto_insurance';
    case PROPERTY_INSURANCE = 'property_insurance';
    case CYBER_INSURANCE = 'cyber_insurance';
    case OTHER = 'other';

    public function label(): string
    {
        return match ($this) {
            self::GENERAL_LIABILITY => 'General Liability',
            self::PROFESSIONAL_LIABILITY => 'Professional Liability',
            self::PRODUCT_LIABILITY => 'Product Liability',
            self::WORKERS_COMPENSATION => 'Workers Compensation',
            self::AUTO_INSURANCE => 'Auto Insurance',
            self::PROPERTY_INSURANCE => 'Property Insurance',
            self::CYBER_INSURANCE => 'Cyber Insurance',
            self::OTHER => 'Other',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::GENERAL_LIABILITY => 'Protects against claims of bodily injury, property damage, and personal injury',
            self::PROFESSIONAL_LIABILITY => 'Protects against claims of professional negligence or errors',
            self::PRODUCT_LIABILITY => 'Protects against claims related to product defects or injuries',
            self::WORKERS_COMPENSATION => 'Provides benefits to employees injured on the job',
            self::AUTO_INSURANCE => 'Protects against vehicle-related accidents and damages',
            self::PROPERTY_INSURANCE => 'Protects against damage to business property',
            self::CYBER_INSURANCE => 'Protects against cyber attacks and data breaches',
            self::OTHER => 'Other types of insurance coverage',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function options(): array
    {
        return [
            self::GENERAL_LIABILITY->value => 'General Liability',
            self::PROFESSIONAL_LIABILITY->value => 'Professional Liability',
            self::PRODUCT_LIABILITY->value => 'Product Liability',
            self::WORKERS_COMPENSATION->value => 'Workers Compensation',
            self::AUTO_INSURANCE->value => 'Auto Insurance',
            self::PROPERTY_INSURANCE->value => 'Property Insurance',
            self::CYBER_INSURANCE->value => 'Cyber Insurance',
            self::OTHER->value => 'Other',
        ];
    }
}
