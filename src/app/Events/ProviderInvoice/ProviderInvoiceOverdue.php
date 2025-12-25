<?php

namespace Fereydooni\Shopping\App\Events\ProviderInvoice;

use Fereydooni\Shopping\App\Models\ProviderInvoice;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ProviderInvoiceOverdue
{
    use Dispatchable, SerializesModels;

    public ProviderInvoice $invoice;

    public int $daysOverdue;

    public string $overdueAt;

    /**
     * Create a new event instance.
     */
    public function __construct(ProviderInvoice $invoice, int $daysOverdue = 0, ?string $overdueAt = null)
    {
        $this->invoice = $invoice;
        $this->daysOverdue = $daysOverdue;
        $this->overdueAt = $overdueAt ?? now()->toISOString();
    }
}
