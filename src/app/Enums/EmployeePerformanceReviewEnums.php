<?php

namespace App\Enums;

enum EmployeePerformanceReviewStatus: string
{
    case DRAFT = 'draft';
    case SUBMITTED = 'submitted';
    case PENDING_APPROVAL = 'pending_approval';
    case APPROVED = 'approved';
    case REJECTED = 'rejected';
    case OVERDUE = 'overdue';

    public function label(): string
    {
        return match ($this) {
            self::DRAFT => 'Draft',
            self::SUBMITTED => 'Submitted',
            self::PENDING_APPROVAL => 'Pending Approval',
            self::APPROVED => 'Approved',
            self::REJECTED => 'Rejected',
            self::OVERDUE => 'Overdue',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::DRAFT => 'gray',
            self::SUBMITTED => 'blue',
            self::PENDING_APPROVAL => 'yellow',
            self::APPROVED => 'green',
            self::REJECTED => 'red',
            self::OVERDUE => 'orange',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::DRAFT => 'document',
            self::SUBMITTED => 'paper-airplane',
            self::PENDING_APPROVAL => 'clock',
            self::APPROVED => 'check-circle',
            self::REJECTED => 'x-circle',
            self::OVERDUE => 'exclamation-triangle',
        };
    }

    public function canTransitionTo(self $newStatus): bool
    {
        return match ($this) {
            self::DRAFT => in_array($newStatus, [self::SUBMITTED]),
            self::SUBMITTED => in_array($newStatus, [self::PENDING_APPROVAL, self::DRAFT]),
            self::PENDING_APPROVAL => in_array($newStatus, [self::APPROVED, self::REJECTED, self::DRAFT]),
            self::APPROVED => in_array($newStatus, [self::DRAFT]),
            self::REJECTED => in_array($newStatus, [self::DRAFT]),
            self::OVERDUE => in_array($newStatus, [self::DRAFT, self::SUBMITTED]),
        };
    }

    public function isEditable(): bool
    {
        return in_array($this, [self::DRAFT, self::REJECTED]);
    }

    public function isApprovalRequired(): bool
    {
        return $this === self::PENDING_APPROVAL;
    }

    public function isFinal(): bool
    {
        return in_array($this, [self::APPROVED, self::REJECTED]);
    }
}

enum EmployeePerformanceReviewRating: int
{
    case POOR = 1;
    case BELOW_AVERAGE = 2;
    case AVERAGE = 3;
    case ABOVE_AVERAGE = 4;
    case EXCELLENT = 5;

    public function label(): string
    {
        return match ($this) {
            self::POOR => 'Poor',
            self::BELOW_AVERAGE => 'Below Average',
            self::AVERAGE => 'Average',
            self::ABOVE_AVERAGE => 'Above Average',
            self::EXCELLENT => 'Excellent',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::POOR => 'Performance significantly below expectations',
            self::BELOW_AVERAGE => 'Performance below expectations',
            self::AVERAGE => 'Performance meets basic expectations',
            self::ABOVE_AVERAGE => 'Performance exceeds expectations',
            self::EXCELLENT => 'Performance significantly exceeds expectations',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::POOR => 'red',
            self::BELOW_AVERAGE => 'orange',
            self::AVERAGE => 'yellow',
            self::ABOVE_AVERAGE => 'blue',
            self::EXCELLENT => 'green',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::POOR => 'x-circle',
            self::BELOW_AVERAGE => 'exclamation-triangle',
            self::AVERAGE => 'minus-circle',
            self::ABOVE_AVERAGE => 'plus-circle',
            self::EXCELLENT => 'star',
        };
    }

    public function percentage(): float
    {
        return ($this->value / 5) * 100;
    }

    public function isPassing(): bool
    {
        return $this->value >= 3;
    }

    public function isOutstanding(): bool
    {
        return $this->value >= 4;
    }

    public static function fromFloat(float $rating): self
    {
        $rounded = round($rating);

        return match (true) {
            $rounded <= 1 => self::POOR,
            $rounded <= 2 => self::BELOW_AVERAGE,
            $rounded <= 3 => self::AVERAGE,
            $rounded <= 4 => self::ABOVE_AVERAGE,
            default => self::EXCELLENT,
        };
    }

    public static function getRange(): array
    {
        return [
            self::POOR->value => self::POOR->label(),
            self::BELOW_AVERAGE->value => self::BELOW_AVERAGE->label(),
            self::AVERAGE->value => self::AVERAGE->label(),
            self::ABOVE_AVERAGE->value => self::ABOVE_AVERAGE->label(),
            self::EXCELLENT->value => self::EXCELLENT->label(),
        ];
    }
}
