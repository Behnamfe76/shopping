<?php

namespace App\Enums;

enum Status: string
{
    case DRAFT = 'draft';
    case SENT = 'sent';
    case DELIVERED = 'delivered';
    case READ = 'read';
    case REPLIED = 'replied';
    case CLOSED = 'closed';
    case ARCHIVED = 'archived';
    case FAILED = 'failed';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function labels(): array
    {
        return [
            self::DRAFT->value => 'Draft',
            self::SENT->value => 'Sent',
            self::DELIVERED->value => 'Delivered',
            self::READ->value => 'Read',
            self::REPLIED->value => 'Replied',
            self::CLOSED->value => 'Closed',
            self::ARCHIVED->value => 'Archived',
            self::FAILED->value => 'Failed',
        ];
    }

    public function label(): string
    {
        return self::labels()[$this->value] ?? $this->value;
    }
}
