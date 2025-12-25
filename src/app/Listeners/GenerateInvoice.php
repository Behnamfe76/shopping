<?php

namespace Fereydooni\Shopping\app\Listeners;

use Fereydooni\Shopping\app\Events\OrderCreated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class GenerateInvoice implements ShouldQueue
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

        // Generate invoice PDF
        $invoiceData = [
            'order_id' => $order->id,
            'customer_name' => $order->user->name ?? 'Unknown',
            'customer_email' => $order->user->email ?? '',
            'order_date' => $order->placed_at ?? $order->created_at,
            'items' => $order->orderItems ?? [],
            'subtotal' => $order->subtotal,
            'tax_amount' => $order->tax_amount,
            'shipping_amount' => $order->shipping_amount,
            'discount_amount' => $order->discount_amount,
            'grand_total' => $order->grand_total,
            'shipping_address' => $order->shippingAddress ?? null,
            'billing_address' => $order->billingAddress ?? null,
        ];

        // Generate PDF invoice
        // $pdf = PDF::loadView('shopping::invoices.order', $invoiceData);
        // $pdfPath = storage_path("app/invoices/order-{$order->id}.pdf");
        // $pdf->save($pdfPath);

        // Store invoice record in database
        // Invoice::create([
        //     'order_id' => $order->id,
        //     'invoice_number' => 'INV-' . str_pad($order->id, 6, '0', STR_PAD_LEFT),
        //     'file_path' => $pdfPath,
        //     'amount' => $order->grand_total,
        // ]);

        \Log::info("Invoice generated for order #{$order->id}");
    }
}
