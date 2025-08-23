<?php

namespace Fereydooni\Shopping\Enums;

enum TrainingMethod: string
{
    case IN_PERSON = 'in_person';
    case ONLINE = 'online';
    case HYBRID = 'hybrid';
    case SELF_STUDY = 'self_study';
    case WORKSHOP = 'workshop';
    case SEMINAR = 'seminar';

    public function label(): string
    {
        return match($this) {
            self::IN_PERSON => 'In Person',
            self::ONLINE => 'Online',
            self::HYBRID => 'Hybrid',
            self::SELF_STUDY => 'Self Study',
            self::WORKSHOP => 'Workshop',
            self::SEMINAR => 'Seminar',
        };
    }

    public function description(): string
    {
        return match($this) {
            self::IN_PERSON => 'Traditional classroom-based training',
            self::ONLINE => 'Web-based or e-learning training',
            self::HYBRID => 'Combination of in-person and online training',
            self::SELF_STUDY => 'Self-paced learning with materials',
            self::WORKSHOP => 'Hands-on practical training session',
            self::SEMINAR => 'Presentation-based training session',
        };
    }

    public function isRemote(): bool
    {
        return in_array($this, [self::ONLINE, self::SELF_STUDY]);
    }

    public function isInPerson(): bool
    {
        return in_array($this, [self::IN_PERSON, self::WORKSHOP, self::SEMINAR]);
    }

    public function requiresInstructor(): bool
    {
        return in_array($this, [self::IN_PERSON, self::HYBRID, self::WORKSHOP, self::SEMINAR]);
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function labels(): array
    {
        return array_combine(
            self::values(),
            array_map(fn($case) => $case->label(), self::cases())
        );
    }
}
