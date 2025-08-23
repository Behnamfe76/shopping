<?php

namespace Fereydooni\Shopping\App\Events\ProviderInvoice;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Fereydooni\Shopping\App\Models\ProviderInvoice;

class ProviderInvoiceOverdue
{
    use Dispatchable, SerializesModels;

    public ProviderInvoice $invoice;
    public int $daysOverdue;
    public string $overdueAt;

    /**
     * Create a new event instance.
     */
    public function __construct(ProviderInvoice $invoice, int $daysOverdue = 0, string $overdueAt = null)
    {
        $this->invoice = $invoice;
        $this->daysOverdue = $daysOverdue;
        $this->overdueAt = $overdueAt ?? now()->toISOString();
    }
}
