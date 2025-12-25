<?php

namespace Fereydooni\Shopping\app\Listeners;

use Fereydooni\Shopping\app\Events\OrderStatusChanged;
use Fereydooni\Shopping\app\Notifications\OrderStatusChangedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;

class SendStatusChangeNotification implements ShouldQueue
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
        $history = $event->history;

        // Send notification to order owner
        if ($order->user) {
            $order->user->notify(new OrderStatusChangedNotification($order, $history));
        }

        // Send notification to admin users if it's a significant status change
        if ($this->isSignificantStatusChange($event->oldStatus, $event->newStatus)) {
            $this->notifyAdmins($order, $history);
        }

        // Send email notification
        $this->sendEmailNotification($order, $history);
    }

    /**
     * Check if the status change is significant enough to notify admins.
     */
    private function isSignificantStatusChange(string $oldStatus, string $newStatus): bool
    {
        $significantChanges = [
            'pending' => ['cancelled', 'completed'],
            'paid' => ['cancelled', 'completed'],
            'shipped' => ['cancelled', 'completed'],
        ];

        return isset($significantChanges[$oldStatus]) && in_array($newStatus, $significantChanges[$oldStatus]);
    }

    /**
     * Notify admin users about significant status changes.
     */
    private function notifyAdmins($order, $history): void
    {
        // Get admin users (this would depend on your user roles/permissions system)
        $adminUsers = \App\Models\User::whereHas('roles', function ($query) {
            $query->where('name', 'admin');
        })->get();

        foreach ($adminUsers as $admin) {
            $admin->notify(new OrderStatusChangedNotification($order, $history, true));
        }
    }

    /**
     * Send email notification.
     */
    private function sendEmailNotification($order, $history): void
    {
        if ($order->user && $order->user->email) {
            Mail::send('shopping::emails.order-status-changed', [
                'order' => $order,
                'history' => $history,
                'oldStatus' => $history->old_status,
                'newStatus' => $history->new_status,
            ], function ($message) use ($order) {
                $message->to($order->user->email)
                    ->subject("Order #{$order->id} Status Updated");
            });
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(OrderStatusChanged $event, \Throwable $exception): void
    {
        \Log::error('Failed to send status change notification', [
            'order_id' => $event->order->id,
            'old_status' => $event->oldStatus,
            'new_status' => $event->newStatus,
            'error' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString(),
        ]);
    }
}
