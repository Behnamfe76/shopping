<?php

namespace Fereydooni\Shopping\app\Events\Customer;

use Fereydooni\Shopping\app\Models\Customer;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CustomerSuspended
{
    use Dispatchable, SerializesModels;

    public Customer $customer;

    public ?string $reason;

    /**
     * Create a new event instance.
     */
    public function __construct(Customer $customer, ?string $reason = null)
    {
        $this->customer = $customer;
        $this->reason = $reason;
    }
}
