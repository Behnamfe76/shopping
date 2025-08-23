<?php

namespace Fereydooni\Shopping\App\Events\ProviderInvoice;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Fereydooni\Shopping\App\Models\ProviderInvoice;

class ProviderInvoiceUpdated
{
    use Dispatchable, SerializesModels;

    public ProviderInvoice $invoice;
    public array $changes;

    /**
     * Create a new event instance.
     */
    public function __construct(ProviderInvoice $invoice, array $changes = [])
    {
        $this->invoice = $invoice;
        $this->changes = $changes;
    }
}
