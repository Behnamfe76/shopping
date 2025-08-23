<?php

namespace Fereydooni\Shopping\app\Enums;

enum CoverageLevel: string
{
    case INDIVIDUAL = 'individual';
    case FAMILY = 'family';
    case EMPLOYEE_PLUS_SPOUSE = 'employee_plus_spouse';
    case EMPLOYEE_PLUS_CHILDREN = 'employee_plus_children';

    public function label(): string
    {
        return match($this) {
            self::INDIVIDUAL => 'Individual',
            self::FAMILY => 'Family',
            self::EMPLOYEE_PLUS_SPOUSE => 'Employee + Spouse',
            self::EMPLOYEE_PLUS_CHILDREN => 'Employee + Children',
        };
    }

    public function description(): string
    {
        return match($this) {
            self::INDIVIDUAL => 'Coverage for employee only',
            self::FAMILY => 'Coverage for employee and all dependents',
            self::EMPLOYEE_PLUS_SPOUSE => 'Coverage for employee and spouse',
            self::EMPLOYEE_PLUS_CHILDREN => 'Coverage for employee and children',
        };
    }

    public function isIndividual(): bool
    {
        return $this === self::INDIVIDUAL;
    }

    public function isFamily(): bool
    {
        return $this === self::FAMILY;
    }

    public function includesSpouse(): bool
    {
        return in_array($this, [self::FAMILY, self::EMPLOYEE_PLUS_SPOUSE]);
    }

    public function includesChildren(): bool
    {
        return in_array($this, [self::FAMILY, self::EMPLOYEE_PLUS_CHILDREN]);
    }

    public function getDependentCount(): int
    {
        return match($this) {
            self::INDIVIDUAL => 0,
            self::EMPLOYEE_PLUS_SPOUSE => 1,
            self::EMPLOYEE_PLUS_CHILDREN => 1,
            self::FAMILY => 2, // Assuming spouse + children
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

