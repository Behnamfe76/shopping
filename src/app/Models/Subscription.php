<?php

namespace Fereydooni\Shopping\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Fereydooni\Shopping\app\Enums\BillingCycle;

class Subscription extends Model
{
    protected $fillable = [
        'product_id',
        'name',
        'billing_cycle',
        'billing_interval',
        'price',
        'trial_period_days',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'billing_interval' => 'integer',
        'trial_period_days' => 'integer',
        'billing_cycle' => BillingCycle::class,
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function userSubscriptions(): HasMany
    {
        return $this->hasMany(UserSubscription::class);
    }
}
