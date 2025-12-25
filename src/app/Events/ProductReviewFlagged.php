<?php

namespace Fereydooni\Shopping\app\Events;

use Fereydooni\Shopping\app\Models\ProductReview;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ProductReviewFlagged
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public ProductReview $review;

    public string $reason;

    public int $userId;

    /**
     * Create a new event instance.
     */
    public function __construct(ProductReview $review, string $reason, int $userId)
    {
        $this->review = $review;
        $this->reason = $reason;
        $this->userId = $userId;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            // Broadcast channels if needed
        ];
    }
}
