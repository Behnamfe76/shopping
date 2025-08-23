<?php

namespace App\Enums;

enum SalaryChangeType: string
{
    case PROMOTION = 'promotion';
    case MERIT = 'merit';
    case COST_OF_LIVING = 'cost_of_living';
    case MARKET_ADJUSTMENT = 'market_adjustment';
    case DEMOTION = 'demotion';
    case PERFORMANCE_BONUS = 'performance_bonus';
    case RETENTION_BONUS = 'retention_bonus';
    case HIRING_BONUS = 'hiring_bonus';
    case SEVERANCE = 'severance';
    case SALARY_FREEZE = 'salary_freeze';
    case SALARY_REDUCTION = 'salary_reduction';
    case EQUITY_ADJUSTMENT = 'equity_adjustment';
    case COMPRESSION_ADJUSTMENT = 'compression_adjustment';
    case GEOGRAPHIC_ADJUSTMENT = 'geographic_adjustment';
    case SKILL_ADJUSTMENT = 'skill_adjustment';
    case EXPERIENCE_ADJUSTMENT = 'experience_adjustment';
    case EDUCATION_ADJUSTMENT = 'education_adjustment';
    case CERTIFICATION_BONUS = 'certification_bonus';
    case LANGUAGE_BONUS = 'language_bonus';
    case SHIFT_DIFFERENTIAL = 'shift_differential';
    case OVERTIME_RATE = 'overtime_rate';
    case HOLIDAY_PAY = 'holiday_pay';
    case WEEKEND_PAY = 'weekend_pay';
    case NIGHT_SHIFT_PAY = 'night_shift_pay';
    case HAZARD_PAY = 'hazard_pay';
    case TRAVEL_PAY = 'travel_pay';
    case RELOCATION_ADJUSTMENT = 'relocation_adjustment';
    case OTHER = 'other';

    public function label(): string
    {
        return match($this) {
            self::PROMOTION => 'Promotion',
            self::MERIT => 'Merit Increase',
            self::COST_OF_LIVING => 'Cost of Living Adjustment',
            self::MARKET_ADJUSTMENT => 'Market Adjustment',
            self::DEMOTION => 'Demotion',
            self::PERFORMANCE_BONUS => 'Performance Bonus',
            self::RETENTION_BONUS => 'Retention Bonus',
            self::HIRING_BONUS => 'Hiring Bonus',
            self::SEVERANCE => 'Severance',
            self::SALARY_FREEZE => 'Salary Freeze',
            self::SALARY_REDUCTION => 'Salary Reduction',
            self::EQUITY_ADJUSTMENT => 'Equity Adjustment',
            self::COMPRESSION_ADJUSTMENT => 'Compression Adjustment',
            self::GEOGRAPHIC_ADJUSTMENT => 'Geographic Adjustment',
            self::SKILL_ADJUSTMENT => 'Skill Adjustment',
            self::EXPERIENCE_ADJUSTMENT => 'Experience Adjustment',
            self::EDUCATION_ADJUSTMENT => 'Education Adjustment',
            self::CERTIFICATION_BONUS => 'Certification Bonus',
            self::LANGUAGE_BONUS => 'Language Bonus',
            self::SHIFT_DIFFERENTIAL => 'Shift Differential',
            self::OVERTIME_RATE => 'Overtime Rate',
            self::HOLIDAY_PAY => 'Holiday Pay',
            self::WEEKEND_PAY => 'Weekend Pay',
            self::NIGHT_SHIFT_PAY => 'Night Shift Pay',
            self::HAZARD_PAY => 'Hazard Pay',
            self::TRAVEL_PAY => 'Travel Pay',
            self::RELOCATION_ADJUSTMENT => 'Relocation Adjustment',
            self::OTHER => 'Other',
        };
    }

