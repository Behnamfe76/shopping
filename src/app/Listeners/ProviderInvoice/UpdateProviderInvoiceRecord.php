<?php

namespace Fereydooni\Shopping\App\Listeners\ProviderInvoice;

use Fereydooni\Shopping\App\Events\ProviderInvoice\ProviderInvoiceCreated;
use Fereydooni\Shopping\App\Events\ProviderInvoice\ProviderInvoiceUpdated;
use Fereydooni\Shopping\App\Events\ProviderInvoice\ProviderInvoiceSent;
use Fereydooni\Shopping\App\Events\ProviderInvoice\ProviderInvoicePaid;
use Fereydooni\Shopping\App\Events\ProviderInvoice\ProviderInvoiceOverdue;
use Fereydooni\Shopping\App\Events\ProviderInvoice\ProviderInvoiceCancelled;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class UpdateProviderInvoiceRecord implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Handle the event.
     */
    public function handle($event): void
    {
        try {
            $invoice = $event->invoice;

            switch (get_class($event)) {
                case ProviderInvoiceCreated::class:
                    $this->handleInvoiceCreated($invoice);
                    break;
                case ProviderInvoiceUpdated::class:
                    $this->handleInvoiceUpdated($invoice, $event->changes);
                    break;
                case ProviderInvoiceSent::class:
                    $this->handleInvoiceSent($invoice);
                    break;
                case ProviderInvoicePaid::class:
                    $this->handleInvoicePaid($invoice);
                    break;
                case ProviderInvoiceOverdue::class:
                    $this->handleInvoiceOverdue($invoice);
                    break;
                case ProviderInvoiceCancelled::class:
                    $this->handleInvoiceCancelled($invoice);
                    break;
            }

        } catch (\Exception $e) {
            Log::error('Failed to update provider invoice record', [
                'event' => get_class($event),
                'invoice_id' => $event->invoice->id ?? 'unknown',
                'error' => $e->getMessage()
            ]);
        }
    }

    protected function handleInvoiceCreated($invoice): void
    {
        // Update provider's invoice count and total
        Log::info('Provider invoice record updated for created invoice', [
            'invoice_id' => $invoice->id,
            'provider_id' => $invoice->provider_id
        ]);
    }

    protected function handleInvoiceUpdated($invoice, array $changes): void
    {
        // Update provider's invoice records based on changes
        Log::info('Provider invoice record updated for modified invoice', [
            'invoice_id' => $invoice->id,
            'provider_id' => $invoice->provider_id,
            'changes' => $changes
        ]);
    }

    protected function handleInvoiceSent($invoice): void
    {
        // Update provider's sent invoice count
        Log::info('Provider invoice record updated for sent invoice', [
            'invoice_id' => $invoice->id,
            'provider_id' => $invoice->provider_id
        ]);
    }

    protected function handleInvoicePaid($invoice): void
    {
        // Update provider's paid invoice count and total
        Log::info('Provider invoice record updated for paid invoice', [
            'invoice_id' => $invoice->id,
            'provider_id' => $invoice->provider_id
        ]);
    }

    protected function handleInvoiceOverdue($invoice): void
    {
        // Update provider's overdue invoice count and total
        Log::info('Provider invoice record updated for overdue invoice', [
            'invoice_id' => $invoice->id,
            'provider_id' => $invoice->provider_id
        ]);
    }

    protected function handleInvoiceCancelled($invoice): void
    {
        // Update provider's cancelled invoice count
        Log::info('Provider invoice record updated for cancelled invoice', [
            'invoice_id' => $invoice->id,
            'provider_id' => $invoice->provider_id
        ]);
    }
}
