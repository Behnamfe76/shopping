<?php

namespace Fereydooni\Shopping\App\Enums;

enum RatingCategory: string
{
    case OVERALL = 'overall';
    case QUALITY = 'quality';
    case SERVICE = 'service';
    case PRICING = 'pricing';
    case COMMUNICATION = 'communication';
    case RELIABILITY = 'reliability';
    case DELIVERY = 'delivery';
    case RESPONSIVENESS = 'responsiveness';
    case PROFESSIONALISM = 'professionalism';
    case VALUE = 'value';

    public function getDescription(): string
    {
        return match ($this) {
            self::OVERALL => 'Overall Rating',
            self::QUALITY => 'Quality Rating',
            self::SERVICE => 'Service Rating',
            self::PRICING => 'Pricing Rating',
            self::COMMUNICATION => 'Communication Rating',
            self::RELIABILITY => 'Reliability Rating',
            self::DELIVERY => 'Delivery Rating',
            self::RESPONSIVENESS => 'Responsiveness Rating',
            self::PROFESSIONALISM => 'Professionalism Rating',
            self::VALUE => 'Value for Money Rating',
        };
    }

    public function getIcon(): string
    {
        return match ($this) {
            self::OVERALL => '⭐',
            self::QUALITY => '🏆',
            self::SERVICE => '🛎️',
            self::PRICING => '💰',
            self::COMMUNICATION => '💬',
            self::RELIABILITY => '🔒',
            self::DELIVERY => '🚚',
            self::RESPONSIVENESS => '⚡',
            self::PROFESSIONALISM => '👔',
            self::VALUE => '💎',
        };
    }

    public function isPrimary(): bool
    {
        return in_array($this, [self::OVERALL, self::QUALITY, self::SERVICE]);
    }
}
