<?php

namespace Fereydooni\Shopping\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Fereydooni\Shopping\app\Enums\SubscriptionStatus;

class UserSubscription extends Model
{
    protected $fillable = [
        'user_id',
        'subscription_id',
        'order_id',
        'start_date',
        'end_date',
        'status',
        'next_billing_date',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'next_billing_date' => 'date',
        'status' => SubscriptionStatus::class,
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(config('shopping.user_model', 'App\Models\User'));
    }

    public function subscription(): BelongsTo
    {
        return $this->belongsTo(Subscription::class);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}
