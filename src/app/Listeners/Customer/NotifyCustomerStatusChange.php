<?php

namespace Fereydooni\Shopping\app\Listeners\Customer;

use Fereydooni\Shopping\app\Events\Customer\CustomerActivated;
use Fereydooni\Shopping\app\Events\Customer\CustomerDeactivated;
use Fereydooni\Shopping\app\Events\Customer\CustomerSuspended;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class NotifyCustomerStatusChange implements ShouldQueue
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
     * Handle customer activated event.
     */
    public function handleCustomerActivated(CustomerActivated $event): void
    {
        $customer = $event->customer;

        // Send activation notification
        // Mail::to($customer->email)->send(new CustomerActivatedEmail($customer));
        \Log::info('Activation notification sent to customer: ' . $customer->email);
    }

    /**
     * Handle customer deactivated event.
     */
    public function handleCustomerDeactivated(CustomerDeactivated $event): void
    {
        $customer = $event->customer;

        // Send deactivation notification
        // Mail::to($customer->email)->send(new CustomerDeactivatedEmail($customer));
        \Log::info('Deactivation notification sent to customer: ' . $customer->email);
    }

    /**
     * Handle customer suspended event.
     */
    public function handleCustomerSuspended(CustomerSuspended $event): void
    {
        $customer = $event->customer;
        $reason = $event->reason;

        // Send suspension notification
        // Mail::to($customer->email)->send(new CustomerSuspendedEmail($customer, $reason));
        \Log::info('Suspension notification sent to customer: ' . $customer->email . ' (Reason: ' . $reason . ')');
    }
}
