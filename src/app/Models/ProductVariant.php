<?php

namespace Fereydooni\Shopping\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class ProductVariant extends Model
{
    protected $fillable = [
        'product_id',
        'sku',
        'price',
        'stock',
        'weight',
        'dimensions',
        'barcode',
        'is_active',
        'is_featured',
        'sort_order',
        'cost_price',
        'sale_price',
        'compare_price',
        'inventory_tracking',
        'low_stock_threshold',
        'reserved_stock',
        'available_stock',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'stock' => 'integer',
        'weight' => 'decimal:2',
        'cost_price' => 'decimal:2',
        'sale_price' => 'decimal:2',
        'compare_price' => 'decimal:2',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'sort_order' => 'integer',
        'low_stock_threshold' => 'integer',
        'reserved_stock' => 'integer',
        'available_stock' => 'integer',
        'created_by' => 'integer',
        'updated_by' => 'integer',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function attributeValues(): BelongsToMany
    {
        return $this->belongsToMany(ProductAttributeValue::class, 'product_variant_values', 'variant_id', 'attribute_value_id');
    }

    public function orderItems(): BelongsTo
    {
        return $this->belongsTo(OrderItem::class, 'variant_id');
    }
}
