<?php

namespace Fereydooni\Shopping\app\Traits;

use Illuminate\Support\Facades\DB;
use Fereydooni\Shopping\app\Models\LoyaltyTransaction;
use Fereydooni\Shopping\app\Enums\LoyaltyTransactionStatus;

trait HasLoyaltyTransactionStatusManagement
{
    /**
     * Reverse a loyalty transaction
     */
    public function reverse(LoyaltyTransaction $transaction, string $reason = null): bool
    {
        return DB::transaction(function () use ($transaction, $reason) {
            // Update the transaction status
            $transaction->update([
                'status' => LoyaltyTransactionStatus::REVERSED,
                'reversed_at' => now(),
                'reversed_by' => auth()->id(),
                'reason' => $reason ?? $transaction->reason,
            ]);

            // Update customer loyalty points
            $this->updateCustomerLoyaltyPoints($transaction->customer_id, -$transaction->points);

            // Dispatch event
            event(new \Fereydooni\Shopping\app\Events\LoyaltyTransaction\LoyaltyTransactionReversed($transaction));

            return true;
        });
    }

    /**
     * Mark transaction as expired
     */
    public function markAsExpired(LoyaltyTransaction $transaction): bool
    {
        return DB::transaction(function () use ($transaction) {
            $transaction->update([
                'status' => LoyaltyTransactionStatus::EXPIRED,
            ]);

            // Update customer loyalty points
            $this->updateCustomerLoyaltyPoints($transaction->customer_id, -$transaction->points);

            // Dispatch event
            event(new \Fereydooni\Shopping\app\Events\LoyaltyTransaction\LoyaltyTransactionExpired($transaction));

            return true;
        });
    }

    /**
     * Mark transaction as processed
     */
    public function markAsProcessed(LoyaltyTransaction $transaction): bool
    {
        return DB::transaction(function () use ($transaction) {
            $transaction->update([
                'status' => LoyaltyTransactionStatus::COMPLETED,
            ]);

            // Update customer loyalty points
            $this->updateCustomerLoyaltyPoints($transaction->customer_id, $transaction->points);

            // Dispatch event
            event(new \Fereydooni\Shopping\app\Events\LoyaltyTransaction\LoyaltyTransactionUpdated($transaction));

            return true;
        });
    }

    /**
     * Mark transaction as failed
     */
    public function markAsFailed(LoyaltyTransaction $transaction, string $reason = null): bool
    {
        return DB::transaction(function () use ($transaction, $reason) {
            $transaction->update([
                'status' => LoyaltyTransactionStatus::FAILED,
                'reason' => $reason ?? $transaction->reason,
            ]);

            // Dispatch event
            event(new \Fereydooni\Shopping\app\Events\LoyaltyTransaction\LoyaltyTransactionUpdated($transaction));

            return true;
        });
    }

    /**
     * Validate transaction status change
     */
    public function validateStatusChange(LoyaltyTransaction $transaction, LoyaltyTransactionStatus $newStatus): bool
    {
        $currentStatus = $transaction->status;

        // Define allowed status transitions
        $allowedTransitions = [
            LoyaltyTransactionStatus::PENDING => [
                LoyaltyTransactionStatus::COMPLETED,
                LoyaltyTransactionStatus::FAILED,
            ],
            LoyaltyTransactionStatus::COMPLETED => [
                LoyaltyTransactionStatus::REVERSED,
                LoyaltyTransactionStatus::EXPIRED,
            ],
            LoyaltyTransactionStatus::FAILED => [
                LoyaltyTransactionStatus::PENDING,
            ],
            LoyaltyTransactionStatus::REVERSED => [],
            LoyaltyTransactionStatus::EXPIRED => [],
        ];

        return in_array($newStatus, $allowedTransitions[$currentStatus] ?? []);
    }

    /**
     * Get status change events
     */
    public function getStatusChangeEvents(LoyaltyTransactionStatus $oldStatus, LoyaltyTransactionStatus $newStatus): array
    {
        $events = [];

        if ($newStatus === LoyaltyTransactionStatus::COMPLETED) {
            $events[] = 'transaction_completed';
        }

        if ($newStatus === LoyaltyTransactionStatus::REVERSED) {
            $events[] = 'transaction_reversed';
        }

        if ($newStatus === LoyaltyTransactionStatus::EXPIRED) {
            $events[] = 'transaction_expired';
        }

        if ($newStatus === LoyaltyTransactionStatus::FAILED) {
            $events[] = 'transaction_failed';
        }

        return $events;
    }

    /**
     * Track status change
     */
    public function trackStatusChange(LoyaltyTransaction $transaction, LoyaltyTransactionStatus $oldStatus, LoyaltyTransactionStatus $newStatus): void
    {
        // Log the status change
        \Log::info('Loyalty transaction status changed', [
            'transaction_id' => $transaction->id,
            'customer_id' => $transaction->customer_id,
            'old_status' => $oldStatus->value,
            'new_status' => $newStatus->value,
            'changed_by' => auth()->id(),
            'changed_at' => now(),
        ]);

        // Update transaction metadata
        $metadata = $transaction->metadata ?? [];
        $metadata['status_changes'][] = [
            'from' => $oldStatus->value,
            'to' => $newStatus->value,
            'changed_by' => auth()->id(),
            'changed_at' => now()->toISOString(),
        ];

        $transaction->update(['metadata' => $metadata]);
    }

    /**
     * Update customer loyalty points
     */
    protected function updateCustomerLoyaltyPoints(int $customerId, int $pointsChange): void
    {
        $customer = \Fereydooni\Shopping\app\Models\Customer::find($customerId);
        
        if ($customer) {
            $customer->increment('loyalty_points', $pointsChange);
        }
    }

    /**
     * Check if transaction can be reversed
     */
    public function canReverse(LoyaltyTransaction $transaction): bool
    {
        return $transaction->status === LoyaltyTransactionStatus::COMPLETED 
            && !$transaction->is_expired
            && !$transaction->is_reversed;
    }

    /**
     * Check if transaction can be expired
     */
    public function canExpire(LoyaltyTransaction $transaction): bool
    {
        return $transaction->status === LoyaltyTransactionStatus::COMPLETED 
            && $transaction->is_expired;
    }

    /**
     * Check if transaction can be processed
     */
    public function canProcess(LoyaltyTransaction $transaction): bool
    {
        return $transaction->status === LoyaltyTransactionStatus::PENDING;
    }

    /**
     * Check if transaction can be failed
     */
    public function canFail(LoyaltyTransaction $transaction): bool
    {
        return in_array($transaction->status, [
            LoyaltyTransactionStatus::PENDING,
            LoyaltyTransactionStatus::COMPLETED,
        ]);
    }
}
