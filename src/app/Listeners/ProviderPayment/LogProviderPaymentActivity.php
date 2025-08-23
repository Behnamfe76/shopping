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

class LogProviderPaymentActivity implements ShouldQueue
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

            // Log the payment activity
            $this->logActivity($payment, $eventType);

            Log::info('Provider payment activity logged successfully', [
                'payment_id' => $payment->id,
                'event_type' => $eventType
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to log provider payment activity', [
                'error' => $e->getMessage(),
                'event_type' => get_class($event),
                'payment_id' => $event->payment->id ?? 'unknown'
            ]);
        }
    }

    /**
     * Get the event type for logging.
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
     * Log the payment activity.
     */
    protected function logActivity($payment, string $eventType): void
    {
        // This would typically log to:
        // - Activity log table
        // - Audit trail
        // - System logs
        // - External logging service

        $activityData = [
            'payment_id' => $payment->id,
            'provider_id' => $payment->provider_id,
            'event_type' => $eventType,
            'amount' => $payment->amount,
            'currency' => $payment->currency,
            'status' => $payment->status,
            'payment_method' => $payment->payment_method,
            'timestamp' => now(),
            'user_id' => auth()->id() ?? null,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent()
        ];

        Log::info('Provider payment activity', $activityData);
    }
}
