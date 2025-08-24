<?php

namespace App\Enums;

enum Direction: string
{
    case INBOUND = 'inbound';
    case OUTBOUND = 'outbound';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function labels(): array
    {
        return [
            self::INBOUND->value => 'Inbound',
            self::OUTBOUND->value => 'Outbound',
        ];
    }

    public function label(): string
    {
        return self::labels()[$this->value] ?? $this->value;
    }
}
