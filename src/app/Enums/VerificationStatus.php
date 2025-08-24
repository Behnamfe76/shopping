<?php

namespace Fereydooni\Shopping\App\Enums;

enum VerificationStatus: string
{
    case UNVERIFIED = 'unverified';
    case PENDING = 'pending';
    case VERIFIED = 'verified';
    case REJECTED = 'rejected';
    case REQUIRES_UPDATE = 'requires_update';

    /**
     * Get the display name for the verification status.
     */
    public function label(): string
    {
        return match($this) {
            self::UNVERIFIED => 'Unverified',
            self::PENDING => 'Pending',
            self::VERIFIED => 'Verified',
            self::REJECTED => 'Rejected',
            self::REQUIRES_UPDATE => 'Requires Update',
        };
    }

    /**
     * Get the description for the verification status.
     */
    public function description(): string
    {
        return match($this) {
            self::UNVERIFIED => 'Certification has not been submitted for verification',
            self::PENDING => 'Certification is pending verification review',
            self::VERIFIED => 'Certification has been verified and approved',
            self::REJECTED => 'Certification verification was rejected',
            self::REQUIRES_UPDATE => 'Certification requires updates before verification',
        };
    }

    /**
     * Get the color class for the verification status.
     */
    public function color(): string
    {
        return match($this) {
            self::UNVERIFIED => 'secondary',
            self::PENDING => 'warning',
            self::VERIFIED => 'success',
            self::REJECTED => 'danger',
            self::REQUIRES_UPDATE => 'info',
        };
    }

    /**
     * Check if the verification status is unverified.
     */
    public function isUnverified(): bool
    {
        return $this === self::UNVERIFIED;
    }

    /**
     * Check if the verification status is pending.
     */
    public function isPending(): bool
    {
        return $this === self::PENDING;
    }

    /**
     * Check if the verification status is verified.
     */
    public function isVerified(): bool
    {
        return $this === self::VERIFIED;
    }

    /**
     * Check if the verification status is rejected.
     */
    public function isRejected(): bool
    {
        return $this === self::REJECTED;
    }

    /**
     * Check if the verification status requires update.
     */
    public function requiresUpdate(): bool
    {
        return $this === self::REQUIRES_UPDATE;
    }

    /**
     * Check if the verification status is in a final state.
     */
    public function isFinal(): bool
    {
        return in_array($this, [self::VERIFIED, self::REJECTED]);
    }

    /**
     * Check if the verification status can be processed.
     */
    public function canProcess(): bool
    {
        return in_array($this, [self::PENDING, self::REQUIRES_UPDATE]);
    }

    /**
     * Get all verification statuses as an array.
     */
    public static function toArray(): array
    {
        return array_map(fn($case) => [
            'value' => $case->value,
            'label' => $case->label(),
            'description' => $case->description(),
            'color' => $case->color(),
        ], self::cases());
    }
}
