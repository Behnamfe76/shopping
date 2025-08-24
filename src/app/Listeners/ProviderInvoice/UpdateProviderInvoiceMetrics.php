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
use Illuminate\Support\Facades\Cache;

class UpdateProviderInvoiceMetrics implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Handle the event.
     */
    public function handle($event): void
    {
        try {
            $invoice = $event->invoice;
            $providerId = $invoice->provider_id;

            switch (get_class($event)) {
                case ProviderInvoiceCreated::class:
                    $this->updateMetricsOnCreate($providerId);
                    break;
                case ProviderInvoiceUpdated::class:
                    $this->updateMetricsOnUpdate($providerId);
                    break;
                case ProviderInvoiceSent::class:
                    $this->updateMetricsOnSend($providerId);
                    break;
                case ProviderInvoicePaid::class:
                    $this->updateMetricsOnPaid($providerId);
                    break;
                case ProviderInvoiceOverdue::class:
                    $this->updateMetricsOnOverdue($providerId);
                    break;
                case ProviderInvoiceCancelled::class:
                    $this->updateMetricsOnCancelled($providerId);
                    break;
            }

            // Clear cache for this provider's metrics
            $this->clearProviderMetricsCache($providerId);

        } catch (\Exception $e) {
            Log::error('Failed to update provider invoice metrics', [
                'event' => get_class($event),
                'invoice_id' => $event->invoice->id ?? 'unknown',
                'error' => $e->getMessage()
            ]);
        }
    }

    protected function updateMetricsOnCreate(int $providerId): void
    {
        // Update total invoice count
        $this->incrementMetric($providerId, 'total_invoices');

        // Update draft invoice count
        $this->incrementMetric($providerId, 'draft_invoices');

        Log::info('Provider invoice metrics updated on create', ['provider_id' => $providerId]);
    }

    protected function updateMetricsOnUpdate(int $providerId): void
    {
        // Metrics might change on update, so recalculate
        $this->recalculateProviderMetrics($providerId);

        Log::info('Provider invoice metrics updated on update', ['provider_id' => $providerId]);
    }

    protected function updateMetricsOnSend(int $providerId): void
    {
        // Decrement draft count, increment sent count
        $this->decrementMetric($providerId, 'draft_invoices');
        $this->incrementMetric($providerId, 'sent_invoices');

        Log::info('Provider invoice metrics updated on send', ['provider_id' => $providerId]);
    }

    protected function updateMetricsOnPaid(int $providerId): void
    {
        // Decrement sent count, increment paid count
        $this->decrementMetric($providerId, 'sent_invoices');
        $this->incrementMetric($providerId, 'paid_invoices');

        // Update total paid amount
        $this->updateTotalPaidAmount($providerId);

        Log::info('Provider invoice metrics updated on paid', ['provider_id' => $providerId]);
    }

    protected function updateMetricsOnOverdue(int $providerId): void
    {
        // Decrement sent count, increment overdue count
        $this->decrementMetric($providerId, 'sent_invoices');
        $this->incrementMetric($providerId, 'overdue_invoices');

        Log::info('Provider invoice metrics updated on overdue', ['provider_id' => $providerId]);
    }

    protected function updateMetricsOnCancelled(int $providerId): void
    {
        // Decrement appropriate count based on previous status
        $this->decrementMetric($providerId, 'draft_invoices');
        $this->incrementMetric($providerId, 'cancelled_invoices');

        Log::info('Provider invoice metrics updated on cancelled', ['provider_id' => $providerId]);
    }

    protected function incrementMetric(int $providerId, string $metric): void
    {
        $cacheKey = "provider_{$providerId}_metric_{$metric}";
        $currentValue = Cache::get($cacheKey, 0);
        Cache::put($cacheKey, $currentValue + 1, now()->addHours(24));
    }

    protected function decrementMetric(int $providerId, string $metric): void
    {
        $cacheKey = "provider_{$providerId}_metric_{$metric}";
        $currentValue = Cache::get($cacheKey, 0);
        if ($currentValue > 0) {
            Cache::put($cacheKey, $currentValue - 1, now()->addHours(24));
        }
    }

    protected function updateTotalPaidAmount(int $providerId): void
    {
        // This would typically update a database field
        // For now, we'll just clear the cache to force recalculation
        $this->clearProviderMetricsCache($providerId);
    }

    protected function recalculateProviderMetrics(int $providerId): void
    {
        // Clear cache to force recalculation on next access
        $this->clearProviderMetricsCache($providerId);
    }

    protected function clearProviderMetricsCache(int $providerId): void
    {
        $cacheKeys = [
            "provider_{$providerId}_metrics",
            "provider_{$providerId}_invoice_stats",
            "provider_{$providerId}_payment_stats"
        ];

        foreach ($cacheKeys as $key) {
            Cache::forget($key);
        }
    }
}