    public function description(): string
    {
        return match($this) {
            self::PROMOTION => 'Salary increase due to promotion to a higher position',
            self::MERIT => 'Salary increase based on performance and merit',
            self::COST_OF_LIVING => 'Salary adjustment to account for inflation and cost of living changes',
            self::MARKET_ADJUSTMENT => 'Salary adjustment to align with market rates',
            self::DEMOTION => 'Salary decrease due to demotion to a lower position',
            self::PERFORMANCE_BONUS => 'One-time bonus based on exceptional performance',
            self::RETENTION_BONUS => 'Bonus to retain valuable employees',
            self::HIRING_BONUS => 'One-time bonus for new hires',
            self::SEVERANCE => 'Compensation upon termination or layoff',
            self::SALARY_FREEZE => 'No salary change due to company policy',
            self::SALARY_REDUCTION => 'Salary decrease due to company restructuring or performance',
            self::EQUITY_ADJUSTMENT => 'Salary adjustment to ensure pay equity across similar roles',
            self::COMPRESSION_ADJUSTMENT => 'Salary adjustment to address pay compression issues',
            self::GEOGRAPHIC_ADJUSTMENT => 'Salary adjustment based on geographic location',
            self::SKILL_ADJUSTMENT => 'Salary adjustment for new or enhanced skills',
            self::EXPERIENCE_ADJUSTMENT => 'Salary adjustment for increased experience',
            self::EDUCATION_ADJUSTMENT => 'Salary adjustment for additional education or degrees',
            self::CERTIFICATION_BONUS => 'Bonus for obtaining professional certifications',
            self::LANGUAGE_BONUS => 'Bonus for multilingual skills',
            self::SHIFT_DIFFERENTIAL => 'Additional pay for working non-standard shifts',
            self::OVERTIME_RATE => 'Adjustment to overtime pay rates',
            self::HOLIDAY_PAY => 'Additional pay for working holidays',
            self::WEEKEND_PAY => 'Additional pay for working weekends',
            self::NIGHT_SHIFT_PAY => 'Additional pay for working night shifts',
            self::HAZARD_PAY => 'Additional pay for hazardous work conditions',
            self::TRAVEL_PAY => 'Additional pay for travel-related work',
            self::RELOCATION_ADJUSTMENT => 'Salary adjustment due to relocation',
            self::OTHER => 'Other salary changes not covered by specific categories',
        };
    }

    public function isPositive(): bool
    {
        return match($this) {
            self::PROMOTION, self::MERIT, self::COST_OF_LIVING, self::MARKET_ADJUSTMENT,
            self::PERFORMANCE_BONUS, self::RETENTION_BONUS, self::HIRING_BONUS,
            self::EQUITY_ADJUSTMENT, self::COMPRESSION_ADJUSTMENT, self::GEOGRAPHIC_ADJUSTMENT,
            self::SKILL_ADJUSTMENT, self::EXPERIENCE_ADJUSTMENT, self::EDUCATION_ADJUSTMENT,
            self::CERTIFICATION_BONUS, self::LANGUAGE_BONUS, self::SHIFT_DIFFERENTIAL,
            self::OVERTIME_RATE, self::HOLIDAY_PAY, self::WEEKEND_PAY, self::NIGHT_SHIFT_PAY,
            self::HAZARD_PAY, self::TRAVEL_PAY, self::RELOCATION_ADJUSTMENT => true,
            self::DEMOTION, self::SEVERANCE, self::SALARY_REDUCTION => false,
            self::SALARY_FREEZE, self::OTHER => false,
        };
    }

    public function isNegative(): bool
    {
        return match($this) {
            self::DEMOTION, self::SEVERANCE, self::SALARY_REDUCTION => true,
            self::PROMOTION, self::MERIT, self::COST_OF_LIVING, self::MARKET_ADJUSTMENT,
            self::PERFORMANCE_BONUS, self::RETENTION_BONUS, self::HIRING_BONUS,
            self::EQUITY_ADJUSTMENT, self::COMPRESSION_ADJUSTMENT, self::GEOGRAPHIC_ADJUSTMENT,
            self::SKILL_ADJUSTMENT, self::EXPERIENCE_ADJUSTMENT, self::EDUCATION_ADJUSTMENT,
            self::CERTIFICATION_BONUS, self::LANGUAGE_BONUS, self::SHIFT_DIFFERENTIAL,
            self::OVERTIME_RATE, self::HOLIDAY_PAY, self::WEEKEND_PAY, self::NIGHT_SHIFT_PAY,
            self::HAZARD_PAY, self::TRAVEL_PAY, self::RELOCATION_ADJUSTMENT => false,
            self::SALARY_FREEZE, self::OTHER => false,
        };
    }

    public function isNeutral(): bool
    {
        return match($this) {
            self::SALARY_FREEZE, self::OTHER => true,
            default => false,
        };
    }

