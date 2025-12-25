<?php

namespace Fereydooni\Shopping\app\Enums;

enum BenefitStatus: string
{
    case ENROLLED = 'enrolled';
    case PENDING = 'pending';
    case TERMINATED = 'terminated';
    case CANCELLED = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::ENROLLED => 'Enrolled',
            self::PENDING => 'Pending',
            self::TERMINATED => 'Terminated',
            self::CANCELLED => 'Cancelled',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::ENROLLED => 'Employee is actively enrolled and receiving benefits',
            self::PENDING => 'Enrollment is pending approval or processing',
            self::TERMINATED => 'Benefits have been terminated',
            self::CANCELLED => 'Enrollment was cancelled before activation',
        };
    }

    public function isActive(): bool
    {
        return $this === self::ENROLLED;
    }

    public function isPending(): bool
    {
        return $this === self::PENDING;
    }

    public function isTerminated(): bool
    {
        return $this === self::TERMINATED;
    }

    public function isCancelled(): bool
    {
        return $this === self::CANCELLED;
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function labels(): array
    {
        return array_reduce(self::cases(), function ($carry, $case) {
            $carry[$case->value] = $case->label();

            return $carry;
        }, []);
    }
}
