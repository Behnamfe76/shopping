<?php

namespace Fereydooni\Shopping\app\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Fereydooni\Shopping\app\Models\ProductMeta;

class ProductMetaUpdated
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public ProductMeta $productMeta,
        public array $changes
    ) {
    }
}
