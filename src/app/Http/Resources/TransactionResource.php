<?php

namespace Fereydooni\Shopping\app\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Fereydooni\Shopping\app\Models\Transaction;

class TransactionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        /** @var Transaction $transaction */
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
            'response_data' => $transaction->response_data,
            'created_at' => $transaction->created_at->toISOString(),
            'updated_at' => $transaction->updated_at->toISOString(),
        ];

        // Include relationships if requested
        if ($request->has('include_relationships')) {
            $includes = is_array($request->include_relationships)
                ? $request->include_relationships
                : explode(',', $request->include_relationships);

            if (in_array('order', $includes) && $transaction->relationLoaded('order')) {
                $data['order'] = new OrderResource($transaction->order);
            }

            if (in_array('user', $includes) && $transaction->relationLoaded('user')) {
                $data['user'] = new UserResource($transaction->user);
            }

            if (in_array('order.items', $includes) && $transaction->relationLoaded('order.items')) {
                $data['order']['items'] = OrderItemResource::collection($transaction->order->items);
            }

            if (in_array('order.statusHistory', $includes) && $transaction->relationLoaded('order.statusHistory')) {
                $data['order']['status_history'] = OrderStatusHistoryResource::collection($transaction->order->statusHistory);
            }
        }

        // Include additional metadata for admin users
        if ($request->user() && $request->user()->can('transaction.view.any')) {
            $data['metadata'] = [
                'is_successful' => $transaction->status === 'success',
                'is_failed' => $transaction->status === 'failed',
                'is_refunded' => $transaction->status === 'refunded',
                'is_pending' => $transaction->status === 'initiated',
                'can_be_refunded' => $transaction->status === 'success',
                'can_be_processed' => $transaction->status === 'initiated',
            ];
        }

        return $data;
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
        return number_format($amount, 2) . ' ' . strtoupper($currency);
    }
}
