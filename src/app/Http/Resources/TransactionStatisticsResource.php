<?php

namespace Fereydooni\Shopping\app\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TransactionStatisticsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        $stats = $this->resource;

        return [
            'overview' => [
                'total_transactions' => $stats['total_transactions'] ?? 0,
                'total_amount' => $stats['total_amount'] ?? 0.0,
                'formatted_total_amount' => $this->formatAmount($stats['total_amount'] ?? 0.0, $stats['currency'] ?? 'USD'),
                'currency' => $stats['currency'] ?? 'USD',
                'average_transaction_amount' => $stats['average_transaction_amount'] ?? 0.0,
                'formatted_average_amount' => $this->formatAmount($stats['average_transaction_amount'] ?? 0.0, $stats['currency'] ?? 'USD'),
                'success_rate' => $stats['success_rate'] ?? 0.0,
                'failure_rate' => $stats['failure_rate'] ?? 0.0,
                'refund_rate' => $stats['refund_rate'] ?? 0.0,
            ],
            'status_distribution' => [
                'initiated' => [
                    'count' => $stats['status_distribution']['initiated']['count'] ?? 0,
                    'percentage' => $stats['status_distribution']['initiated']['percentage'] ?? 0.0,
                    'amount' => $stats['status_distribution']['initiated']['amount'] ?? 0.0,
                    'formatted_amount' => $this->formatAmount($stats['status_distribution']['initiated']['amount'] ?? 0.0, $stats['currency'] ?? 'USD'),
                ],
                'success' => [
                    'count' => $stats['status_distribution']['success']['count'] ?? 0,
                    'percentage' => $stats['status_distribution']['success']['percentage'] ?? 0.0,
                    'amount' => $stats['status_distribution']['success']['amount'] ?? 0.0,
                    'formatted_amount' => $this->formatAmount($stats['status_distribution']['success']['amount'] ?? 0.0, $stats['currency'] ?? 'USD'),
                ],
                'failed' => [
                    'count' => $stats['status_distribution']['failed']['count'] ?? 0,
                    'percentage' => $stats['status_distribution']['failed']['percentage'] ?? 0.0,
                    'amount' => $stats['status_distribution']['failed']['amount'] ?? 0.0,
                    'formatted_amount' => $this->formatAmount($stats['status_distribution']['failed']['amount'] ?? 0.0, $stats['currency'] ?? 'USD'),
                ],
                'refunded' => [
                    'count' => $stats['status_distribution']['refunded']['count'] ?? 0,
                    'percentage' => $stats['status_distribution']['refunded']['percentage'] ?? 0.0,
                    'amount' => $stats['status_distribution']['refunded']['amount'] ?? 0.0,
                    'formatted_amount' => $this->formatAmount($stats['status_distribution']['refunded']['amount'] ?? 0.0, $stats['currency'] ?? 'USD'),
                ],
            ],
            'gateway_performance' => $this->formatGatewayPerformance($stats['gateway_performance'] ?? []),
            'revenue_breakdown' => [
                'daily' => $this->formatRevenueData($stats['revenue_breakdown']['daily'] ?? []),
                'weekly' => $this->formatRevenueData($stats['revenue_breakdown']['weekly'] ?? []),
                'monthly' => $this->formatRevenueData($stats['revenue_breakdown']['monthly'] ?? []),
                'yearly' => $this->formatRevenueData($stats['revenue_breakdown']['yearly'] ?? []),
            ],
            'top_performing_gateways' => $this->formatTopGateways($stats['top_performing_gateways'] ?? []),
            'recent_activity' => [
                'last_24_hours' => [
                    'transactions' => $stats['recent_activity']['last_24_hours']['transactions'] ?? 0,
                    'amount' => $stats['recent_activity']['last_24_hours']['amount'] ?? 0.0,
                    'formatted_amount' => $this->formatAmount($stats['recent_activity']['last_24_hours']['amount'] ?? 0.0, $stats['currency'] ?? 'USD'),
                ],
                'last_7_days' => [
                    'transactions' => $stats['recent_activity']['last_7_days']['transactions'] ?? 0,
                    'amount' => $stats['recent_activity']['last_7_days']['amount'] ?? 0.0,
                    'formatted_amount' => $this->formatAmount($stats['recent_activity']['last_7_days']['amount'] ?? 0.0, $stats['currency'] ?? 'USD'),
                ],
                'last_30_days' => [
                    'transactions' => $stats['recent_activity']['last_30_days']['transactions'] ?? 0,
                    'amount' => $stats['recent_activity']['last_30_days']['amount'] ?? 0.0,
                    'formatted_amount' => $this->formatAmount($stats['recent_activity']['last_30_days']['amount'] ?? 0.0, $stats['currency'] ?? 'USD'),
                ],
            ],
            'currency_breakdown' => $this->formatCurrencyBreakdown($stats['currency_breakdown'] ?? []),
            'generated_at' => now()->toISOString(),
            'date_range' => [
                'start_date' => $stats['date_range']['start_date'] ?? null,
                'end_date' => $stats['date_range']['end_date'] ?? null,
            ],
        ];
    }

    /**
     * Format gateway performance data.
     */
    private function formatGatewayPerformance(array $gatewayData): array
    {
        $formatted = [];

        foreach ($gatewayData as $gateway => $data) {
            $formatted[$gateway] = [
                'gateway_name' => $this->getGatewayName($gateway),
                'total_transactions' => $data['total_transactions'] ?? 0,
                'successful_transactions' => $data['successful_transactions'] ?? 0,
                'failed_transactions' => $data['failed_transactions'] ?? 0,
                'refunded_transactions' => $data['refunded_transactions'] ?? 0,
                'total_amount' => $data['total_amount'] ?? 0.0,
                'formatted_total_amount' => $this->formatAmount($data['total_amount'] ?? 0.0, $data['currency'] ?? 'USD'),
                'success_rate' => $data['success_rate'] ?? 0.0,
                'failure_rate' => $data['failure_rate'] ?? 0.0,
                'refund_rate' => $data['refund_rate'] ?? 0.0,
                'average_transaction_amount' => $data['average_transaction_amount'] ?? 0.0,
                'formatted_average_amount' => $this->formatAmount($data['average_transaction_amount'] ?? 0.0, $data['currency'] ?? 'USD'),
            ];
        }

        return $formatted;
    }

    /**
     * Format revenue data.
     */
    private function formatRevenueData(array $revenueData): array
    {
        $formatted = [];

        foreach ($revenueData as $period => $data) {
            $formatted[$period] = [
                'period' => $period,
                'transactions' => $data['transactions'] ?? 0,
                'amount' => $data['amount'] ?? 0.0,
                'formatted_amount' => $this->formatAmount($data['amount'] ?? 0.0, $data['currency'] ?? 'USD'),
                'successful_transactions' => $data['successful_transactions'] ?? 0,
                'successful_amount' => $data['successful_amount'] ?? 0.0,
                'formatted_successful_amount' => $this->formatAmount($data['successful_amount'] ?? 0.0, $data['currency'] ?? 'USD'),
            ];
        }

        return $formatted;
    }

    /**
     * Format top performing gateways.
     */
    private function formatTopGateways(array $topGateways): array
    {
        $formatted = [];

        foreach ($topGateways as $index => $gateway) {
            $formatted[] = [
                'rank' => $index + 1,
                'gateway' => $gateway['gateway'],
                'gateway_name' => $this->getGatewayName($gateway['gateway']),
                'total_transactions' => $gateway['total_transactions'] ?? 0,
                'total_amount' => $gateway['total_amount'] ?? 0.0,
                'formatted_total_amount' => $this->formatAmount($gateway['total_amount'] ?? 0.0, $gateway['currency'] ?? 'USD'),
                'success_rate' => $gateway['success_rate'] ?? 0.0,
                'average_transaction_amount' => $gateway['average_transaction_amount'] ?? 0.0,
                'formatted_average_amount' => $this->formatAmount($gateway['average_transaction_amount'] ?? 0.0, $gateway['currency'] ?? 'USD'),
            ];
        }

        return $formatted;
    }

    /**
     * Format currency breakdown.
     */
    private function formatCurrencyBreakdown(array $currencyData): array
    {
        $formatted = [];

        foreach ($currencyData as $currency => $data) {
            $formatted[$currency] = [
                'currency' => $currency,
                'total_transactions' => $data['total_transactions'] ?? 0,
                'total_amount' => $data['total_amount'] ?? 0.0,
                'formatted_total_amount' => $this->formatAmount($data['total_amount'] ?? 0.0, $currency),
                'successful_transactions' => $data['successful_transactions'] ?? 0,
                'successful_amount' => $data['successful_amount'] ?? 0.0,
                'formatted_successful_amount' => $this->formatAmount($data['successful_amount'] ?? 0.0, $currency),
                'percentage_of_total' => $data['percentage_of_total'] ?? 0.0,
            ];
        }

        return $formatted;
    }

    /**
     * Get formatted gateway name.
     */
    private function getGatewayName(string $gateway): string
    {
        return match (strtolower($gateway)) {
            'stripe' => 'Stripe',
            'paypal' => 'PayPal',
            'square' => 'Square',
            'braintree' => 'Braintree',
            'adyen' => 'Adyen',
            'razorpay' => 'Razorpay',
            'mollie' => 'Mollie',
            'klarna' => 'Klarna',
            'affirm' => 'Affirm',
            'afterpay' => 'Afterpay',
            default => ucfirst($gateway)
        };
    }

    /**
     * Format amount with currency.
     */
    private function formatAmount(float $amount, string $currency): string
    {
        return number_format($amount, 2).' '.strtoupper($currency);
    }
}
