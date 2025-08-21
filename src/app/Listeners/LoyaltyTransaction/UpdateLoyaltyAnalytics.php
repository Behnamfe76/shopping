<?php

namespace Fereydooni\Shopping\app\Listeners\LoyaltyTransaction;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Fereydooni\Shopping\app\Events\LoyaltyTransaction\LoyaltyTransactionCreated;
use Fereydooni\Shopping\app\Events\LoyaltyTransaction\LoyaltyTransactionUpdated;
use Fereydooni\Shopping\app\Events\LoyaltyTransaction\LoyaltyTransactionDeleted;
use Fereydooni\Shopping\app\Events\LoyaltyTransaction\LoyaltyTransactionReversed;
use Fereydooni\Shopping\app\Events\LoyaltyTransaction\LoyaltyTransactionExpired;
use Fereydooni\Shopping\app\Events\LoyaltyTransaction\LoyaltyPointsEarned;
use Fereydooni\Shopping\app\Events\LoyaltyTransaction\LoyaltyPointsRedeemed;
use Fereydooni\Shopping\app\Events\LoyaltyTransaction\LoyaltyPointsExpired;

class UpdateLoyaltyAnalytics implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Handle the event.
     */
    public function handle($event): void
    {
        $transaction = $event->transaction;
        $customerId = $transaction->customer_id;

        // Update customer loyalty analytics
        $this->updateCustomerAnalytics($customerId);
        
        // Update global loyalty analytics
        $this->updateGlobalAnalytics();
        
        // Log analytics update
        \Log::info('Loyalty analytics updated', [
            'transaction_id' => $transaction->id,
            'customer_id' => $customerId,
            'event_type' => get_class($event),
        ]);
    }

    /**
     * Update customer-specific analytics
     */
    protected function updateCustomerAnalytics(int $customerId): void
    {
        // This would typically update a customer analytics table or cache
        // For now, we'll just log the update
        \Log::info('Customer loyalty analytics updated', [
            'customer_id' => $customerId,
            'updated_at' => now(),
        ]);
    }

    /**
     * Update global loyalty analytics
     */
    protected function updateGlobalAnalytics(): void
    {
        // This would typically update global analytics tables or cache
        // For now, we'll just log the update
        \Log::info('Global loyalty analytics updated', [
            'updated_at' => now(),
        ]);
    }

    /**
     * Handle loyalty transaction created event
     */
    public function handleLoyaltyTransactionCreated(LoyaltyTransactionCreated $event): void
    {
        $this->handle($event);
    }

    /**
     * Handle loyalty transaction updated event
     */
    public function handleLoyaltyTransactionUpdated(LoyaltyTransactionUpdated $event): void
    {
        $this->handle($event);
    }

    /**
     * Handle loyalty transaction deleted event
     */
    public function handleLoyaltyTransactionDeleted(LoyaltyTransactionDeleted $event): void
    {
        $this->handle($event);
    }

    /**
     * Handle loyalty transaction reversed event
     */
    public function handleLoyaltyTransactionReversed(LoyaltyTransactionReversed $event): void
    {
        $this->handle($event);
    }

    /**
     * Handle loyalty transaction expired event
     */
    public function handleLoyaltyTransactionExpired(LoyaltyTransactionExpired $event): void
    {
        $this->handle($event);
    }

    /**
     * Handle loyalty points earned event
     */
    public function handleLoyaltyPointsEarned(LoyaltyPointsEarned $event): void
    {
        $this->handle($event);
    }

    /**
     * Handle loyalty points redeemed event
     */
    public function handleLoyaltyPointsRedeemed(LoyaltyPointsRedeemed $event): void
    {
        $this->handle($event);
    }

    /**
     * Handle loyalty points expired event
     */
    public function handleLoyaltyPointsExpired(LoyaltyPointsExpired $event): void
    {
        $this->handle($event);
    }
}
