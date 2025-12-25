<?php

namespace Fereydooni\Shopping\App\Events\ProviderInvoice;

use Fereydooni\Shopping\App\Models\ProviderInvoice;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ProviderInvoiceCancelled
{
    use Dispatchable, SerializesModels;

    public ProviderInvoice $invoice;

    public ?string $reason;

    public string $cancelledAt;

    /**
     * Create a new event instance.
     */
    public function __construct(ProviderInvoice $invoice, ?string $reason = null, ?string $cancelledAt = null)
    {
        $this->invoice = $invoice;
        $this->reason = $reason;
        $this->cancelledAt = $cancelledAt ?? now()->toISOString();
    }
}
