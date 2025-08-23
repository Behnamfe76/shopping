<?php

namespace Fereydooni\Shopping\Enums;

enum TrainingStatus: string
{
    case NOT_STARTED = 'not_started';
    case IN_PROGRESS = 'in_progress';
    case COMPLETED = 'completed';
    case FAILED = 'failed';
    case CANCELLED = 'cancelled';

    public function label(): string
    {
        return match($this) {
            self::NOT_STARTED => 'Not Started',
            self::IN_PROGRESS => 'In Progress',
            self::COMPLETED => 'Completed',
            self::FAILED => 'Failed',
            self::CANCELLED => 'Cancelled',
        };
    }

    public function description(): string
    {
        return match($this) {
            self::NOT_STARTED => 'Training has been assigned but not yet started',
            self::IN_PROGRESS => 'Training is currently being undertaken',
            self::COMPLETED => 'Training has been successfully completed',
            self::FAILED => 'Training was attempted but not completed successfully',
            self::CANCELLED => 'Training was cancelled before completion',
        };
    }

    public function isActive(): bool
    {
        return in_array($this, [self::NOT_STARTED, self::IN_PROGRESS]);
    }

    public function isCompleted(): bool
    {
        return $this === self::COMPLETED;
    }

    public function isFailed(): bool
    {
        return $this === self::FAILED;
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
        return array_combine(
            self::values(),
            array_map(fn($case) => $case->label(), self::cases())
        );
    }
}
