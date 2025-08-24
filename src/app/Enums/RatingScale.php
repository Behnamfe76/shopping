<?php

namespace Fereydooni\Shopping\App\Enums;

enum RatingScale: string
{
    case FIVE_STAR = '5_star';
    case TEN_SCALE = '10_scale';
    case PERCENTAGE = 'percentage';

    public function getMaxValue(): int
    {
        return match($this) {
            self::FIVE_STAR => 5,
            self::TEN_SCALE => 10,
            self::PERCENTAGE => 100,
        };
    }

    public function getMinValue(): int
    {
        return match($this) {
            self::FIVE_STAR => 1,
            self::TEN_SCALE => 1,
            self::PERCENTAGE => 0,
        };
    }

    public function getDescription(): string
    {
        return match($this) {
            self::FIVE_STAR => '1-5 Star Rating',
            self::TEN_SCALE => '1-10 Scale Rating',
            self::PERCENTAGE => '0-100% Rating',
        };
    }

    public function isValidRating(float $rating): bool
    {
        return $rating >= $this->getMinValue() && $rating <= $this->getMaxValue();
    }
}