    public function requiresApproval(): bool
    {
        return match($this) {
            self::PROMOTION, self::DEMOTION, self::SALARY_REDUCTION, self::SEVERANCE => true,
            self::MERIT, self::COST_OF_LIVING, self::MARKET_ADJUSTMENT => true,
            self::PERFORMANCE_BONUS, self::RETENTION_BONUS, self::HIRING_BONUS => true,
            self::EQUITY_ADJUSTMENT, self::COMPRESSION_ADJUSTMENT => true,
            self::GEOGRAPHIC_ADJUSTMENT, self::RELOCATION_ADJUSTMENT => true,
            self::SKILL_ADJUSTMENT, self::EXPERIENCE_ADJUSTMENT, self::EDUCATION_ADJUSTMENT => true,
            self::CERTIFICATION_BONUS, self::LANGUAGE_BONUS => true,
            self::SHIFT_DIFFERENTIAL, self::OVERTIME_RATE, self::HOLIDAY_PAY => false,
            self::WEEKEND_PAY, self::NIGHT_SHIFT_PAY, self::HAZARD_PAY, self::TRAVEL_PAY => false,
            self::SALARY_FREEZE, self::OTHER => true,
        };
    }

    public function isRetroactiveEligible(): bool
    {
        return match($this) {
            self::PROMOTION, self::MERIT, self::COST_OF_LIVING, self::MARKET_ADJUSTMENT,
            self::EQUITY_ADJUSTMENT, self::COMPRESSION_ADJUSTMENT, self::GEOGRAPHIC_ADJUSTMENT,
            self::SKILL_ADJUSTMENT, self::EXPERIENCE_ADJUSTMENT, self::EDUCATION_ADJUSTMENT => true,
            self::DEMOTION, self::PERFORMANCE_BONUS, self::RETENTION_BONUS, self::HIRING_BONUS,
            self::SEVERANCE, self::SALARY_FREEZE, self::SALARY_REDUCTION, self::OTHER => false,
            self::CERTIFICATION_BONUS, self::LANGUAGE_BONUS, self::SHIFT_DIFFERENTIAL,
            self::OVERTIME_RATE, self::HOLIDAY_PAY, self::WEEKEND_PAY, self::NIGHT_SHIFT_PAY,
            self::HAZARD_PAY, self::TRAVEL_PAY, self::RELOCATION_ADJUSTMENT => false,
        };
    }

    public function getCategory(): string
    {
        return match($this) {
            self::PROMOTION, self::DEMOTION => 'Position Change',
            self::MERIT, self::PERFORMANCE_BONUS => 'Performance',
            self::COST_OF_LIVING, self::MARKET_ADJUSTMENT => 'Market',
            self::RETENTION_BONUS, self::HIRING_BONUS => 'Recruitment',
            self::SEVERANCE => 'Termination',
            self::SALARY_FREEZE, self::SALARY_REDUCTION => 'Restructuring',
            self::EQUITY_ADJUSTMENT, self::COMPRESSION_ADJUSTMENT => 'Equity',
            self::GEOGRAPHIC_ADJUSTMENT, self::RELOCATION_ADJUSTMENT => 'Location',
            self::SKILL_ADJUSTMENT, self::EXPERIENCE_ADJUSTMENT, self::EDUCATION_ADJUSTMENT,
            self::CERTIFICATION_BONUS, self::LANGUAGE_BONUS => 'Development',
            self::SHIFT_DIFFERENTIAL, self::OVERTIME_RATE, self::HOLIDAY_PAY,
            self::WEEKEND_PAY, self::NIGHT_SHIFT_PAY, self::HAZARD_PAY, self::TRAVEL_PAY => 'Work Conditions',
            self::OTHER => 'Other',
        };
    }

    public static function getPositiveTypes(): array
    {
        return array_filter(self::cases(), fn($type) => $type->isPositive());
    }

    public static function getNegativeTypes(): array
    {
        return array_filter(self::cases(), fn($type) => $type->isNegative());
    }

    public static function getNeutralTypes(): array
    {
        return array_filter(self::cases(), fn($type) => $type->isNeutral());
    }

    public static function getApprovalRequiredTypes(): array
    {
        return array_filter(self::cases(), fn($type) => $type->requiresApproval());
    }

    public static function getRetroactiveEligibleTypes(): array
    {
        return array_filter(self::cases(), fn($type) => $type->isRetroactiveEligible());
    }

    public static function getByCategory(string $category): array
    {
        return array_filter(self::cases(), fn($type) => $type->getCategory() === $category);
    }
}
