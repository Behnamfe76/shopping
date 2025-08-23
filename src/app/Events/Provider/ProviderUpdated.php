<?php

namespace Fereydooni\Shopping\app\Events\Provider;

use Fereydooni\Shopping\app\Models\Provider;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ProviderUpdated
{
    use Dispatchable, SerializesModels;

    public Provider $provider;
    public array $changes;

    public function __construct(Provider $provider, array $changes = [])
    {
        $this->provider = $provider;
        $this->changes = $changes;
    }
}
