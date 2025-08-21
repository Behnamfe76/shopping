<?php

namespace Fereydooni\Shopping\app\Enums;

enum CommunicationStatus: string
{
    case DRAFT = 'draft';
    case SCHEDULED = 'scheduled';
    case SENDING = 'sending';
    case SENT = 'sent';
    case DELIVERED = 'delivered';
    case OPENED = 'opened';
    case CLICKED = 'clicked';
    case BOUNCED = 'bounced';
    case UNSUBSCRIBED = 'unsubscribed';
    case CANCELLED = 'cancelled';
    case FAILED = 'failed';

    public function label(): string
    {
        return match($this) {
            self::DRAFT => 'Draft',
            self::SCHEDULED => 'Scheduled',
            self::SENDING => 'Sending',
            self::SENT => 'Sent',
            self::DELIVERED => 'Delivered',
            self::OPENED => 'Opened',
            self::CLICKED => 'Clicked',
            self::BOUNCED => 'Bounced',
            self::UNSUBSCRIBED => 'Unsubscribed',
            self::CANCELLED => 'Cancelled',
            self::FAILED => 'Failed',
        };
    }

    public function description(): string
    {
        return match($this) {
            self::DRAFT => 'Communication is in draft state',
            self::SCHEDULED => 'Communication is scheduled for future delivery',
            self::SENDING => 'Communication is currently being sent',
            self::SENT => 'Communication has been sent',
            self::DELIVERED => 'Communication has been delivered to recipient',
            self::OPENED => 'Communication has been opened by recipient',
            self::CLICKED => 'Communication has been clicked by recipient',
            self::BOUNCED => 'Communication delivery failed',
            self::UNSUBSCRIBED => 'Recipient has unsubscribed',
            self::CANCELLED => 'Communication has been cancelled',
            self::FAILED => 'Communication sending failed',
        };
    }

    public function isActive(): bool
    {
        return in_array($this, [self::DRAFT, self::SCHEDULED, self::SENDING]);
    }

    public function isCompleted(): bool
    {
        return in_array($this, [self::SENT, self::DELIVERED, self::OPENED, self::CLICKED]);
    }

    public function isFailed(): bool
    {
        return in_array($this, [self::BOUNCED, self::FAILED]);
    }

    public function isCancelled(): bool
    {
        return in_array($this, [self::CANCELLED, self::UNSUBSCRIBED]);
    }

    public function canBeEdited(): bool
    {
        return in_array($this, [self::DRAFT, self::SCHEDULED]);
    }

    public function canBeCancelled(): bool
    {
        return in_array($this, [self::DRAFT, self::SCHEDULED, self::SENDING]);
    }

    public static function toArray(): array
    {
        return array_map(fn($case) => [
            'value' => $case->value,
            'label' => $case->label(),
            'description' => $case->description(),
        ], self::cases());
    }
}
