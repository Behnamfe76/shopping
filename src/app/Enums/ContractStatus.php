<?php

namespace Fereydooni\Shopping\App\Enums;

enum ContractStatus: string
{
    case DRAFT = 'draft';
    case ACTIVE = 'active';
    case EXPIRED = 'expired';
    case TERMINATED = 'terminated';
    case SUSPENDED = 'suspended';
    case PENDING_RENEWAL = 'pending_renewal';

    public function label(): string
    {
        return match ($this) {
            self::DRAFT => 'Draft',
            self::ACTIVE => 'Active',
            self::EXPIRED => 'Expired',
            self::TERMINATED => 'Terminated',
            self::SUSPENDED => 'Suspended',
            self::PENDING_RENEWAL => 'Pending Renewal',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::DRAFT => 'Contract is in draft status',
            self::ACTIVE => 'Contract is active and in force',
            self::EXPIRED => 'Contract has expired',
            self::TERMINATED => 'Contract has been terminated',
            self::SUSPENDED => 'Contract is temporarily suspended',
            self::PENDING_RENEWAL => 'Contract is pending renewal',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::DRAFT => 'gray',
            self::ACTIVE => 'green',
            self::EXPIRED => 'red',
            self::TERMINATED => 'red',
            self::SUSPENDED => 'yellow',
            self::PENDING_RENEWAL => 'orange',
        };
    }

    public function isActive(): bool
    {
        return $this === self::ACTIVE;
    }

    public function isExpired(): bool
    {
        return $this === self::EXPIRED;
    }

    public function isTerminated(): bool
    {
        return $this === self::TERMINATED;
    }

    public function isSuspended(): bool
    {
        return $this === self::SUSPENDED;
    }

    public function canBeModified(): bool
    {
        return in_array($this, [self::DRAFT, self::ACTIVE, self::SUSPENDED]);
    }

    public function canBeSigned(): bool
    {
        return $this === self::DRAFT;
    }

    public function canBeRenewed(): bool
    {
        return in_array($this, [self::ACTIVE, self::EXPIRED, self::PENDING_RENEWAL]);
    }

    public function canBeTerminated(): bool
    {
        return in_array($this, [self::ACTIVE, self::SUSPENDED]);
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function labels(): array
    {
        return [
            self::DRAFT->value => self::DRAFT->label(),
            self::ACTIVE->value => self::ACTIVE->label(),
            self::EXPIRED->value => self::EXPIRED->label(),
            self::TERMINATED->value => self::TERMINATED->label(),
            self::SUSPENDED->value => self::SUSPENDED->label(),
            self::PENDING_RENEWAL->value => self::PENDING_RENEWAL->label(),
        ];
    }
}
