<?php

namespace Fereydooni\Shopping\app\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Fereydooni\Shopping\app\Models\ProductVariant;

class ProductVariantStockUpdated
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public ProductVariant $variant,
        public int $oldStock,
        public int $newStock,
        public string $operation,
        public ?string $reason = null
    ) {
    }
}
