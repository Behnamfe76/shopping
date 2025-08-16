<?php

namespace Fereydooni\Shopping\app\Events;

use Fereydooni\Shopping\app\Models\ProductReview;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ProductReviewVoted
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public ProductReview $review;
    public bool $isHelpful;
    public int $userId;

    /**
     * Create a new event instance.
     */
    public function __construct(ProductReview $review, bool $isHelpful, int $userId)
    {
        $this->review = $review;
        $this->isHelpful = $isHelpful;
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
