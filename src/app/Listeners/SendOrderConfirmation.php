<?php

namespace Fereydooni\Shopping\app\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Fereydooni\Shopping\app\Events\OrderCreated;

class SendOrderConfirmation implements ShouldQueue
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

        // Send order confirmation email to customer
        // This would typically use Laravel's Mail facade
        // Mail::to($order->user->email)->send(new OrderConfirmationMail($order));

        // For now, just log the action
        \Log::info('Order confirmation email sent for order #' . $order->id);
    }
}
