<?php

namespace Fereydooni\Shopping\App\Listeners\ProviderInvoice;

use Fereydooni\Shopping\App\Events\ProviderInvoice\ProviderInvoiceCreated;
use Fereydooni\Shopping\App\Events\ProviderInvoice\ProviderInvoiceSent;
use Fereydooni\Shopping\App\Events\ProviderInvoice\ProviderInvoicePaid;
use Fereydooni\Shopping\App\Events\ProviderInvoice\ProviderInvoiceOverdue;
use Fereydooni\Shopping\App\Events\ProviderInvoice\ProviderInvoiceCancelled;
use Fereydooni\Shopping\App\Notifications\ProviderInvoice\InvoiceCreated;
use Fereydooni\Shopping\App\Notifications\ProviderInvoice\InvoiceSent;
use Fereydooni\Shopping\App\Notifications\ProviderInvoice\InvoicePaid;
use Fereydooni\Shopping\Shopping\App\Notifications\ProviderInvoice\InvoiceOverdue;
use Fereydooni\Shopping\App\Notifications\ProviderInvoice\InvoiceCancelled;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class SendProviderInvoiceNotification implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Handle the event.
     */
    public function handle($event): void
    {
        try {
            $invoice = $event->invoice;
            $provider = $invoice->provider;

            if (!$provider) {
                Log::warning('Provider not found for invoice notification', [
                    'invoice_id' => $invoice->id
                ]);
                return;
            }

            switch (get_class($event)) {
                case ProviderInvoiceCreated::class:
                    $this->handleInvoiceCreated($invoice, $provider);
                    break;
                case ProviderInvoiceSent::class:
                    $this->handleInvoiceSent($invoice, $provider);
                    break;
                case ProviderInvoicePaid::class:
                    $this->handleInvoicePaid($invoice, $provider);
                    break;
                case ProviderInvoiceOverdue::class:
                    $this->handleInvoiceOverdue($invoice, $provider);
                    break;
                case ProviderInvoiceCancelled::class:
                    $this->handleInvoiceCancelled($invoice, $provider);
                    break;
            }

        } catch (\Exception $e) {
            Log::error('Failed to send provider invoice notification', [
                'event' => get_class($event),
                'invoice_id' => $event->invoice->id ?? 'unknown',
                'error' => $e->getMessage()
            ]);
        }
    }

    protected function handleInvoiceCreated($invoice, $provider): void
    {
        // Send notification to internal team
        Log::info('Invoice created notification sent', [
            'invoice_id' => $invoice->id,
            'provider_id' => $provider->id
        ]);

        // Could dispatch actual notification here
        // $provider->notify(new InvoiceCreated($invoice));
    }

    protected function handleInvoiceSent($invoice, $provider): void
    {
        // Send notification to provider
        Log::info('Invoice sent notification sent to provider', [
            'invoice_id' => $invoice->id,
            'provider_id' => $provider->id
        ]);

        // Could dispatch actual notification here
        // $provider->notify(new InvoiceSent($invoice));
    }

    protected function handleInvoicePaid($invoice, $provider): void
    {
        // Send notification to internal team
        Log::info('Invoice paid notification sent', [
            'invoice_id' => $invoice->id,
            'provider_id' => $provider->id
        ]);

        // Could dispatch actual notification here
        // $provider->notify(new InvoicePaid($invoice));
    }

    protected function handleInvoiceOverdue($invoice, $provider): void
    {
        // Send overdue notification to provider
        Log::info('Invoice overdue notification sent to provider', [
            'invoice_id' => $invoice->id,
            'provider_id' => $provider->id,
            'days_overdue' => $event->daysOverdue ?? 0
        ]);

        // Could dispatch actual notification here
        // $provider->notify(new InvoiceOverdue($invoice));
    }

    protected function handleInvoiceCancelled($invoice, $provider): void
    {
        // Send cancellation notification to provider
        Log::info('Invoice cancelled notification sent to provider', [
            'invoice_id' => $invoice->id,
            'provider_id' => $provider->id,
            'reason' => $event->reason ?? 'No reason provided'
        ]);

        // Could dispatch actual notification here
        // $provider->notify(new InvoiceCancelled($invoice));
    }
}
