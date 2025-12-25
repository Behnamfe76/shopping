<?php

namespace Fereydooni\Shopping\app\Models;

use Fereydooni\Shopping\app\Enums\TransactionStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transaction extends Model
{
    protected $fillable = [
        'order_id',
        'user_id',
        'gateway',
        'transaction_id',
        'amount',
        'currency',
        'status',
        'payment_date',
        'response_data',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'status' => TransactionStatus::class,
        'payment_date' => 'datetime',
        'response_data' => 'array',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(config('shopping.user_model', 'App\Models\User'));
    }
}
