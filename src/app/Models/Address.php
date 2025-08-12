<?php

namespace Fereydooni\Shopping\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Fereydooni\Shopping\app\Enums\AddressType;

class Address extends Model
{
    protected $fillable = [
        'user_id',
        'type',
        'full_name',
        'phone',
        'street',
        'city',
        'state',
        'postal_code',
        'country',
    ];

    protected $casts = [
        'type' => AddressType::class,
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(config('shopping.user_model', 'App\Models\User'));
    }

    public function shippingOrders(): HasMany
    {
        return $this->hasMany(Order::class, 'shipping_address_id');
    }

    public function billingOrders(): HasMany
    {
        return $this->hasMany(Order::class, 'billing_address_id');
    }
}
