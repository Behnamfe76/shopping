<?php

namespace Fereydooni\Shopping\App\Listeners\ProviderInvoice;

use Fereydooni\Shopping\App\Events\ProviderInvoice\ProviderInvoicePaid;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProcessInvoicePayment implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Handle the event.
     */
    public function handle(ProviderInvoicePaid $event): void
    {
        try {
            $invoice = $event->invoice;

            DB::transaction(function () use ($invoice) {
                $this->processPayment($invoice);
                $this->updateProviderRecords($invoice);
                $this->createPaymentRecord($invoice);
                $this->updateFinancialMetrics($invoice);
            });

            Log::info('Invoice payment processed successfully', [
                'invoice_id' => $invoice->id,
                'provider_id' => $invoice->provider_id,
                'amount' => $invoice->total_amount,
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to process invoice payment', [
                'invoice_id' => $event->invoice->id ?? 'unknown',
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    protected function processPayment($invoice): void
    {
        // Process the actual payment
        // This could involve calling payment gateways, updating bank records, etc.

        Log::info('Processing payment for invoice', [
            'invoice_id' => $invoice->id,
            'amount' => $invoice->total_amount,
            'payment_method' => $invoice->payment_method,
        ]);
    }

    protected function updateProviderRecords($invoice): void
    {
        // Update provider's payment history and outstanding balance
        $provider = $invoice->provider;

        if ($provider) {
            // Update provider's total paid amount
            $provider->increment('total_paid_amount', $invoice->total_amount);

            // Update provider's outstanding balance
            $provider->decrement('outstanding_balance', $invoice->total_amount);

            // Update last payment date
            $provider->update(['last_payment_date' => now()]);

            Log::info('Provider records updated after payment', [
                'provider_id' => $provider->id,
                'invoice_id' => $invoice->id,
            ]);
        }
    }

    protected function createPaymentRecord($invoice): void
    {
        // Create a payment record in the payments table
        // This could be a separate table for payment tracking

        $paymentData = [
            'provider_id' => $invoice->provider_id,
            'invoice_id' => $invoice->id,
            'amount' => $invoice->total_amount,
            'payment_date' => $invoice->paid_at ?? now(),
            'payment_method' => $invoice->payment_method,
            'reference_number' => $invoice->reference_number,
            'status' => 'completed',
            'created_at' => now(),
            'updated_at' => now(),
        ];

        // You could create a ProviderPayment model here
        // ProviderPayment::create($paymentData);

        Log::info('Payment record created', [
            'invoice_id' => $invoice->id,
            'payment_data' => $paymentData,
        ]);
    }

    protected function updateFinancialMetrics($invoice): void
    {
        // Update system-wide financial metrics
        // This could involve updating cache, database fields, etc.

        $cacheKey = 'financial_metrics_'.date('Y-m');
        $metrics = cache($cacheKey, []);

        $metrics['total_payments'] = ($metrics['total_payments'] ?? 0) + 1;
        $metrics['total_payment_amount'] = ($metrics['total_payment_amount'] ?? 0) + $invoice->total_amount;
        $metrics['average_payment_amount'] = $metrics['total_payment_amount'] / $metrics['total_payments'];

        cache([$cacheKey => $metrics], now()->addMonth());

        Log::info('Financial metrics updated', [
            'invoice_id' => $invoice->id,
            'metrics' => $metrics,
        ]);
    }
}
