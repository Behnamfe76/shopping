<?php

namespace Fereydooni\Shopping\App\Enums;

enum InvoiceStatus: string
{
    case DRAFT = 'draft';
    case SENT = 'sent';
    case PAID = 'paid';
    case OVERDUE = 'overdue';
    case CANCELLED = 'cancelled';
    case DISPUTED = 'disputed';
    case PARTIALLY_PAID = 'partially_paid';

    /**
     * Get all statuses
     */
    public static function all(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * Get status label
     */
    public function label(): string
    {
        return match($this) {
            self::DRAFT => 'Draft',
            self::SENT => 'Sent',
            self::PAID => 'Paid',
            self::OVERDUE => 'Overdue',
            self::CANCELLED => 'Cancelled',
            self::DISPUTED => 'Disputed',
            self::PARTIALLY_PAID => 'Partially Paid',
        };
    }

    /**
     * Check if status is editable
     */
    public function isEditable(): bool
    {
        return in_array($this, [self::DRAFT, self::SENT]);
    }

    /**
     * Check if status is final
     */
    public function isFinal(): bool
    {
        return in_array($this, [self::PAID, self::CANCELLED]);
    }

    /**
     * Check if status requires payment
     */
    public function requiresPayment(): bool
    {
        return in_array($this, [self::SENT, self::OVERDUE, self::PARTIALLY_PAID]);
    }

    /**
     * Get status color for UI
     */
    public function color(): string
    {
        return match($this) {
            self::DRAFT => 'gray',
            self::SENT => 'blue',
            self::PAID => 'green',
            self::OVERDUE => 'red',
            self::CANCELLED => 'black',
            self::DISPUTED => 'orange',
            self::PARTIALLY_PAID => 'yellow',
        };
    }
}

