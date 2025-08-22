<?php

namespace Fereydooni\Shopping\app\Enums;

enum EmploymentType: string
{
    case FULL_TIME = 'full_time';
    case PART_TIME = 'part_time';
    case CONTRACT = 'contract';
    case TEMPORARY = 'temporary';
    case INTERN = 'intern';
    case FREELANCE = 'freelance';

    public function label(): string
    {
        return match($this) {
            self::FULL_TIME => 'Full Time',
            self::PART_TIME => 'Part Time',
            self::CONTRACT => 'Contract',
            self::TEMPORARY => 'Temporary',
            self::INTERN => 'Intern',
            self::FREELANCE => 'Freelance',
        };
    }

    public function shortLabel(): string
    {
        return match($this) {
            self::FULL_TIME => 'FT',
            self::PART_TIME => 'PT',
            self::CONTRACT => 'Contract',
            self::TEMPORARY => 'Temp',
            self::INTERN => 'Intern',
            self::FREELANCE => 'Freelance',
        };
    }

    public function isFullTime(): bool
    {
        return $this === self::FULL_TIME;
    }

    public function isPartTime(): bool
    {
        return $this === self::PART_TIME;
    }

    public function isContract(): bool
    {
        return $this === self::CONTRACT;
    }

    public function isTemporary(): bool
    {
        return $this === self::TEMPORARY;
    }

    public function isIntern(): bool
    {
        return $this === self::INTERN;
    }

    public function isFreelance(): bool
    {
        return $this === self::FREELANCE;
    }

    public function isPermanent(): bool
    {
        return in_array($this, [self::FULL_TIME, self::PART_TIME]);
    }

    public function isTemporaryOrContract(): bool
    {
        return in_array($this, [self::CONTRACT, self::TEMPORARY, self::INTERN, self::FREELANCE]);
    }

    public function getDefaultVacationDays(): int
    {
        return match($this) {
            self::FULL_TIME => 20,
            self::PART_TIME => 10,
            self::CONTRACT => 0,
            self::TEMPORARY => 0,
            self::INTERN => 0,
            self::FREELANCE => 0,
        };
    }

    public function getDefaultSickDays(): int
    {
        return match($this) {
            self::FULL_TIME => 10,
            self::PART_TIME => 5,
            self::CONTRACT => 0,
            self::TEMPORARY => 0,
            self::INTERN => 0,
            self::FREELANCE => 0,
        };
    }
}

