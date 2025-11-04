<?php

namespace Fereydooni\Shopping\app\Enums;

enum ProductStatus: int
{
    case DRAFT = 1;
    case PUBLISHED = 2;
    case ARCHIVED = 3;
    case ACTIVE = 4;
    case INACTIVE = 5;

    public function toString(): string
    {
        return match($this) {
            self::DRAFT => 'draft',
            self::PUBLISHED => 'published',
            self::ARCHIVED => 'archived',
            self::ACTIVE => 'active',
            self::INACTIVE => 'inactive',
        };
    }

    public static function fromString(string $value): self
    {
        return match($value) {
            'draft' => self::DRAFT,
            'published' => self::PUBLISHED,
            'archived' => self::ARCHIVED,
            'active' => self::ACTIVE,
            'inactive' => self::INACTIVE,
            default => throw new \InvalidArgumentException("Invalid status: $value"),
        };
    }

    public function label(): string
    {
        return match($this) {
            self::DRAFT => 'Draft',
            self::PUBLISHED => 'Published',
            self::ARCHIVED => 'Archived',
            self::ACTIVE => 'Active',
            self::INACTIVE => 'Inactive',
        };
    }
}
