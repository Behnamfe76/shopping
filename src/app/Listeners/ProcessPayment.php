<?php

namespace Fereydooni\Shopping\app\Listeners;

use Fereydooni\Shopping\app\Events\OrderCreated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class ProcessPayment implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(OrderCreated $event): void
    {
        $order = $event->order;

        // Process payment based on payment method
        switch ($order->payment_method) {
            case 'credit_card':
                $this->processCreditCardPayment($order);
                break;
            case 'paypal':
                $this->processPayPalPayment($order);
                break;
            case 'bank_transfer':
                $this->processBankTransfer($order);
                break;
            default:
                \Log::warning("Unknown payment method for order #{$order->id}: {$order->payment_method}");
        }
    }

    /**
     * Process credit card payment
     */
    private function processCreditCardPayment($order): void
    {
        // Credit card payment processing logic
        // This would integrate with a payment gateway like Stripe
        \Log::info("Processing credit card payment for order #{$order->id}");
    }

    /**
     * Process PayPal payment
     */
    private function processPayPalPayment($order): void
    {
        // PayPal payment processing logic
        \Log::info("Processing PayPal payment for order #{$order->id}");
    }

    /**
     * Process bank transfer payment
     */
    private function processBankTransfer($order): void
    {
        // Bank transfer processing logic
        \Log::info("Processing bank transfer for order #{$order->id}");
    }
}
