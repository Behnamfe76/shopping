<?php

namespace Fereydooni\Shopping\App\Events\ProviderInvoice;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Fereydooni\Shopping\App\Models\ProviderInvoice;

class ProviderInvoicePaid
{
    use Dispatchable, SerializesModels;

    public ProviderInvoice $invoice;
    public string $paidAt;
    public ?string $paymentMethod;

    /**
     * Create a new event instance.
     */
    public function __construct(ProviderInvoice $invoice, string $paidAt = null, ?string $paymentMethod = null)
    {
        $this->invoice = $invoice;
        $this->paidAt = $paidAt ?? now()->toISOString();
        $this->paymentMethod = $paymentMethod;
    }
}
