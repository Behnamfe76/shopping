<?php

namespace Fereydooni\Shopping\app\Traits;

use Fereydooni\Shopping\app\Enums\TransactionStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

trait HasTransactionOperations
{
    /**
     * Mark transaction as successful
     */
    public function markTransactionAsSuccess(Model $transaction, array $responseData = []): bool
    {
        return $transaction->update([
            'status' => TransactionStatus::SUCCESS,
            'payment_date' => now(),
            'response_data' => array_merge($transaction->response_data ?? [], $responseData)
        ]);
    }

    /**
     * Mark transaction as failed
     */
    public function markTransactionAsFailed(Model $transaction, array $responseData = []): bool
    {
        return $transaction->update([
            'status' => TransactionStatus::FAILED,
            'response_data' => array_merge($transaction->response_data ?? [], $responseData)
        ]);
    }

    /**
     * Mark transaction as refunded
     */
    public function markTransactionAsRefunded(Model $transaction, array $responseData = []): bool
    {
        return $transaction->update([
            'status' => TransactionStatus::REFUNDED,
            'response_data' => array_merge($transaction->response_data ?? [], $responseData)
        ]);
    }

    /**
     * Mark transaction as initiated
     */
    public function markTransactionAsInitiated(Model $transaction): bool
    {
        return $transaction->update([
            'status' => TransactionStatus::INITIATED,
            'payment_date' => null
        ]);
    }

    /**
     * Generate unique transaction ID
     */
    public function generateTransactionId(string $gateway, string $prefix = 'TXN'): string
    {
        $timestamp = now()->format('YmdHis');
        $random = Str::random(8);
        return strtoupper("{$prefix}_{$gateway}_{$timestamp}_{$random}");
    }

    /**
     * Validate transaction amount
     */
    public function validateTransactionAmount(float $amount, string $currency): bool
    {
        if ($amount <= 0) {
            return false;
        }

        // Currency-specific validations
        $currencyValidations = [
            'USD' => ['min' => 0.01, 'max' => 999999.99],
            'EUR' => ['min' => 0.01, 'max' => 999999.99],
            'GBP' => ['min' => 0.01, 'max' => 999999.99],
        ];

        if (isset($currencyValidations[$currency])) {
            $validation = $currencyValidations[$currency];
            return $amount >= $validation['min'] && $amount <= $validation['max'];
        }

        return true;
    }

    /**
     * Format transaction amount
     */
    public function formatTransactionAmount(float $amount, string $currency): string
    {
        $formattedAmount = number_format($amount, 2);
        return "{$formattedAmount} " . strtoupper($currency);
    }

    /**
     * Calculate transaction fees
     */
    public function calculateTransactionFees(float $amount, string $gateway): array
    {
        $feeRates = [
            'stripe' => ['percentage' => 2.9, 'fixed' => 0.30],
            'paypal' => ['percentage' => 2.9, 'fixed' => 0.30],
            'square' => ['percentage' => 2.6, 'fixed' => 0.10],
            'braintree' => ['percentage' => 2.9, 'fixed' => 0.30],
        ];

        $rates = $feeRates[strtolower($gateway)] ?? ['percentage' => 3.0, 'fixed' => 0.30];

        $percentageFee = ($amount * $rates['percentage']) / 100;
        $totalFee = $percentageFee + $rates['fixed'];
        $netAmount = $amount - $totalFee;

        return [
            'gross_amount' => $amount,
            'percentage_fee' => $percentageFee,
            'fixed_fee' => $rates['fixed'],
            'total_fee' => $totalFee,
            'net_amount' => $netAmount,
            'fee_percentage' => $rates['percentage'],
        ];
    }

    /**
     * Validate gateway response
     */
    public function validateGatewayResponse(array $responseData): bool
    {
        $requiredFields = ['status', 'transaction_id'];

        foreach ($requiredFields as $field) {
            if (!isset($responseData[$field])) {
                return false;
            }
        }

        return true;
    }

