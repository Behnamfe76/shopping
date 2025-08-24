<?php

namespace Fereydooni\Shopping\App\Events\ProviderInvoice;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Fereydooni\Shopping\App\Models\ProviderInvoice;

class ProviderInvoiceSent
{
    use Dispatchable, SerializesModels;

    public ProviderInvoice $invoice;
    public string $sentAt;

    /**
     * Create a new event instance.
     */
    public function __construct(ProviderInvoice $invoice, string $sentAt = null)
    {
        $this->invoice = $invoice;
        $this->sentAt = $sentAt ?? now()->toISOString();
    }
}

