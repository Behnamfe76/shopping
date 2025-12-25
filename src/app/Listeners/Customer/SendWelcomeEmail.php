<?php

namespace Fereydooni\Shopping\app\Listeners\Customer;

use Fereydooni\Shopping\app\Events\Customer\CustomerCreated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendWelcomeEmail implements ShouldQueue
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
     * Handle the event.
     */
    public function handle(CustomerCreated $event): void
    {
        $customer = $event->customer;

        // Send welcome email logic here
        // This would typically use Laravel's Mail facade
        // Mail::to($customer->email)->send(new WelcomeEmail($customer));

        // For now, we'll just log the action
        \Log::info('Welcome email sent to customer: '.$customer->email);
    }
}
