<?php

namespace Fereydooni\Shopping\app\Enums;

enum PositionLevel: string
{
    case ENTRY = 'entry';
    case JUNIOR = 'junior';
    case MID = 'mid';
    case SENIOR = 'senior';
    case LEAD = 'lead';
    case MANAGER = 'manager';
    case DIRECTOR = 'director';
    case EXECUTIVE = 'executive';

    public function label(): string
    {
        return match ($this) {
            self::ENTRY => 'Entry Level',
            self::JUNIOR => 'Junior',
            self::MID => 'Mid Level',
            self::SENIOR => 'Senior',
            self::LEAD => 'Lead',
            self::MANAGER => 'Manager',
            self::DIRECTOR => 'Director',
            self::EXECUTIVE => 'Executive',
        };
    }

    public function shortLabel(): string
    {
        return match ($this) {
            self::ENTRY => 'Entry',
            self::JUNIOR => 'Junior',
            self::MID => 'Mid',
            self::SENIOR => 'Senior',
            self::LEAD => 'Lead',
            self::MANAGER => 'Manager',
            self::DIRECTOR => 'Director',
            self::EXECUTIVE => 'Exec',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::ENTRY => 'blue',
            self::JUNIOR => 'cyan',
            self::MID => 'green',
            self::SENIOR => 'yellow',
            self::LEAD => 'orange',
            self::MANAGER => 'red',
            self::DIRECTOR => 'purple',
            self::EXECUTIVE => 'indigo',
        };
    }

    public function isEntryLevel(): bool
    {
        return in_array($this, [self::ENTRY, self::JUNIOR]);
    }

    public function isMidLevel(): bool
    {
        return in_array($this, [self::MID, self::SENIOR]);
    }

    public function isSeniorLevel(): bool
    {
        return in_array($this, [self::LEAD, self::MANAGER, self::DIRECTOR, self::EXECUTIVE]);
    }

    public function isManagement(): bool
    {
        return in_array($this, [self::MANAGER, self::DIRECTOR, self::EXECUTIVE]);
    }

    public function getHierarchyLevel(): int
    {
        return match ($this) {
            self::ENTRY => 1,
            self::JUNIOR => 2,
            self::MID => 3,
            self::SENIOR => 4,
            self::LEAD => 5,
            self::MANAGER => 6,
            self::DIRECTOR => 7,
            self::EXECUTIVE => 8,
        };
    }

    public function requiresExperience(): int
    {
        return match ($this) {
            self::ENTRY => 0,
            self::JUNIOR => 1,
            self::MID => 3,
            self::SENIOR => 5,
            self::LEAD => 7,
            self::MANAGER => 8,
            self::DIRECTOR => 10,
            self::EXECUTIVE => 15,
        };
    }
}
