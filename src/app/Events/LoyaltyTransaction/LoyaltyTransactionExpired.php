<?php

namespace Fereydooni\Shopping\app\Events\LoyaltyTransaction;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Fereydooni\Shopping\app\Models\LoyaltyTransaction;

class LoyaltyTransactionExpired
{
    use Dispatchable, SerializesModels;

    public LoyaltyTransaction $transaction;

    /**
     * Create a new event instance.
     */
    public function __construct(LoyaltyTransaction $transaction)
    {
        $this->transaction = $transaction;
    }

    /**
     * Get the channels the event should broadcast on.
     */
    public function broadcastOn(): array
    {
        return [];
    }
}
