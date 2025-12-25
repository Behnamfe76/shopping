<?php

namespace Fereydooni\Shopping\app\Listeners\Customer;

use Fereydooni\Shopping\app\Events\Customer\CustomerActivated;
use Fereydooni\Shopping\app\Events\Customer\CustomerCreated;
use Fereydooni\Shopping\app\Events\Customer\CustomerDeactivated;
use Fereydooni\Shopping\app\Events\Customer\CustomerDeleted;
use Fereydooni\Shopping\app\Events\Customer\CustomerSuspended;
use Fereydooni\Shopping\app\Events\Customer\CustomerUpdated;
use Fereydooni\Shopping\app\Events\Customer\LoyaltyPointsAdded;
use Fereydooni\Shopping\app\Events\Customer\LoyaltyPointsDeducted;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class LogCustomerActivity implements ShouldQueue
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
        \Log::info("Customer created: {$customer->id} - {$customer->email}");
    }

    /**
     * Handle customer updated event.
     */
    public function handleCustomerUpdated(CustomerUpdated $event): void
    {
        $customer = $event->customer;
        $changes = $event->changes;
        \Log::info("Customer updated: {$customer->id} - Changes: ".json_encode($changes));
    }

    /**
     * Handle customer deleted event.
     */
    public function handleCustomerDeleted(CustomerDeleted $event): void
    {
        $customer = $event->customer;
        \Log::info("Customer deleted: {$customer->id} - {$customer->email}");
    }

    /**
     * Handle customer activated event.
     */
    public function handleCustomerActivated(CustomerActivated $event): void
    {
        $customer = $event->customer;
        \Log::info("Customer activated: {$customer->id} - {$customer->email}");
    }

    /**
     * Handle customer deactivated event.
     */
    public function handleCustomerDeactivated(CustomerDeactivated $event): void
    {
        $customer = $event->customer;
        \Log::info("Customer deactivated: {$customer->id} - {$customer->email}");
    }

    /**
     * Handle customer suspended event.
     */
    public function handleCustomerSuspended(CustomerSuspended $event): void
    {
        $customer = $event->customer;
        $reason = $event->reason;
        \Log::info("Customer suspended: {$customer->id} - {$customer->email} - Reason: {$reason}");
    }

    /**
     * Handle loyalty points added event.
     */
    public function handleLoyaltyPointsAdded(LoyaltyPointsAdded $event): void
    {
        $customer = $event->customer;
        $points = $event->points;
        $reason = $event->reason;
        \Log::info("Loyalty points added: Customer {$customer->id} - +{$points} points - Reason: {$reason}");
    }

    /**
     * Handle loyalty points deducted event.
     */
    public function handleLoyaltyPointsDeducted(LoyaltyPointsDeducted $event): void
    {
        $customer = $event->customer;
        $points = $event->points;
        $reason = $event->reason;
        \Log::info("Loyalty points deducted: Customer {$customer->id} - -{$points} points - Reason: {$reason}");
    }
}
