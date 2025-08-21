<?php

namespace Fereydooni\Shopping\app\Listeners\Customer;

use Fereydooni\Shopping\app\Events\Customer\LoyaltyPointsAdded;
use Fereydooni\Shopping\app\Events\Customer\LoyaltyPointsDeducted;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class UpdateLoyaltyProgram implements ShouldQueue
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
     * Handle loyalty points added event.
     */
    public function handleLoyaltyPointsAdded(LoyaltyPointsAdded $event): void
    {
        $customer = $event->customer;
        $points = $event->points;
        $reason = $event->reason;
        $newBalance = $event->newBalance;

        // Update loyalty program records
        // This could involve creating loyalty transaction records, updating tiers, etc.
        \Log::info("Loyalty points added for customer {$customer->id}: +{$points} points. New balance: {$newBalance}");

        // Check if customer should be upgraded to a new tier
        $this->checkTierUpgrade($customer, $newBalance);
    }

    /**
     * Handle loyalty points deducted event.
     */
    public function handleLoyaltyPointsDeducted(LoyaltyPointsDeducted $event): void
    {
        $customer = $event->customer;
        $points = $event->points;
        $reason = $event->reason;
        $newBalance = $event->newBalance;

        // Update loyalty program records
        \Log::info("Loyalty points deducted for customer {$customer->id}: -{$points} points. New balance: {$newBalance}");

        // Check if customer should be downgraded to a lower tier
        $this->checkTierDowngrade($customer, $newBalance);
    }

    /**
     * Check if customer should be upgraded to a new tier.
     */
    private function checkTierUpgrade($customer, int $newBalance): void
    {
        // Implement tier upgrade logic
        // This would check if the new balance qualifies for a higher tier
        \Log::info("Checking tier upgrade for customer {$customer->id} with balance {$newBalance}");
    }

    /**
     * Check if customer should be downgraded to a lower tier.
     */
    private function checkTierDowngrade($customer, int $newBalance): void
    {
        // Implement tier downgrade logic
        // This would check if the new balance requires a lower tier
        \Log::info("Checking tier downgrade for customer {$customer->id} with balance {$newBalance}");
    }
}
