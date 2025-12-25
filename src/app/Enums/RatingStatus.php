<?php

namespace Fereydooni\Shopping\App\Enums;

enum RatingStatus: string
{
    case PENDING = 'pending';
    case APPROVED = 'approved';
    case REJECTED = 'rejected';
    case FLAGGED = 'flagged';
    case UNDER_REVIEW = 'under_review';
    case SPAM = 'spam';
    case INAPPROPRIATE = 'inappropriate';

    public function getDescription(): string
    {
        return match ($this) {
            self::PENDING => 'Pending Review',
            self::APPROVED => 'Approved',
            self::REJECTED => 'Rejected',
            self::FLAGGED => 'Flagged for Review',
            self::UNDER_REVIEW => 'Under Review',
            self::SPAM => 'Marked as Spam',
            self::INAPPROPRIATE => 'Marked as Inappropriate',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::PENDING => 'warning',
            self::APPROVED => 'success',
            self::REJECTED => 'danger',
            self::FLAGGED => 'warning',
            self::UNDER_REVIEW => 'info',
            self::SPAM => 'secondary',
            self::INAPPROPRIATE => 'danger',
        };
    }

    public function isVisible(): bool
    {
        return in_array($this, [self::APPROVED]);
    }

    public function requiresModeration(): bool
    {
        return in_array($this, [self::PENDING, self::FLAGGED, self::UNDER_REVIEW]);
    }

    public function isBlocked(): bool
    {
        return in_array($this, [self::REJECTED, self::SPAM, self::INAPPROPRIATE]);
    }
}
