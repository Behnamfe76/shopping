<?php

namespace Fereydooni\Shopping\app\Events;

use Fereydooni\Shopping\app\Models\ProductMeta;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ProductMetaUpdated
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public ProductMeta $productMeta,
        public array $changes
    ) {}
}
