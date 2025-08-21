<?php

namespace Fereydooni\Shopping\app\Listeners\Customer;

use Fereydooni\Shopping\app\Events\Customer\CustomerCreated;
use Fereydooni\Shopping\app\Events\Customer\CustomerUpdated;
use Fereydooni\Shopping\app\Events\Customer\CustomerActivated;
use Fereydooni\Shopping\app\Events\Customer\CustomerDeactivated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class UpdateCustomerAnalytics implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle customer created event.
     */
    public function handleCustomerCreated(CustomerCreated $event): void
    {
        $customer = $event->customer;

        // Update analytics for new customer
        // This could involve updating counters, creating analytics records, etc.
        \Log::info('Customer analytics updated for new customer: ' . $customer->id);
    }

    /**
     * Handle customer updated event.
     */
    public function handleCustomerUpdated(CustomerUpdated $event): void
    {
        $customer = $event->customer;
        $changes = $event->changes;

        // Update analytics based on changes
        // This could involve updating specific metrics based on what changed
        \Log::info('Customer analytics updated for customer: ' . $customer->id);
    }

    /**
     * Handle customer activated event.
     */
    public function handleCustomerActivated(CustomerActivated $event): void
    {
        $customer = $event->customer;

        // Update analytics for activated customer
        \Log::info('Customer analytics updated for activated customer: ' . $customer->id);
    }

    /**
     * Handle customer deactivated event.
     */
    public function handleCustomerDeactivated(CustomerDeactivated $event): void
    {
        $customer = $event->customer;

        // Update analytics for deactivated customer
        \Log::info('Customer analytics updated for deactivated customer: ' . $customer->id);
    }
}
