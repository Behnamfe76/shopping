<?php

namespace Fereydooni\Shopping\app\Enums;

enum SegmentType: string
{
    case DEMOGRAPHIC = 'demographic';
    case BEHAVIORAL = 'behavioral';
    case GEOGRAPHIC = 'geographic';
    case PSYCHOGRAPHIC = 'psychographic';
    case TRANSACTIONAL = 'transactional';
    case ENGAGEMENT = 'engagement';
    case LOYALTY = 'loyalty';
    case CUSTOM = 'custom';

    public function label(): string
    {
        return match($this) {
            self::DEMOGRAPHIC => 'Demographic',
            self::BEHAVIORAL => 'Behavioral',
            self::GEOGRAPHIC => 'Geographic',
            self::PSYCHOGRAPHIC => 'Psychographic',
            self::TRANSACTIONAL => 'Transactional',
            self::ENGAGEMENT => 'Engagement',
            self::LOYALTY => 'Loyalty',
            self::CUSTOM => 'Custom',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::DEMOGRAPHIC => 'blue',
            self::BEHAVIORAL => 'green',
            self::GEOGRAPHIC => 'purple',
            self::PSYCHOGRAPHIC => 'orange',
            self::TRANSACTIONAL => 'red',
            self::ENGAGEMENT => 'teal',
            self::LOYALTY => 'indigo',
            self::CUSTOM => 'gray',
        };
    }

    public function description(): string
    {
        return match($this) {
            self::DEMOGRAPHIC => 'Segments based on customer demographics like age, gender, income',
            self::BEHAVIORAL => 'Segments based on customer behavior and actions',
            self::GEOGRAPHIC => 'Segments based on customer location and geography',
            self::PSYCHOGRAPHIC => 'Segments based on customer lifestyle and personality',
            self::TRANSACTIONAL => 'Segments based on purchase history and transactions',
            self::ENGAGEMENT => 'Segments based on customer engagement levels',
            self::LOYALTY => 'Segments based on customer loyalty and retention',
            self::CUSTOM => 'Custom segments defined by business rules',
        };
    }

    public function isAutomatic(): bool
    {
        return in_array($this, [
            self::DEMOGRAPHIC,
            self::BEHAVIORAL,
            self::GEOGRAPHIC,
            self::TRANSACTIONAL,
            self::ENGAGEMENT,
            self::LOYALTY
        ]);
    }

    public function isManual(): bool
    {
        return $this === self::CUSTOM;
    }

    public function isDynamic(): bool
    {
        return in_array($this, [
            self::BEHAVIORAL,
            self::TRANSACTIONAL,
            self::ENGAGEMENT,
            self::LOYALTY
        ]);
    }

    public function isStatic(): bool
    {
        return in_array($this, [
            self::DEMOGRAPHIC,
            self::GEOGRAPHIC,
            self::PSYCHOGRAPHIC,
            self::CUSTOM
        ]);
    }
}
