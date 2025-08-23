<?php

namespace Fereydooni\Shopping\App\Events\ProviderPayment;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Fereydooni\Shopping\App\Models\ProviderPayment;

class ProviderPaymentProcessed
{
    use Dispatchable, SerializesModels;

    /**
     * The provider payment instance.
     */
    public ProviderPayment $payment;

    /**
     * Create a new event instance.
     */
    public function __construct(ProviderPayment $payment)
    {
        $this->payment = $payment;
    }

    /**
     * Get the channels the event should broadcast on.
     */
    public function broadcastOn(): array
    {
        return [];
    }
}
