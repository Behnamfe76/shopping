<?php

namespace Fereydooni\Shopping\app\Events;

use Fereydooni\Shopping\app\Models\EmployeePerformanceReview;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Event fired when a performance review is created
 */
class EmployeePerformanceReviewCreated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public EmployeePerformanceReview $review;

    public function __construct(EmployeePerformanceReview $review)
    {
        $this->review = $review;
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('employee-performance-reviews'),
        ];
    }
}

/**
 * Event fired when a performance review is updated
 */
class EmployeePerformanceReviewUpdated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public EmployeePerformanceReview $review;
    public array $changes;

    public function __construct(EmployeePerformanceReview $review, array $changes = [])
    {
        $this->review = $review;
        $this->changes = $changes;
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('employee-performance-reviews'),
        ];
    }
}

/**
 * Event fired when a performance review is deleted
 */
class EmployeePerformanceReviewDeleted
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public EmployeePerformanceReview $review;

    public function __construct(EmployeePerformanceReview $review)
    {
        $this->review = $review;
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('employee-performance-reviews'),
        ];
    }
}

/**
 * Event fired when a performance review is submitted for approval
 */
class EmployeePerformanceReviewSubmitted
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public EmployeePerformanceReview $review;

    public function __construct(EmployeePerformanceReview $review)
    {
        $this->review = $review;
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('employee-performance-reviews'),
        ];
    }
}

/**
 * Event fired when a performance review is approved
 */
class EmployeePerformanceReviewApproved
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public EmployeePerformanceReview $review;
    public int $approvedBy;

    public function __construct(EmployeePerformanceReview $review, int $approvedBy)
    {
        $this->review = $review;
        $this->approvedBy = $approvedBy;
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('employee-performance-reviews'),
        ];
    }
}

/**
 * Event fired when a performance review is rejected
 */
class EmployeePerformanceReviewRejected
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public EmployeePerformanceReview $review;
    public int $rejectedBy;
    public ?string $reason;

    public function __construct(EmployeePerformanceReview $review, int $rejectedBy, ?string $reason = null)
    {
        $this->review = $review;
        $this->rejectedBy = $rejectedBy;
        $this->reason = $reason;
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('employee-performance-reviews'),
        ];
    }
}

/**
 * Event fired when a reviewer is assigned to a performance review
 */
class EmployeePerformanceReviewAssigned
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public EmployeePerformanceReview $review;
    public int $reviewerId;

    public function __construct(EmployeePerformanceReview $review, int $reviewerId)
    {
        $this->review = $review;
        $this->reviewerId = $reviewerId;
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('employee-performance-reviews'),
        ];
    }
}

/**
 * Event fired when a performance review is scheduled
 */
class EmployeePerformanceReviewScheduled
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public EmployeePerformanceReview $review;
    public string $reviewDate;

    public function __construct(EmployeePerformanceReview $review, string $reviewDate)
    {
        $this->review = $review;
        $this->reviewDate = $reviewDate;
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('employee-performance-reviews'),
        ];
    }
}

/**
 * Event fired when a performance review becomes overdue
 */
class EmployeePerformanceReviewOverdue
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public EmployeePerformanceReview $review;

    public function __construct(EmployeePerformanceReview $review)
    {
        $this->review = $review;
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('employee-performance-reviews'),
        ];
    }
}

/**
 * Event fired when a review reminder is sent
 */
class EmployeePerformanceReviewReminderSent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public EmployeePerformanceReview $review;
    public string $reminderType;

    public function __construct(EmployeePerformanceReview $review, string $reminderType = 'general')
    {
        $this->review = $review;
        $this->reminderType = $reminderType;
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('employee-performance-reviews'),
        ];
    }
}