    /**
     * Process gateway response
     */
    public function processGatewayResponse(Model $transaction, array $responseData): bool
    {
        if (!$this->validateGatewayResponse($responseData)) {
            return false;
        }

        $status = strtolower($responseData['status']);

        switch ($status) {
            case 'success':
            case 'completed':
            case 'approved':
                return $this->markTransactionAsSuccess($transaction, $responseData);

            case 'failed':
            case 'declined':
            case 'rejected':
                return $this->markTransactionAsFailed($transaction, $responseData);

            case 'pending':
            case 'processing':
                return $this->markTransactionAsInitiated($transaction);

            default:
                return false;
        }
    }

    /**
     * Check if transaction can be refunded
     */
    public function canRefundTransaction(Model $transaction): bool
    {
        return $transaction->status === TransactionStatus::SUCCESS;
    }

    /**
     * Check if transaction can be processed
     */
    public function canProcessTransaction(Model $transaction): bool
    {
        return $transaction->status === TransactionStatus::INITIATED;
    }

    /**
     * Get transaction status label
     */
    public function getTransactionStatusLabel(Model $transaction): string
    {
        return $transaction->status->label();
    }

    /**
     * Get gateway display name
     */
    public function getGatewayDisplayName(string $gateway): string
    {
        $gatewayNames = [
            'stripe' => 'Stripe',
            'paypal' => 'PayPal',
            'square' => 'Square',
            'braintree' => 'Braintree',
            'adyen' => 'Adyen',
            'worldpay' => 'Worldpay',
        ];

        return $gatewayNames[strtolower($gateway)] ?? ucfirst(str_replace('_', ' ', $gateway));
    }

    /**
     * Get supported gateways
     */
    public function getSupportedGateways(): array
    {
        return [
            'stripe' => 'Stripe',
            'paypal' => 'PayPal',
            'square' => 'Square',
            'braintree' => 'Braintree',
            'adyen' => 'Adyen',
            'worldpay' => 'Worldpay',
        ];
    }

    /**
     * Get supported currencies
     */
    public function getSupportedCurrencies(): array
    {
        return [
            'USD' => 'US Dollar',
            'EUR' => 'Euro',
            'GBP' => 'British Pound',
            'CAD' => 'Canadian Dollar',
            'AUD' => 'Australian Dollar',
            'JPY' => 'Japanese Yen',
        ];
    }

    /**
     * Validate currency
     */
    public function validateCurrency(string $currency): bool
    {
        $supportedCurrencies = array_keys($this->getSupportedCurrencies());
        return in_array(strtoupper($currency), $supportedCurrencies);
    }

    /**
     * Get transaction summary
     */
    public function getTransactionSummary(Model $transaction): array
    {
        return [
            'id' => $transaction->id,
            'transaction_id' => $transaction->transaction_id,
            'amount' => $this->formatTransactionAmount($transaction->amount, $transaction->currency),
            'status' => $this->getTransactionStatusLabel($transaction),
            'gateway' => $this->getGatewayDisplayName($transaction->gateway),
            'payment_date' => $transaction->payment_date?->format('Y-m-d H:i:s'),
            'can_refund' => $this->canRefundTransaction($transaction),
            'can_process' => $this->canProcessTransaction($transaction),
        ];
    }

    /**
     * Update payment date
     */
    public function updatePaymentDate(Model $transaction, ?string $paymentDate = null): bool
    {
        $date = $paymentDate ? now()->parse($paymentDate) : now();
        return $transaction->update(['payment_date' => $date]);
    }

    /**
     * Add response data to transaction
     */
    public function addResponseData(Model $transaction, array $responseData): bool
    {
        $existingData = $transaction->response_data ?? [];
        $mergedData = array_merge($existingData, $responseData);

        return $transaction->update(['response_data' => $mergedData]);
    }

    /**
     * Get transaction response data
     */
    public function getResponseData(Model $transaction, string $key = null): mixed
    {
        $responseData = $transaction->response_data ?? [];

        if ($key) {
            return $responseData[$key] ?? null;
        }

        return $responseData;
    }

    /**
     * Check if transaction is expired
     */
    public function isTransactionExpired(Model $transaction, int $expiryHours = 24): bool
    {
        if ($transaction->status !== TransactionStatus::INITIATED) {
            return false;
        }

        $expiryTime = $transaction->created_at->addHours($expiryHours);
        return now()->isAfter($expiryTime);
    }

    /**
     * Get transaction age in hours
     */
    public function getTransactionAge(Model $transaction): int
    {
        return $transaction->created_at->diffInHours(now());
    }
}
