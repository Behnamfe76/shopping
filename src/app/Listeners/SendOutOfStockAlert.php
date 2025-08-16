<?php

namespace Fereydooni\Shopping\app\Listeners;

use Fereydooni\Shopping\app\Events\ProductOutOfStock;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class SendOutOfStockAlert implements ShouldQueue
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
    public function handle(ProductOutOfStock $event): void
    {
        $product = $event->product;

        // Log the out of stock alert
        Log::critical('Product out of stock', [
            'product_id' => $product->id,
            'product_title' => $product->title,
            'sku' => $product->sku,
            'category_id' => $product->category_id,
            'brand_id' => $product->brand_id
        ]);

        // Send urgent notification to inventory managers
        $this->sendUrgentInventoryAlert($product);

        // Send notification to product manager
        $this->sendProductManagerAlert($product);

        // Send notification to admin users
        $this->sendAdminAlert($product);

        // Send notification to suppliers if configured
        $this->sendSupplierAlert($product);
    }

    /**
     * Send urgent notification to inventory managers.
     */
    private function sendUrgentInventoryAlert($product): void
    {
        // Implementation for sending urgent notification to inventory managers
        // Example: Notification::send($inventoryManagers, new OutOfStockAlertNotification($product));
    }

    /**
     * Send notification to product manager.
     */
    private function sendProductManagerAlert($product): void
    {
        // Implementation for sending notification to product manager
        // Example: Notification::send($productManager, new OutOfStockAlertNotification($product));
    }

    /**
     * Send notification to admin users.
     */
    private function sendAdminAlert($product): void
    {
        // Implementation for sending notification to admin users
        // Example: Notification::send($adminUsers, new OutOfStockAlertNotification($product));
    }

    /**
     * Send notification to suppliers.
     */
    private function sendSupplierAlert($product): void
    {
        // Implementation for sending notification to suppliers
        // Example: Notification::send($suppliers, new OutOfStockAlertNotification($product));
    }

    /**
     * Handle a job failure.
     */
    public function failed(ProductOutOfStock $event, \Throwable $exception): void
    {
        Log::error('Failed to send out of stock alert', [
            'product_id' => $event->product->id,
            'error' => $exception->getMessage()
        ]);
    }
}
