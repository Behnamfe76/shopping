<?php

namespace Fereydooni\Shopping\app\Events;

use Fereydooni\Shopping\app\Models\ProductVariant;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ProductVariantDeleted
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public ProductVariant $variant
    ) {}
}
