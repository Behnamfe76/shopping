<?php

namespace Fereydooni\Shopping\app\Enums;

enum CommunicationChannel: string
{
    case EMAIL = 'email';
    case SMS = 'sms';
    case PUSH = 'push';
    case WEB = 'web';
    case MOBILE = 'mobile';
    case MAIL = 'mail';
    case PHONE = 'phone';

    public function label(): string
    {
        return match ($this) {
            self::EMAIL => 'Email',
            self::SMS => 'SMS',
            self::PUSH => 'Push Notification',
            self::WEB => 'Web',
            self::MOBILE => 'Mobile App',
            self::MAIL => 'Physical Mail',
            self::PHONE => 'Phone',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::EMAIL => 'Electronic mail delivery',
            self::SMS => 'Short message service',
            self::PUSH => 'Mobile push notification',
            self::WEB => 'Web-based communication',
            self::MOBILE => 'Mobile application',
            self::MAIL => 'Physical mail delivery',
            self::PHONE => 'Voice communication',
        };
    }

    public function isDigital(): bool
    {
        return in_array($this, [self::EMAIL, self::SMS, self::PUSH, self::WEB, self::MOBILE]);
    }

    public function isPhysical(): bool
    {
        return in_array($this, [self::MAIL, self::PHONE]);
    }

    public function supportsTracking(): bool
    {
        return in_array($this, [self::EMAIL, self::SMS, self::PUSH, self::WEB, self::MOBILE]);
    }

    public function supportsScheduling(): bool
    {
        return in_array($this, [self::EMAIL, self::SMS, self::PUSH, self::WEB, self::MOBILE]);
    }

    public function getIcon(): string
    {
        return match ($this) {
            self::EMAIL => 'mail',
            self::SMS => 'message-circle',
            self::PUSH => 'bell',
            self::WEB => 'globe',
            self::MOBILE => 'smartphone',
            self::MAIL => 'package',
            self::PHONE => 'phone',
        };
    }

    public static function toArray(): array
    {
        return array_map(fn ($case) => [
            'value' => $case->value,
            'label' => $case->label(),
            'description' => $case->description(),
            'is_digital' => $case->isDigital(),
            'is_physical' => $case->isPhysical(),
            'supports_tracking' => $case->supportsTracking(),
            'supports_scheduling' => $case->supportsScheduling(),
            'icon' => $case->getIcon(),
        ], self::cases());
    }
}
