<?php

namespace Fereydooni\Shopping\app\Events;

use Fereydooni\Shopping\app\Models\ProductVariant;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ProductVariantStatusChanged
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public ProductVariant $variant,
        public string $statusType,
        public bool $oldValue,
        public bool $newValue
    ) {}
}
