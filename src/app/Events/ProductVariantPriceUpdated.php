<?php

namespace Fereydooni\Shopping\app\Events;

use Fereydooni\Shopping\app\Models\ProductVariant;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ProductVariantPriceUpdated
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public ProductVariant $variant,
        public string $priceType,
        public float $oldPrice,
        public float $newPrice
    ) {}
}
