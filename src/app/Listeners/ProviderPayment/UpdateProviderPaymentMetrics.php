<?php

namespace Fereydooni\Shopping\App\Listeners\ProviderPayment;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Fereydooni\Shopping\App\Events\ProviderPayment\ProviderPaymentCreated;
use Fereydooni\Shopping\App\Events\ProviderPayment\ProviderPaymentUpdated;
use Fereydooni\Shopping\App\Events\ProviderPayment\ProviderPaymentProcessed;
use Fereydooni\Shopping\App\Events\ProviderPayment\ProviderPaymentCompleted;
use Fereydooni\Shopping\App\Events\ProviderPayment\ProviderPaymentFailed;
use Fereydooni\Shopping\App\Events\ProviderPayment\ProviderPaymentReconciled;

class UpdateProviderPaymentMetrics implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Handle the event.
     */
    public function handle($event): void
    {
        try {
            $payment = $event->payment;
            $eventType = $this->getEventType($event);

            // Update payment metrics based on event type
            $this->updateMetrics($payment, $eventType);

            Log::info('Provider payment metrics updated successfully', [
                'payment_id' => $payment->id,
                'event_type' => $eventType
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to update provider payment metrics', [
                'error' => $e->getMessage(),
                'event_type' => get_class($event),
                'payment_id' => $event->payment->id ?? 'unknown'
            ]);
        }
    }

    /**
     * Get the event type for metrics update.
     */
    protected function getEventType($event): string
    {
        return match (get_class($event)) {
            ProviderPaymentCreated::class => 'payment_created',
            ProviderPaymentUpdated::class => 'payment_updated',
            ProviderPaymentProcessed::class => 'payment_processed',
            ProviderPaymentCompleted::class => 'payment_completed',
            ProviderPaymentFailed::class => 'payment_failed',
            ProviderPaymentReconciled::class => 'payment_reconciled',
            default => 'unknown_event'
        };
    }

    /**
     * Update payment metrics.
     */
    protected function updateMetrics($payment, string $eventType): void
    {
        // This would typically update:
        // - Payment count metrics
        // - Amount totals
        // - Status distribution
        // - Payment method statistics
        // - Reconciliation metrics
        // - Performance indicators

        $metricsData = [
            'payment_id' => $payment->id,
            'provider_id' => $payment->provider_id,
            'event_type' => $eventType,
            'amount' => $payment->amount,
            'currency' => $payment->currency,
            'status' => $payment->status,
            'payment_method' => $payment->payment_method,
            'timestamp' => now()
        ];

        Log::info('Updating payment metrics', $metricsData);
    }
}
