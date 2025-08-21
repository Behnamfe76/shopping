<?php

namespace Fereydooni\Shopping\app\Enums;

enum SegmentStatus: string
{
    case ACTIVE = 'active';
    case INACTIVE = 'inactive';
    case DRAFT = 'draft';
    case ARCHIVED = 'archived';

    public function label(): string
    {
        return match($this) {
            self::ACTIVE => 'Active',
            self::INACTIVE => 'Inactive',
            self::DRAFT => 'Draft',
            self::ARCHIVED => 'Archived',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::ACTIVE => 'green',
            self::INACTIVE => 'gray',
            self::DRAFT => 'yellow',
            self::ARCHIVED => 'red',
        };
    }

    public function description(): string
    {
        return match($this) {
            self::ACTIVE => 'Segment is active and being used for targeting',
            self::INACTIVE => 'Segment is inactive and not being used',
            self::DRAFT => 'Segment is in draft mode and not yet active',
            self::ARCHIVED => 'Segment has been archived and is no longer available',
        };
    }

    public function isActive(): bool
    {
        return $this === self::ACTIVE;
    }

    public function isInactive(): bool
    {
        return $this === self::INACTIVE;
    }

    public function isDraft(): bool
    {
        return $this === self::DRAFT;
    }

    public function isArchived(): bool
    {
        return $this === self::ARCHIVED;
    }

    public function canBeUsed(): bool
    {
        return $this === self::ACTIVE;
    }

    public function canBeEdited(): bool
    {
        return in_array($this, [self::ACTIVE, self::INACTIVE, self::DRAFT]);
    }

    public function canBeDeleted(): bool
    {
        return in_array($this, [self::DRAFT, self::ARCHIVED]);
    }
}
