<?php

namespace Fereydooni\Shopping\app\Enums;

enum CustomerNoteType: string
{
    case GENERAL = 'general';
    case SUPPORT = 'support';
    case SALES = 'sales';
    case BILLING = 'billing';
    case TECHNICAL = 'technical';
    case COMPLAINT = 'complaint';
    case FEEDBACK = 'feedback';
    case FOLLOW_UP = 'follow_up';

    public function label(): string
    {
        return match ($this) {
            self::GENERAL => 'General',
            self::SUPPORT => 'Support',
            self::SALES => 'Sales',
            self::BILLING => 'Billing',
            self::TECHNICAL => 'Technical',
            self::COMPLAINT => 'Complaint',
            self::FEEDBACK => 'Feedback',
            self::FOLLOW_UP => 'Follow Up',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::GENERAL => 'gray',
            self::SUPPORT => 'blue',
            self::SALES => 'green',
            self::BILLING => 'purple',
            self::TECHNICAL => 'orange',
            self::COMPLAINT => 'red',
            self::FEEDBACK => 'yellow',
            self::FOLLOW_UP => 'indigo',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::GENERAL => 'document-text',
            self::SUPPORT => 'support',
            self::SALES => 'shopping-cart',
            self::BILLING => 'credit-card',
            self::TECHNICAL => 'wrench',
            self::COMPLAINT => 'exclamation-triangle',
            self::FEEDBACK => 'chat-bubble-left-right',
            self::FOLLOW_UP => 'arrow-path',
        };
    }

    public function isUrgent(): bool
    {
        return in_array($this, [self::COMPLAINT, self::TECHNICAL]);
    }

    public function requiresFollowUp(): bool
    {
        return in_array($this, [self::COMPLAINT, self::SUPPORT, self::FOLLOW_UP]);
    }

    public function isCustomerFacing(): bool
    {
        return in_array($this, [self::SUPPORT, self::BILLING, self::COMPLAINT, self::FEEDBACK]);
    }
}
