<?php

namespace Fereydooni\Shopping\app\Enums;

enum EmployeeNotePriority: string
{
    case LOW = 'low';
    case MEDIUM = 'medium';
    case HIGH = 'high';
    case URGENT = 'urgent';

    public function label(): string
    {
        return match ($this) {
            self::LOW => 'Low',
            self::MEDIUM => 'Medium',
            self::HIGH => 'High',
            self::URGENT => 'Urgent',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::LOW => 'gray',
            self::MEDIUM => 'blue',
            self::HIGH => 'orange',
            self::URGENT => 'red',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::LOW => 'arrow-down',
            self::MEDIUM => 'minus',
            self::HIGH => 'arrow-up',
            self::URGENT => 'exclamation',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::LOW => 'Low priority - can be addressed when convenient',
            self::MEDIUM => 'Medium priority - should be addressed soon',
            self::HIGH => 'High priority - needs attention promptly',
            self::URGENT => 'Urgent priority - requires immediate attention',
        };
    }

    public function weight(): int
    {
        return match ($this) {
            self::LOW => 1,
            self::MEDIUM => 2,
            self::HIGH => 3,
            self::URGENT => 4,
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function labels(): array
    {
        return array_combine(
            array_column(self::cases(), 'value'),
            array_map(fn ($case) => $case->label(), self::cases())
        );
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

    public static function fromWeight(int $weight): ?self
    {
        foreach (self::cases() as $case) {
            if ($case->weight() === $weight) {
                return $case;
            }
        }

        return null;
    }
}
