<?php

namespace Fereydooni\Shopping\App\Enums;

enum ProficiencyLevel: string
{
    case BEGINNER = 'beginner';
    case INTERMEDIATE = 'intermediate';
    case ADVANCED = 'advanced';
    case EXPERT = 'expert';
    case MASTER = 'master';

    public function label(): string
    {
        return match ($this) {
            self::BEGINNER => 'Beginner',
            self::INTERMEDIATE => 'Intermediate',
            self::ADVANCED => 'Advanced',
            self::EXPERT => 'Expert',
            self::MASTER => 'Master',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::BEGINNER => 'Basic knowledge and understanding',
            self::INTERMEDIATE => 'Working knowledge and practical experience',
            self::ADVANCED => 'Deep understanding and significant experience',
            self::EXPERT => 'Mastery with extensive experience',
            self::MASTER => 'Highest level of expertise and authority',
        };
    }

    public function numericValue(): int
    {
        return match ($this) {
            self::BEGINNER => 1,
            self::INTERMEDIATE => 2,
            self::ADVANCED => 3,
            self::EXPERT => 4,
            self::MASTER => 5,
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function labels(): array
    {
        return array_combine(
            self::values(),
            array_map(fn ($case) => $case->label(), self::cases())
        );
    }

    public static function fromNumeric(int $value): ?self
    {
        return match ($value) {
            1 => self::BEGINNER,
            2 => self::INTERMEDIATE,
            3 => self::ADVANCED,
            4 => self::EXPERT,
            5 => self::MASTER,
            default => null,
        };
    }
}
