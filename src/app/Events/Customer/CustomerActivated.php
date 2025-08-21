<?php

namespace Fereydooni\Shopping\app\Events\Customer;

use Fereydooni\Shopping\app\Models\Customer;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CustomerActivated
{
    use Dispatchable, SerializesModels;

    public Customer $customer;

    /**
     * Create a new event instance.
     */
    public function __construct(Customer $customer)
    {
        $this->customer = $customer;
    }
}
