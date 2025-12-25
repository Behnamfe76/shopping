<?php

namespace App\Enums;

enum PerformanceGrade: string
{
    case A = 'A';
    case B = 'B';
    case C = 'C';
    case D = 'D';
    case F = 'F';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public function getDescription(): string
    {
        return match ($this) {
            self::A => 'Excellent Performance',
            self::B => 'Good Performance',
            self::C => 'Average Performance',
            self::D => 'Below Average Performance',
            self::F => 'Poor Performance',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::A => 'green',
            self::B => 'blue',
            self::C => 'yellow',
            self::D => 'orange',
            self::F => 'red',
        };
    }

    public function getScoreRange(): array
    {
        return match ($this) {
            self::A => [90, 100],
            self::B => [80, 89],
            self::C => [70, 79],
            self::D => [60, 69],
            self::F => [0, 59],
        };
    }
}
