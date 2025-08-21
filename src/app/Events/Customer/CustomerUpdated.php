<?php

namespace Fereydooni\Shopping\app\Events\Customer;

use Fereydooni\Shopping\app\Models\Customer;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CustomerUpdated
{
    use Dispatchable, SerializesModels;

    public Customer $customer;
    public array $changes;

    /**
     * Create a new event instance.
     */
    public function __construct(Customer $customer, array $changes = [])
    {
        $this->customer = $customer;
        $this->changes = $changes;
    }
}
