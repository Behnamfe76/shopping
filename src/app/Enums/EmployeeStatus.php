<?php

namespace Fereydooni\Shopping\app\Enums;

enum EmployeeStatus: string
{
    case ACTIVE = 'active';
    case INACTIVE = 'inactive';
    case TERMINATED = 'terminated';
    case PENDING = 'pending';
    case ON_LEAVE = 'on_leave';

    public function label(): string
    {
        return match($this) {
            self::ACTIVE => 'Active',
            self::INACTIVE => 'Inactive',
            self::TERMINATED => 'Terminated',
            self::PENDING => 'Pending',
            self::ON_LEAVE => 'On Leave',
        };
    }

    public function shortLabel(): string
    {
        return match($this) {
            self::ACTIVE => 'Active',
            self::INACTIVE => 'Inactive',
            self::TERMINATED => 'Terminated',
            self::PENDING => 'Pending',
            self::ON_LEAVE => 'Leave',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::ACTIVE => 'green',
            self::INACTIVE => 'yellow',
            self::TERMINATED => 'red',
            self::PENDING => 'blue',
            self::ON_LEAVE => 'orange',
        };
    }

    public function isActive(): bool
    {
        return $this === self::ACTIVE;
    }

    public function isTerminated(): bool
    {
        return $this === self::TERMINATED;
    }

    public function isOnLeave(): bool
    {
        return $this === self::ON_LEAVE;
    }

    public function canWork(): bool
    {
        return in_array($this, [self::ACTIVE, self::PENDING]);
    }
}

