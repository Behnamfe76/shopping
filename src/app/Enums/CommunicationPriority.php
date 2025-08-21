<?php

namespace Fereydooni\Shopping\app\Enums;

enum CommunicationPriority: string
{
    case LOW = 'low';
    case NORMAL = 'normal';
    case HIGH = 'high';
    case URGENT = 'urgent';

    public function label(): string
    {
        return match($this) {
            self::LOW => 'Low',
            self::NORMAL => 'Normal',
            self::HIGH => 'High',
            self::URGENT => 'Urgent',
        };
    }

    public function description(): string
    {
        return match($this) {
            self::LOW => 'Low priority communication',
            self::NORMAL => 'Normal priority communication',
            self::HIGH => 'High priority communication',
            self::URGENT => 'Urgent priority communication',
        };
    }

    public function getWeight(): int
    {
        return match($this) {
            self::LOW => 1,
            self::NORMAL => 2,
            self::HIGH => 3,
            self::URGENT => 4,
        };
    }

    public function getColor(): string
    {
        return match($this) {
            self::LOW => 'gray',
            self::NORMAL => 'blue',
            self::HIGH => 'orange',
            self::URGENT => 'red',
        };
    }

    public function requiresImmediateAttention(): bool
    {
        return in_array($this, [self::HIGH, self::URGENT]);
    }

    public static function toArray(): array
    {
        return array_map(fn($case) => [
            'value' => $case->value,
            'label' => $case->label(),
            'description' => $case->description(),
            'weight' => $case->getWeight(),
            'color' => $case->getColor(),
        ], self::cases());
    }
}
