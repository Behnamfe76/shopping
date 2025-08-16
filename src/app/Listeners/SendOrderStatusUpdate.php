<?php

namespace Fereydooni\Shopping\app\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Fereydooni\Shopping\app\Events\OrderStatusChanged;

class SendOrderStatusUpdate implements ShouldQueue
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
    public function handle(OrderStatusChanged $event): void
    {
        $order = $event->order;
        $oldStatus = $event->oldStatus;
        $newStatus = $event->newStatus;

        // Send status update email to customer
        // Mail::to($order->user->email)->send(new OrderStatusUpdateMail($order, $oldStatus, $newStatus));

        // Send SMS notification if configured
        // SMS::send($order->user->phone, "Your order #{$order->id} status has changed from {$oldStatus} to {$newStatus}");

        // For now, just log the action
        \Log::info("Order status update notification sent for order #{$order->id}: {$oldStatus} -> {$newStatus}");
    }
}
