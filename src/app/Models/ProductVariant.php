<?php

namespace Fereydooni\Shopping\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Fereydooni\Shopping\app\Models\ProductVariantValue;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductVariant extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'product_id',
        'name',
        'description',
        'in_stock',
        'multi_variant',
        'sku',
        'price',
        'stock_quantity',
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
        'stock_quantity' => 'integer',
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

    protected $with = ['values'];

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

    public function values()
    {
        return $this->hasMany(ProductVariantValue::class, 'variant_id');
    }
}
