<?php

namespace Fereydooni\Shopping\app\Enums;

enum CommunicationType: string
{
    case EMAIL = 'email';
    case SMS = 'sms';
    case PUSH_NOTIFICATION = 'push_notification';
    case IN_APP = 'in_app';
    case LETTER = 'letter';
    case PHONE_CALL = 'phone_call';

    public function label(): string
    {
        return match($this) {
            self::EMAIL => 'Email',
            self::SMS => 'SMS',
            self::PUSH_NOTIFICATION => 'Push Notification',
            self::IN_APP => 'In-App Message',
            self::LETTER => 'Letter',
            self::PHONE_CALL => 'Phone Call',
        };
    }

    public function description(): string
    {
        return match($this) {
            self::EMAIL => 'Electronic mail communication',
            self::SMS => 'Short message service',
            self::PUSH_NOTIFICATION => 'Mobile push notification',
            self::IN_APP => 'In-application message',
            self::LETTER => 'Physical letter',
            self::PHONE_CALL => 'Voice call',
        };
    }

    public function isDigital(): bool
    {
        return in_array($this, [self::EMAIL, self::SMS, self::PUSH_NOTIFICATION, self::IN_APP]);
    }

    public function isPhysical(): bool
    {
        return in_array($this, [self::LETTER, self::PHONE_CALL]);
    }

    public function requiresTemplate(): bool
    {
        return in_array($this, [self::EMAIL, self::SMS, self::PUSH_NOTIFICATION, self::IN_APP]);
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
