<?php

namespace Fereydooni\Shopping\app\Enums;

enum Relationship: string
{
    case SPOUSE = 'spouse';
    case PARENT = 'parent';
    case CHILD = 'child';
    case SIBLING = 'sibling';
    case FRIEND = 'friend';
    case OTHER = 'other';

    public function label(): string
    {
        return match ($this) {
            self::SPOUSE => 'Spouse',
            self::PARENT => 'Parent',
            self::CHILD => 'Child',
            self::SIBLING => 'Sibling',
            self::FRIEND => 'Friend',
            self::OTHER => 'Other',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::SPOUSE => 'Husband, wife, or domestic partner',
            self::PARENT => 'Father, mother, or legal guardian',
            self::CHILD => 'Son, daughter, or dependent child',
            self::SIBLING => 'Brother or sister',
            self::FRIEND => 'Close friend or acquaintance',
            self::OTHER => 'Other relationship type',
        };
    }

    public static function options(): array
    {
        $options = [];
        foreach (self::cases() as $case) {
            $options[$case->value] = $case->label();
        }
        return $options;
    }

    public static function fromLabel(string $label): ?self
    {
        foreach (self::cases() as $case) {
            if ($case->label() === $label) {
                return $case;
            }
        }
        return null;
    }
}
