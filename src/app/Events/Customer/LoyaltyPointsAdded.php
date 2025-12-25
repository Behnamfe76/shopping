<?php

namespace Fereydooni\Shopping\app\Events\Customer;

use Fereydooni\Shopping\app\Models\Customer;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class LoyaltyPointsAdded
{
    use Dispatchable, SerializesModels;

    public Customer $customer;

    public int $points;

    public ?string $reason;

    public int $newBalance;

    /**
     * Create a new event instance.
     */
    public function __construct(Customer $customer, int $points, ?string $reason = null, int $newBalance = 0)
    {
        $this->customer = $customer;
        $this->points = $points;
        $this->reason = $reason;
        $this->newBalance = $newBalance;
    }
}
