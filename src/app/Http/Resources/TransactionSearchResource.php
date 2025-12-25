<?php

namespace Fereydooni\Shopping\app\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TransactionSearchResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        $transaction = $this->resource;

        $data = [
            'id' => $transaction->id,
            'order_id' => $transaction->order_id,
            'user_id' => $transaction->user_id,
            'gateway' => $transaction->gateway,
            'gateway_name' => $this->getGatewayName($transaction->gateway),
            'transaction_id' => $transaction->transaction_id,
            'amount' => $transaction->amount,
            'formatted_amount' => $this->formatAmount($transaction->amount, $transaction->currency),
            'currency' => $transaction->currency,
            'status' => $transaction->status,
            'status_label' => $this->getStatusLabel($transaction->status),
            'payment_date' => $transaction->payment_date?->toISOString(),
            'created_at' => $transaction->created_at->toISOString(),
            'updated_at' => $transaction->updated_at->toISOString(),
            'search_highlights' => $this->getSearchHighlights($transaction, $request->get('query')),
            'relevance_score' => $this->calculateRelevanceScore($transaction, $request->get('query')),
        ];

        // Include relationships if requested
        if ($request->has('include_relationships')) {
            $includes = is_array($request->include_relationships)
                ? $request->include_relationships
                : explode(',', $request->include_relationships);

            if (in_array('order', $includes) && $transaction->relationLoaded('order')) {
                $data['order'] = [
                    'id' => $transaction->order->id,
                    'order_number' => $transaction->order->order_number,
                    'total_amount' => $transaction->order->total_amount,
                    'status' => $transaction->order->status,
                ];
            }

            if (in_array('user', $includes) && $transaction->relationLoaded('user')) {
                $data['user'] = [
                    'id' => $transaction->user->id,
                    'name' => $transaction->user->name,
                    'email' => $transaction->user->email,
                ];
            }
        }

        return $data;
    }

    /**
     * Get search highlights for the transaction.
     */
    private function getSearchHighlights($transaction, ?string $query): array
    {
        if (! $query) {
            return [];
        }

        $highlights = [];
        $query = strtolower($query);

        // Check transaction ID
        if (str_contains(strtolower($transaction->transaction_id), $query)) {
            $highlights['transaction_id'] = $this->highlightText($transaction->transaction_id, $query);
        }

        // Check gateway
        if (str_contains(strtolower($transaction->gateway), $query)) {
            $highlights['gateway'] = $this->highlightText($transaction->gateway, $query);
        }

        // Check amount
        if (str_contains((string) $transaction->amount, $query)) {
            $highlights['amount'] = $this->highlightText((string) $transaction->amount, $query);
        }

        // Check currency
        if (str_contains(strtolower($transaction->currency), $query)) {
            $highlights['currency'] = $this->highlightText($transaction->currency, $query);
        }

        // Check status
        if (str_contains(strtolower($transaction->status), $query)) {
            $highlights['status'] = $this->highlightText($transaction->status, $query);
        }

        return $highlights;
    }

    /**
     * Highlight search terms in text.
     */
    private function highlightText(string $text, string $query): string
    {
        $highlighted = preg_replace(
            '/('.preg_quote($query, '/').')/i',
            '<mark>$1</mark>',
            $text
        );

        return $highlighted;
    }

    /**
     * Calculate relevance score for search results.
     */
    private function calculateRelevanceScore($transaction, ?string $query): float
    {
        if (! $query) {
            return 0.0;
        }

        $score = 0.0;
        $query = strtolower($query);

        // Exact matches get higher scores
        if (strtolower($transaction->transaction_id) === $query) {
            $score += 10.0;
        }

        if (strtolower($transaction->gateway) === $query) {
            $score += 8.0;
        }

        if (strtolower($transaction->currency) === $query) {
            $score += 6.0;
        }

        if (strtolower($transaction->status) === $query) {
            $score += 5.0;
        }

        // Partial matches get lower scores
        if (str_contains(strtolower($transaction->transaction_id), $query)) {
            $score += 3.0;
        }

        if (str_contains(strtolower($transaction->gateway), $query)) {
            $score += 2.0;
        }

        if (str_contains(strtolower($transaction->currency), $query)) {
            $score += 1.5;
        }

        if (str_contains(strtolower($transaction->status), $query)) {
            $score += 1.0;
        }

        // Amount matches
        if (str_contains((string) $transaction->amount, $query)) {
            $score += 2.0;
        }

        return min($score, 10.0); // Cap at 10.0
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
     * Get status label.
     */
    private function getStatusLabel(string $status): string
    {
        return match ($status) {
            'initiated' => 'Initiated',
            'success' => 'Successful',
            'failed' => 'Failed',
            'refunded' => 'Refunded',
            default => ucfirst($status)
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
