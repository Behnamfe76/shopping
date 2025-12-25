<?php

namespace Fereydooni\Shopping\app\Enums;

enum TimeOffType: string
{
    case VACATION = 'vacation';
    case SICK = 'sick';
    case PERSONAL = 'personal';
    case BEREAVEMENT = 'bereavement';
    case JURY_DUTY = 'jury_duty';
    case MILITARY = 'military';
    case OTHER = 'other';

    public function label(): string
    {
        return match ($this) {
            self::VACATION => 'Vacation',
            self::SICK => 'Sick Leave',
            self::PERSONAL => 'Personal Leave',
            self::BEREAVEMENT => 'Bereavement Leave',
            self::JURY_DUTY => 'Jury Duty',
            self::MILITARY => 'Military Leave',
            self::OTHER => 'Other',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::VACATION => 'Paid time off for vacation purposes',
            self::SICK => 'Paid time off due to illness or injury',
            self::PERSONAL => 'Personal time off for various reasons',
            self::BEREAVEMENT => 'Time off due to death of family member',
            self::JURY_DUTY => 'Time off for jury duty service',
            self::MILITARY => 'Time off for military service',
            self::OTHER => 'Other types of time off',
        };
    }

    public function isPaid(): bool
    {
        return match ($this) {
            self::VACATION, self::SICK, self::BEREAVEMENT, self::JURY_DUTY, self::MILITARY => true,
            self::PERSONAL, self::OTHER => false,
        };
    }

    public function requiresApproval(): bool
    {
        return match ($this) {
            self::SICK => false,
            self::VACATION, self::PERSONAL, self::BEREAVEMENT, self::JURY_DUTY, self::MILITARY, self::OTHER => true,
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::VACATION => 'blue',
            self::SICK => 'red',
            self::PERSONAL => 'green',
            self::BEREAVEMENT => 'purple',
            self::JURY_DUTY => 'orange',
            self::MILITARY => 'navy',
            self::OTHER => 'gray',
        };
    }
}
