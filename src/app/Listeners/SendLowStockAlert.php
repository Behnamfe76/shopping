<?php

namespace Fereydooni\Shopping\app\Listeners;

use Fereydooni\Shopping\app\Events\ProductLowStock;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class SendLowStockAlert implements ShouldQueue
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
    public function handle(ProductLowStock $event): void
    {
        $product = $event->product;
        $currentStock = $event->currentStock;
        $threshold = $event->threshold;

        // Log the low stock alert
        Log::warning('Low stock alert', [
            'product_id' => $product->id,
            'product_title' => $product->title,
            'current_stock' => $currentStock,
            'threshold' => $threshold,
            'category_id' => $product->category_id,
            'brand_id' => $product->brand_id,
        ]);

        // Send notification to inventory managers
        $this->sendInventoryManagerNotification($product, $currentStock, $threshold);

        // Send notification to product manager
        $this->sendProductManagerNotification($product, $currentStock, $threshold);

        // Send notification to admin users
        $this->sendAdminNotification($product, $currentStock, $threshold);
    }

    /**
     * Send notification to inventory managers.
     */
    private function sendInventoryManagerNotification($product, int $currentStock, int $threshold): void
    {
        // Implementation for sending notification to inventory managers
        // Example: Notification::send($inventoryManagers, new LowStockAlertNotification($product, $currentStock, $threshold));
    }

    /**
     * Send notification to product manager.
     */
    private function sendProductManagerNotification($product, int $currentStock, int $threshold): void
    {
        // Implementation for sending notification to product manager
        // Example: Notification::send($productManager, new LowStockAlertNotification($product, $currentStock, $threshold));
    }

    /**
     * Send notification to admin users.
     */
    private function sendAdminNotification($product, int $currentStock, int $threshold): void
    {
        // Implementation for sending notification to admin users
        // Example: Notification::send($adminUsers, new LowStockAlertNotification($product, $currentStock, $threshold));
    }

    /**
     * Handle a job failure.
     */
    public function failed(ProductLowStock $event, \Throwable $exception): void
    {
        Log::error('Failed to send low stock alert', [
            'product_id' => $event->product->id,
            'error' => $exception->getMessage(),
        ]);
    }
}
