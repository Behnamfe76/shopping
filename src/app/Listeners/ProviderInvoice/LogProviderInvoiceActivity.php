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
use Illuminate\Support\Facades\Auth;

class LogProviderInvoiceActivity implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Handle the event.
     */
    public function handle($event): void
    {
        try {
            $invoice = $event->invoice;
            $user = Auth::user();
            $userId = $user ? $user->id : 'system';
            $userName = $user ? $user->name : 'System';

            $activityData = [
                'invoice_id' => $invoice->id,
                'provider_id' => $invoice->provider_id,
                'invoice_number' => $invoice->invoice_number,
                'user_id' => $userId,
                'user_name' => $userName,
                'timestamp' => now()->toISOString(),
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent()
            ];

            switch (get_class($event)) {
                case ProviderInvoiceCreated::class:
                    $this->logInvoiceCreated($activityData);
                    break;
                case ProviderInvoiceUpdated::class:
                    $this->logInvoiceUpdated($activityData, $event);
                    break;
                case ProviderInvoiceSent::class:
                    $this->logInvoiceSent($activityData);
                    break;
                case ProviderInvoicePaid::class:
                    $this->logInvoicePaid($activityData);
                    break;
                case ProviderInvoiceOverdue::class:
                    $this->logInvoiceOverdue($activityData);
                    break;
                case ProviderInvoiceCancelled::class:
                    $this->logInvoiceCancelled($activityData);
                    break;
            }

        } catch (\Exception $e) {
            Log::error('Failed to log provider invoice activity', [
                'event' => get_class($event),
                'invoice_id' => $event->invoice->id ?? 'unknown',
                'error' => $e->getMessage()
            ]);
        }
    }

    protected function logInvoiceCreated(array $activityData): void
    {
        Log::info('Provider invoice created', $activityData);

        // Here you could also store in an activity log table
        // ActivityLog::create([
        //     'log_name' => 'provider_invoice',
        //     'description' => 'Provider invoice created',
        //     'subject_type' => ProviderInvoice::class,
        //     'subject_id' => $activityData['invoice_id'],
        //     'causer_type' => User::class,
        //     'causer_id' => $activityData['user_id'],
        //     'properties' => $activityData
        // ]);
    }

    protected function logInvoiceUpdated(array $activityData, $event): void
    {
        $changes = $event->changes ?? [];
        $activityData['changes'] = $changes;

        Log::info('Provider invoice updated', $activityData);
    }

    protected function logInvoiceSent(array $activityData): void
    {
        Log::info('Provider invoice sent', $activityData);
    }

    protected function logInvoicePaid(array $activityData): void
    {
        Log::info('Provider invoice marked as paid', $activityData);
    }

    protected function logInvoiceOverdue(array $activityData): void
    {
        Log::info('Provider invoice marked as overdue', $activityData);
    }

    protected function logInvoiceCancelled(array $activityData): void
    {
        Log::info('Provider invoice cancelled', $activityData);
    }
}

