<?php

namespace Fereydooni\Shopping\App\Listeners\ProviderPayment;

use Fereydooni\Shopping\App\Events\ProviderPayment\ProviderPaymentCompleted;
use Fereydooni\Shopping\App\Events\ProviderPayment\ProviderPaymentCreated;
use Fereydooni\Shopping\App\Events\ProviderPayment\ProviderPaymentFailed;
use Fereydooni\Shopping\App\Events\ProviderPayment\ProviderPaymentProcessed;
use Fereydooni\Shopping\App\Events\ProviderPayment\ProviderPaymentReconciled;
use Fereydooni\Shopping\App\Notifications\ProviderPayment\PaymentCompleted;
use Fereydooni\Shopping\App\Notifications\ProviderPayment\PaymentCreated;
use Fereydooni\Shopping\App\Notifications\ProviderPayment\PaymentFailed;
use Fereydooni\Shopping\App\Notifications\ProviderPayment\PaymentProcessed;
use Fereydooni\Shopping\App\Notifications\ProviderPayment\PaymentReconciled;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class SendProviderPaymentNotification implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Handle the event.
     */
    public function handle($event): void
    {
        try {
            $payment = $event->payment;
            $notification = null;

            // Determine which notification to send based on event type
            switch (get_class($event)) {
                case ProviderPaymentCreated::class:
                    $notification = new PaymentCreated($payment);
                    break;
                case ProviderPaymentProcessed::class:
                    $notification = new PaymentProcessed($payment);
                    break;
                case ProviderPaymentCompleted::class:
                    $notification = new PaymentCompleted($payment);
                    break;
                case ProviderPaymentFailed::class:
                    $notification = new PaymentFailed($payment);
                    break;
                case ProviderPaymentReconciled::class:
                    $notification = new PaymentReconciled($payment);
                    break;
                default:
                    Log::info('No notification configured for event type', [
                        'event_type' => get_class($event),
                        'payment_id' => $payment->id,
                    ]);

                    return;
            }

            if ($notification) {
                // Send notification to provider
                if ($payment->provider && $payment->provider->email) {
                    Notification::route('mail', $payment->provider->email)
                        ->notify($notification);
                }

                // Send notification to relevant staff members
                $this->notifyStaffMembers($payment, $notification);

                Log::info('Provider payment notification sent successfully', [
                    'payment_id' => $payment->id,
                    'provider_id' => $payment->provider_id,
                    'notification_type' => get_class($notification),
                ]);
            }

        } catch (\Exception $e) {
            Log::error('Failed to send provider payment notification', [
                'error' => $e->getMessage(),
                'event_type' => get_class($event),
                'payment_id' => $event->payment->id ?? 'unknown',
            ]);
        }
    }

    /**
     * Notify relevant staff members.
     */
    protected function notifyStaffMembers($payment, $notification): void
    {
        // This would typically notify:
        // - Accounting team
        // - Finance team
        // - Management
        // - Other stakeholders

        Log::info('Notifying staff members about payment', [
            'payment_id' => $payment->id,
            'notification_type' => get_class($notification),
        ]);
    }
}
