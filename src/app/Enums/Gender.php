<?php

namespace Fereydooni\Shopping\app\Enums;

enum Gender: string
{
    case MALE = 'male';
    case FEMALE = 'female';
    case OTHER = 'other';
    case PREFER_NOT_TO_SAY = 'prefer_not_to_say';

    public function label(): string
    {
        return match ($this) {
            self::MALE => 'Male',
            self::FEMALE => 'Female',
            self::OTHER => 'Other',
            self::PREFER_NOT_TO_SAY => 'Prefer not to say',
        };
    }

    public function shortLabel(): string
    {
        return match ($this) {
            self::MALE => 'M',
            self::FEMALE => 'F',
            self::OTHER => 'O',
            self::PREFER_NOT_TO_SAY => 'N/A',
        };
    }
}
