<?php

namespace Fereydooni\Shopping\app\Traits;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

trait HasFinancialOperations
{
    /**
     * Calculate order totals
     */
    public function calculateOrderTotals(array $items): array
    {
        $subtotal = 0;

        foreach ($items as $item) {
            $subtotal += ($item['quantity'] ?? 1) * ($item['unit_price'] ?? 0);
        }

        $taxAmount = $this->calculateTax($subtotal);
        $shippingAmount = $this->calculateShipping($subtotal);
        $discountAmount = $this->calculateDiscount($subtotal);
        $grandTotal = $subtotal + $taxAmount + $shippingAmount - $discountAmount;

        return [
            'subtotal' => $subtotal,
            'tax_amount' => $taxAmount,
            'shipping_amount' => $shippingAmount,
            'discount_amount' => $discountAmount,
            'grand_total' => $grandTotal,
        ];
    }

    /**
     * Apply discount to order
     */
    public function applyDiscount(object $item, float $discountAmount, string $discountType = 'fixed'): bool
    {
        $this->validateDiscount($discountAmount, $discountType);

        $currentDiscount = $item->discount_amount ?? 0;
        $newDiscount = $discountType === 'percentage'
            ? ($item->subtotal * $discountAmount / 100)
            : $discountAmount;

        $data = [
            'discount_amount' => $currentDiscount + $newDiscount,
        ];

        // Recalculate grand total
        $totals = $this->calculateOrderTotals([]);
        $data['grand_total'] = $totals['grand_total'];

        return $this->repository->update($item, $data);
    }

    /**
     * Remove discount from order
     */
    public function removeDiscount(object $item): bool
    {
        $data = [
            'discount_amount' => 0,
            'coupon_discount' => 0,
            'coupon_code' => null,
        ];

        // Recalculate grand total
        $totals = $this->calculateOrderTotals([]);
        $data['grand_total'] = $totals['grand_total'];

        return $this->repository->update($item, $data);
    }

    /**
     * Process payment
     */
    public function processPayment(object $item, string $paymentMethod, float $amount): bool
    {
        $this->validatePayment($amount);

        $data = [
            'payment_method' => $paymentMethod,
            'payment_status' => 'paid',
            'status' => 'paid',
        ];

        return $this->repository->update($item, $data);
    }

    /**
     * Process refund
     */
    public function processRefund(object $item, float $amount, string $reason = null): bool
    {
        $this->validateRefund($item, $amount);

        $data = [
            'payment_status' => 'refunded',
        ];

        if ($reason) {
            $data['notes'] = $this->addRefundNote($item, $amount, $reason);
        }

        return $this->repository->update($item, $data);
    }

    /**
     * Get total revenue
     */
    public function getTotalRevenue(): float
    {
        return $this->repository->getTotalRevenue();
    }

    /**
     * Get total revenue by date range
     */
    public function getTotalRevenueByDateRange(string $startDate, string $endDate): float
    {
        return $this->repository->getTotalRevenueByDateRange($startDate, $endDate);
    }

    /**
     * Calculate tax amount
     */
    protected function calculateTax(float $subtotal): float
    {
        // Default tax rate of 10%
        $taxRate = config('shopping.tax_rate', 0.10);
        return $subtotal * $taxRate;
    }

    /**
     * Calculate shipping amount
     */
    protected function calculateShipping(float $subtotal): float
    {
        // Free shipping for orders over $100
        $freeShippingThreshold = config('shopping.free_shipping_threshold', 100);
        $baseShippingCost = config('shopping.base_shipping_cost', 10);

        return $subtotal >= $freeShippingThreshold ? 0 : $baseShippingCost;
    }

    /**
     * Calculate discount amount
     */
    protected function calculateDiscount(float $subtotal): float
    {
        // Default discount of 0
        return 0;
    }

    /**
     * Validate discount
     */
    protected function validateDiscount(float $discountAmount, string $discountType): void
    {
        $rules = [
            'discount_amount' => 'required|numeric|min:0',
            'discount_type' => 'required|in:fixed,percentage',
        ];

        $data = [
            'discount_amount' => $discountAmount,
            'discount_type' => $discountType,
        ];

        $validator = Validator::make($data, $rules);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        if ($discountType === 'percentage' && $discountAmount > 100) {
            throw new ValidationException(
                Validator::make([], [])->errors()->add('discount_amount', 'Percentage discount cannot exceed 100%')
            );
        }
    }

    /**
     * Validate payment
     */
    protected function validatePayment(float $amount): void
    {
        $rules = [
            'amount' => 'required|numeric|min:0',
        ];

        $data = ['amount' => $amount];

        $validator = Validator::make($data, $rules);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }
    }

    /**
     * Validate refund
     */
    protected function validateRefund(object $item, float $amount): void
    {
        $rules = [
            'amount' => 'required|numeric|min:0',
        ];

        $data = ['amount' => $amount];

        $validator = Validator::make($data, $rules);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        if ($amount > $item->grand_total) {
            throw new ValidationException(
                Validator::make([], [])->errors()->add('amount', 'Refund amount cannot exceed order total')
            );
        }
    }

    /**
     * Add refund note
     */
    protected function addRefundNote(object $item, float $amount, string $reason): string
    {
        $currentNotes = $item->notes ? json_decode($item->notes, true) : [];

        $note = "Refund processed: {$amount} - {$reason}";

        $currentNotes[] = [
            'note' => $note,
            'type' => 'refund',
            'created_at' => now()->toISOString(),
        ];

        return json_encode($currentNotes);
    }

    /**
     * Convert currency
     */
    public function convertCurrency(float $amount, string $fromCurrency, string $toCurrency): float
    {
        if ($fromCurrency === $toCurrency) {
            return $amount;
        }

        // This would typically use an exchange rate service
        $exchangeRate = $this->getExchangeRate($fromCurrency, $toCurrency);
        return $amount * $exchangeRate;
    }

    /**
     * Get exchange rate
     */
    protected function getExchangeRate(string $fromCurrency, string $toCurrency): float
    {
        // Default exchange rate of 1.0
        // In a real application, this would fetch from an API
        return 1.0;
    }
}
