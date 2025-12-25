<?php

namespace Fereydooni\Shopping\app\Traits;

use Fereydooni\Shopping\app\Models\Customer;

trait HasCustomerLoyaltyManagement
{
    /**
     * Add loyalty points to customer
     */
    public function addLoyaltyPoints(Customer $customer, int $points, ?string $reason = null): bool
    {
        if ($points <= 0) {
            throw new \InvalidArgumentException('Points must be greater than zero.');
        }

        $newPoints = $customer->loyalty_points + $points;
        $data = ['loyalty_points' => $newPoints];

        if ($reason) {
            $data['notes'] = $customer->notes."\nLoyalty points added: +{$points} ({$reason})";
        }

        $result = $this->repository->update($customer, $data);

        if ($result) {
            $this->fireLoyaltyPointsAddedEvent($customer, $points, $reason, $newPoints);
        }

        return $result;
    }

    /**
     * Deduct loyalty points from customer
     */
    public function deductLoyaltyPoints(Customer $customer, int $points, ?string $reason = null): bool
    {
        if ($points <= 0) {
            throw new \InvalidArgumentException('Points must be greater than zero.');
        }

        if ($customer->loyalty_points < $points) {
            throw new \InvalidArgumentException('Insufficient loyalty points.');
        }

        $newPoints = max(0, $customer->loyalty_points - $points);
        $data = ['loyalty_points' => $newPoints];

        if ($reason) {
            $data['notes'] = $customer->notes."\nLoyalty points deducted: -{$points} ({$reason})";
        }

        $result = $this->repository->update($customer, $data);

        if ($result) {
            $this->fireLoyaltyPointsDeductedEvent($customer, $points, $reason, $newPoints);
        }

        return $result;
    }

    /**
     * Reset loyalty points for customer
     */
    public function resetLoyaltyPoints(Customer $customer): bool
    {
        $result = $this->repository->update($customer, ['loyalty_points' => 0]);

        if ($result) {
            $this->fireLoyaltyPointsResetEvent($customer);
        }

        return $result;
    }

    /**
     * Get loyalty balance for customer
     */
    public function getLoyaltyBalance(Customer $customer): int
    {
        return $customer->loyalty_points;
    }

    /**
     * Get total loyalty points across all customers
     */
    public function getTotalLoyaltyPoints(): int
    {
        return $this->repository->getTotalLoyaltyPoints();
    }

    /**
     * Get average loyalty points across all customers
     */
    public function getAverageLoyaltyPoints(): float
    {
        return $this->repository->getAverageLoyaltyPoints();
    }

    /**
     * Get customers by loyalty points range
     */
    public function getCustomersByLoyaltyPointsRange(int $minPoints, int $maxPoints): \Illuminate\Database\Eloquent\Collection
    {
        return $this->repository->findByLoyaltyPointsRange($minPoints, $maxPoints);
    }

    /**
     * Get customers by loyalty points range as DTOs
     */
    public function getCustomersByLoyaltyPointsRangeDTO(int $minPoints, int $maxPoints): \Illuminate\Database\Eloquent\Collection
    {
        return $this->repository->findByLoyaltyPointsRangeDTO($minPoints, $maxPoints);
    }

    /**
     * Get most loyal customers
     */
    public function getMostLoyal(int $limit = 10): \Illuminate\Database\Eloquent\Collection
    {
        return $this->repository->getMostLoyal($limit);
    }

    /**
     * Get most loyal customers as DTOs
     */
    public function getMostLoyalDTO(int $limit = 10): \Illuminate\Database\Eloquent\Collection
    {
        return $this->repository->getMostLoyalDTO($limit);
    }

    /**
     * Validate loyalty points operation
     */
    protected function validateLoyaltyPointsOperation(Customer $customer, int $points, string $operation): void
    {
        $validator = \Illuminate\Support\Facades\Validator::make([], []);

        if ($points <= 0) {
            $validator->errors()->add('points', 'Points must be greater than zero.');
        }

        if ($operation === 'deduct' && $customer->loyalty_points < $points) {
            $validator->errors()->add('points', 'Insufficient loyalty points.');
        }

        if ($validator->errors()->isNotEmpty()) {
            throw new \Illuminate\Validation\ValidationException($validator);
        }
    }

    /**
     * Fire loyalty points added event
     */
    protected function fireLoyaltyPointsAddedEvent(Customer $customer, int $points, ?string $reason = null, int $newBalance = 0): void
    {
        event(new \Fereydooni\Shopping\app\Events\Customer\LoyaltyPointsAdded($customer, $points, $reason, $newBalance));
    }

    /**
     * Fire loyalty points deducted event
     */
    protected function fireLoyaltyPointsDeductedEvent(Customer $customer, int $points, ?string $reason = null, int $newBalance = 0): void
    {
        event(new \Fereydooni\Shopping\app\Events\Customer\LoyaltyPointsDeducted($customer, $points, $reason, $newBalance));
    }

    /**
     * Fire loyalty points reset event
     */
    protected function fireLoyaltyPointsResetEvent(Customer $customer): void
    {
        // This could be a new event or reuse existing one
        event(new \Fereydooni\Shopping\app\Events\Customer\LoyaltyPointsDeducted($customer, $customer->loyalty_points, 'Reset', 0));
    }
}
