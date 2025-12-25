<?php

namespace Fereydooni\Shopping\App\Events\ProviderInvoice;

use Fereydooni\Shopping\App\Models\ProviderInvoice;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ProviderInvoiceSent
{
    use Dispatchable, SerializesModels;

    public ProviderInvoice $invoice;

    public string $sentAt;

    /**
     * Create a new event instance.
     */
    public function __construct(ProviderInvoice $invoice, ?string $sentAt = null)
    {
        $this->invoice = $invoice;
        $this->sentAt = $sentAt ?? now()->toISOString();
    }
}
