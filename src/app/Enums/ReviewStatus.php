<?php

namespace Fereydooni\Shopping\app\Enums;

enum ReviewStatus: string
{
    case PENDING = 'pending';
    case APPROVED = 'approved';
    case REJECTED = 'rejected';
    case FLAGGED = 'flagged';
    case SPAM = 'spam';

    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'Pending',
            self::APPROVED => 'Approved',
            self::REJECTED => 'Rejected',
            self::FLAGGED => 'Flagged',
            self::SPAM => 'Spam',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::PENDING => 'warning',
            self::APPROVED => 'success',
            self::REJECTED => 'danger',
            self::FLAGGED => 'warning',
            self::SPAM => 'danger',
        };
    }

    public function isActive(): bool
    {
        return $this === self::APPROVED;
    }

    public function isModerated(): bool
    {
        return in_array($this, [self::APPROVED, self::REJECTED]);
    }

    public function requiresModeration(): bool
    {
        return $this === self::PENDING;
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
}
