<?php

namespace Fereydooni\Shopping\app\Enums;

enum PositionStatus: string
{
    case ACTIVE = 'active';
    case INACTIVE = 'inactive';
    case ARCHIVED = 'archived';
    case HIRING = 'hiring';
    case FROZEN = 'frozen';

    public function label(): string
    {
        return match($this) {
            self::ACTIVE => 'Active',
            self::INACTIVE => 'Inactive',
            self::ARCHIVED => 'Archived',
            self::HIRING => 'Hiring',
            self::FROZEN => 'Frozen',
        };
    }

    public function shortLabel(): string
    {
        return match($this) {
            self::ACTIVE => 'Active',
            self::INACTIVE => 'Inactive',
            self::ARCHIVED => 'Archived',
            self::HIRING => 'Hiring',
            self::FROZEN => 'Frozen',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::ACTIVE => 'green',
            self::INACTIVE => 'yellow',
            self::ARCHIVED => 'gray',
            self::HIRING => 'blue',
            self::FROZEN => 'red',
        };
    }

    public function isActive(): bool
    {
        return $this === self::ACTIVE;
    }

    public function isHiring(): bool
    {
        return $this === self::HIRING;
    }

    public function isArchived(): bool
    {
        return $this === self::ARCHIVED;
    }

    public function isFrozen(): bool
    {
        return $this === self::FROZEN;
    }

    public function canHire(): bool
    {
        return in_array($this, [self::ACTIVE, self::HIRING]);
    }

    public function isVisible(): bool
    {
        return !in_array($this, [self::ARCHIVED]);
    }
}
