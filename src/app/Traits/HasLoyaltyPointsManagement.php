<?php

namespace Fereydooni\Shopping\app\Traits;

use Fereydooni\Shopping\app\Enums\LoyaltyReferenceType;
use Fereydooni\Shopping\app\Enums\LoyaltyTransactionStatus;
use Fereydooni\Shopping\app\Enums\LoyaltyTransactionType;
use Fereydooni\Shopping\app\Models\LoyaltyTransaction;
use Illuminate\Support\Facades\DB;

trait HasLoyaltyPointsManagement
{
    /**
     * Add loyalty points to customer
     */
    public function addPoints(int $customerId, int $points, ?string $reason = null, array $metadata = []): LoyaltyTransaction
    {
        return DB::transaction(function () use ($customerId, $points, $reason, $metadata) {
            $transaction = $this->create([
                'customer_id' => $customerId,
                'user_id' => auth()->id(),
                'transaction_type' => LoyaltyTransactionType::EARNED,
                'points' => $points,
                'points_value' => $this->calculatePointsValue($points),
                'reference_type' => LoyaltyReferenceType::MANUAL,
                'description' => $reason ?? 'Points added manually',
                'reason' => $reason,
                'status' => LoyaltyTransactionStatus::COMPLETED,
                'metadata' => $metadata,
            ]);

            // Update customer loyalty points
            $this->updateCustomerLoyaltyPoints($customerId, $points);

            // Dispatch event
            event(new \Fereydooni\Shopping\app\Events\LoyaltyTransaction\LoyaltyPointsEarned($transaction));

            return $transaction;
        });
    }

    /**
     * Deduct loyalty points from customer
     */
    public function deductPoints(int $customerId, int $points, ?string $reason = null, array $metadata = []): LoyaltyTransaction
    {
        return DB::transaction(function () use ($customerId, $points, $reason, $metadata) {
            // Check if customer has enough points
            $currentBalance = $this->calculateBalance($customerId);
            if ($currentBalance < $points) {
                throw new \Exception("Insufficient loyalty points. Current balance: {$currentBalance}, Required: {$points}");
            }

            $transaction = $this->create([
                'customer_id' => $customerId,
                'user_id' => auth()->id(),
                'transaction_type' => LoyaltyTransactionType::REDEEMED,
                'points' => $points,
                'points_value' => $this->calculatePointsValue($points),
                'reference_type' => LoyaltyReferenceType::MANUAL,
                'description' => $reason ?? 'Points deducted manually',
                'reason' => $reason,
                'status' => LoyaltyTransactionStatus::COMPLETED,
                'metadata' => $metadata,
            ]);

            // Update customer loyalty points
            $this->updateCustomerLoyaltyPoints($customerId, -$points);

            // Dispatch event
            event(new \Fereydooni\Shopping\app\Events\LoyaltyTransaction\LoyaltyPointsRedeemed($transaction));

            return $transaction;
        });
    }

    /**
     * Calculate loyalty balance for customer
     */
    public function calculateBalance(int $customerId): int
    {
        $earned = $this->model::byCustomer($customerId)
            ->earned()
            ->where('status', LoyaltyTransactionStatus::COMPLETED)
            ->sum('points');

        $redeemed = $this->model::byCustomer($customerId)
            ->redeemed()
            ->where('status', LoyaltyTransactionStatus::COMPLETED)
            ->sum('points');

        $expired = $this->model::byCustomer($customerId)
            ->where('status', LoyaltyTransactionStatus::EXPIRED)
            ->sum('points');

        $reversed = $this->model::byCustomer($customerId)
            ->where('status', LoyaltyTransactionStatus::REVERSED)
            ->sum('points');

        return $earned - $redeemed - $expired - $reversed;
    }

    /**
     * Calculate loyalty balance value for customer
     */
    public function calculateBalanceValue(int $customerId): float
    {
        $balance = $this->calculateBalance($customerId);

        return $this->calculatePointsValue($balance);
    }

    /**
     * Check loyalty expiration for customer
     */
    public function checkExpiration(int $customerId): int
    {
        return $this->model::byCustomer($customerId)
            ->where('status', LoyaltyTransactionStatus::COMPLETED)
            ->where('expires_at', '<=', now())
            ->sum('points');
    }

    /**
     * Calculate loyalty tier for customer
     */
    public function calculateTier(int $customerId): string
    {
        $balance = $this->calculateBalance($customerId);
        $totalSpent = $this->getCustomerTotalSpent($customerId);

        if ($totalSpent >= 10000 || $balance >= 10000) {
            return 'platinum';
        } elseif ($totalSpent >= 5000 || $balance >= 5000) {
            return 'gold';
        } elseif ($totalSpent >= 1000 || $balance >= 1000) {
            return 'silver';
        } else {
            return 'bronze';
        }
    }

    /**
     * Validate loyalty transaction
     */
    public function validateTransaction(array $data): bool
    {
        $rules = [
            'customer_id' => 'required|integer|exists:customers,id',
            'user_id' => 'required|integer|exists:users,id',
            'transaction_type' => 'required|string|in:earned,redeemed,expired,reversed,bonus,adjustment',
            'points' => 'required|integer|min:1',
            'points_value' => 'required|numeric|min:0',
            'reference_type' => 'required|string|in:order,product,campaign,manual,system',
            'status' => 'required|string|in:pending,completed,failed,reversed,expired',
        ];

        $validator = validator($data, $rules);

        if ($validator->fails()) {
            return false;
        }

        // Additional business logic validation
        if ($data['transaction_type'] === 'redeemed') {
            $currentBalance = $this->calculateBalance($data['customer_id']);
            if ($currentBalance < $data['points']) {
                return false;
            }
        }

        return true;
    }

    /**
     * Get customer total spent
     */
    protected function getCustomerTotalSpent(int $customerId): float
    {
        $customer = \Fereydooni\Shopping\app\Models\Customer::find($customerId);

        return $customer ? $customer->total_spent : 0;
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
     * Calculate points value
     */
    public function calculatePointsValue(int $points): float
    {
        return $points * 0.01; // 1 point = $0.01
    }

    /**
     * Get loyalty points events
     */
    public function getLoyaltyPointsEvents(int $customerId): array
    {
        $transactions = $this->model::byCustomer($customerId)
            ->with(['customer', 'user'])
            ->latest()
            ->get();

        return $transactions->map(function ($transaction) {
            return [
                'id' => $transaction->id,
                'type' => $transaction->transaction_type->value,
                'points' => $transaction->points,
                'points_value' => $transaction->points_value,
                'status' => $transaction->status->value,
                'description' => $transaction->description,
                'created_at' => $transaction->created_at->toISOString(),
                'processed_by' => $transaction->user ? $transaction->user->name : null,
            ];
        })->toArray();
    }

    /**
     * Track loyalty balance
     */
    public function trackLoyaltyBalance(int $customerId): array
    {
        $balance = $this->calculateBalance($customerId);
        $balanceValue = $this->calculateBalanceValue($customerId);
        $tier = $this->calculateTier($customerId);
        $expiringPoints = $this->checkExpiration($customerId);

        return [
            'customer_id' => $customerId,
            'current_balance' => $balance,
            'current_balance_value' => $balanceValue,
            'tier' => $tier,
            'expiring_points' => $expiringPoints,
            'last_updated' => now()->toISOString(),
        ];
    }
}
