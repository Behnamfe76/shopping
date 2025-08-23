<?php

namespace Fereydooni\Shopping\app\Events\Provider;

use Fereydooni\Shopping\app\Models\Provider;
use Fereydooni\Shopping\app\Enums\ProviderStatus;

class ProviderStatusChanged
{
    public Provider $provider;
    public ProviderStatus $oldStatus;
    public ProviderStatus $newStatus;
    public ?string $reason;

    public function __construct(Provider $provider, ProviderStatus $oldStatus, ProviderStatus $newStatus, ?string $reason = null)
    {
        $this->provider = $provider;
        $this->oldStatus = $oldStatus;
        $this->newStatus = $newStatus;
        $this->reason = $reason;
    }
}
