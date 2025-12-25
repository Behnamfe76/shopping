<?php

namespace Fereydooni\Shopping\App\Events\ProviderInvoice;

use Fereydooni\Shopping\App\Models\ProviderInvoice;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ProviderInvoiceCreated
{
    use Dispatchable, SerializesModels;

    public ProviderInvoice $invoice;

    /**
     * Create a new event instance.
     */
    public function __construct(ProviderInvoice $invoice)
    {
        $this->invoice = $invoice;
    }
}
