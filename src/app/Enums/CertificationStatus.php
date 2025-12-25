<?php

namespace Fereydooni\Shopping\App\Enums;

enum CertificationStatus: string
{
    case ACTIVE = 'active';
    case EXPIRED = 'expired';
    case SUSPENDED = 'suspended';
    case REVOKED = 'revoked';
    case PENDING_RENEWAL = 'pending_renewal';

    /**
     * Get the display name for the certification status.
     */
    public function label(): string
    {
        return match ($this) {
            self::ACTIVE => 'Active',
            self::EXPIRED => 'Expired',
            self::SUSPENDED => 'Suspended',
            self::REVOKED => 'Revoked',
            self::PENDING_RENEWAL => 'Pending Renewal',
        };
    }

    /**
     * Get the description for the certification status.
     */
    public function description(): string
    {
        return match ($this) {
            self::ACTIVE => 'Certification is currently valid and active',
            self::EXPIRED => 'Certification has expired and is no longer valid',
            self::SUSPENDED => 'Certification is temporarily suspended',
            self::REVOKED => 'Certification has been permanently revoked',
            self::PENDING_RENEWAL => 'Certification is pending renewal',
        };
    }

    /**
     * Get the color class for the certification status.
     */
    public function color(): string
    {
        return match ($this) {
            self::ACTIVE => 'success',
            self::EXPIRED => 'danger',
            self::SUSPENDED => 'warning',
            self::REVOKED => 'danger',
            self::PENDING_RENEWAL => 'info',
        };
    }

    /**
     * Check if the certification status is active.
     */
    public function isActive(): bool
    {
        return $this === self::ACTIVE;
    }

    /**
     * Check if the certification status is expired.
     */
    public function isExpired(): bool
    {
        return $this === self::EXPIRED;
    }

    /**
     * Check if the certification status is suspended.
     */
    public function isSuspended(): bool
    {
        return $this === self::SUSPENDED;
    }

    /**
     * Check if the certification status is revoked.
     */
    public function isRevoked(): bool
    {
        return $this === self::REVOKED;
    }

    /**
     * Check if the certification status is pending renewal.
     */
    public function isPendingRenewal(): bool
    {
        return $this === self::PENDING_RENEWAL;
    }

    /**
     * Check if the certification status is valid for use.
     */
    public function isValid(): bool
    {
        return in_array($this, [self::ACTIVE, self::PENDING_RENEWAL]);
    }

    /**
     * Get all certification statuses as an array.
     */
    public static function toArray(): array
    {
        return array_map(fn ($case) => [
            'value' => $case->value,
            'label' => $case->label(),
            'description' => $case->description(),
            'color' => $case->color(),
        ], self::cases());
    }
}
