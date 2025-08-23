<?php

namespace Fereydooni\Shopping\App\Enums;

enum ContractType: string
{
    case SERVICE = 'service';
    case SUPPLY = 'supply';
    case DISTRIBUTION = 'distribution';
    case PARTNERSHIP = 'partnership';
    case OTHER = 'other';

    public function label(): string
    {
        return match ($this) {
            self::SERVICE => 'Service Contract',
            self::SUPPLY => 'Supply Contract',
            self::DISTRIBUTION => 'Distribution Contract',
            self::PARTNERSHIP => 'Partnership Contract',
            self::OTHER => 'Other Contract',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::SERVICE => 'Contract for providing services',
            self::SUPPLY => 'Contract for supplying goods',
            self::DISTRIBUTION => 'Contract for distribution rights',
            self::PARTNERSHIP => 'Partnership agreement',
            self::OTHER => 'Other type of contract',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function labels(): array
    {
        return [
            self::SERVICE->value => self::SERVICE->label(),
            self::SUPPLY->value => self::SUPPLY->label(),
            self::DISTRIBUTION->value => self::DISTRIBUTION->label(),
            self::PARTNERSHIP->value => self::PARTNERSHIP->label(),
            self::OTHER->value => self::OTHER->label(),
        ];
    }
}
