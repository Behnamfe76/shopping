<?php

namespace Fereydooni\Shopping\app\Models;

use Illuminate\Database\Eloquent\Model;
use Fereydooni\Shopping\app\Enums\Gender;
use Illuminate\Database\Eloquent\SoftDeletes;
use Fereydooni\Shopping\app\Enums\CustomerType;
use Fereydooni\Unixtime\HasTimestampEquivalents;
use Fereydooni\Shopping\app\Enums\CustomerStatus;
use Fereydooni\Shopping\app\Traits\HasUniqueColumn;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Customer extends Model
{
    use SoftDeletes;
    use HasUniqueColumn;
    use HasTimestampEquivalents;

    protected $uniqueColumnField = 'customer_number';

    /**
     * Optional: Specify which datetime columns should have timestamp equivalents.
     * If not set, all datetime/date/timestamp casts will be auto-detected.
     *
     * @var array
     */
    // protected $timestampEquivalentColumns = [
    //     'date_of_birth',
    //     'created_at',
    //     'updated_at',
    //     'deleted_at',
    //     'last_order_date',
    //     'first_order_date',
    // ];

    /**
     * Optional: Exclude specific columns from having timestamp equivalents.
     *
     * @var array
     */
    // protected $excludedTimestampColumns = [
    //     'date_of_birth', // Example: exclude date of birth from timestamp conversion
    // ];

    /**
     * Optional: Customize the suffix for timestamp columns.
     * Default is '_unix' (e.g., created_at_unix)
     *
     * @var string
     */
    // protected $timestampColumnSuffix = '_timestamp';

    protected $uniqueColumnSignature = [
        'length' => 15,
        'type'   => 'alphanumeric',
        'prefix' => 'CUST-',
        'suffix' => '',
    ];

    protected $fillable = [
        'user_id',
        'customer_number',
        'first_name',
        'last_name',
        'email',
        'phone',
        'date_of_birth',
        'gender',
        'company_name',
        'tax_id',
        'customer_type',
        'status',
        'loyalty_points',
        'total_orders',
        'total_spent',
        'average_order_value',
        'last_order_date',
        'first_order_date',
        'preferred_payment_method',
        'preferred_shipping_method',
        'marketing_consent',
        'newsletter_subscription',
        'notes',
        'tags',
        'address_count',
        'order_count',
        'review_count',
        'wishlist_count',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'gender' => Gender::class,
        'customer_type' => CustomerType::class,
        'status' => CustomerStatus::class,
        'loyalty_points' => 'integer',
        'total_orders' => 'integer',
        'total_spent' => 'decimal:2',
        'average_order_value' => 'decimal:2',
        'last_order_date' => 'datetime',
        'first_order_date' => 'datetime',
        'marketing_consent' => 'boolean',
        'newsletter_subscription' => 'boolean',
        'tags' => 'array',
        'address_count' => 'integer',
        'order_count' => 'integer',
        'review_count' => 'integer',
        'wishlist_count' => 'integer',
    ];

    protected $attributes = [
        'status' => CustomerStatus::PENDING,
        'customer_type' => CustomerType::INDIVIDUAL,
        'loyalty_points' => 0,
        'total_orders' => 0,
        'total_spent' => 0.00,
        'average_order_value' => 0.00,
        'marketing_consent' => false,
        'newsletter_subscription' => false,
        'address_count' => 0,
        'order_count' => 0,
        'review_count' => 0,
        'wishlist_count' => 0,
    ];

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(config('auth.providers.users.model'));
    }

    public function addresses(): HasMany
    {
        return $this->hasMany(Address::class, 'user_id', 'user_id');
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'user_id', 'user_id');
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(ProductReview::class, 'user_id', 'user_id');
    }

    public function notes(): HasMany
    {
        return $this->hasMany(CustomerNote::class);
    }

    // Accessors
    public function getFullNameAttribute(): string
    {
        return trim($this->first_name . ' ' . $this->last_name);
    }

    public function getDisplayNameAttribute(): string
    {
        if ($this->company_name && $this->customer_type->hasBusinessFields()) {
            return $this->company_name;
        }
        return $this->full_name;
    }

    public function getIsActiveAttribute(): bool
    {
        return $this->status->isActive();
    }

    public function getCanOrderAttribute(): bool
    {
        return $this->status->canOrder();
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', CustomerStatus::ACTIVE);
    }

    public function scopeByType($query, CustomerType $type)
    {
        return $query->where('customer_type', $type);
    }

    public function scopeSearch($query, string $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('first_name', 'like', "%{$search}%")
                ->orWhere('last_name', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%")
                ->orWhere('customer_number', 'like', "%{$search}%");
        });
    }

    // Methods
    public function activate(): bool
    {
        return $this->update(['status' => CustomerStatus::ACTIVE]);
    }

    public function deactivate(): bool
    {
        return $this->update(['status' => CustomerStatus::INACTIVE]);
    }

    public function addLoyaltyPoints(int $points): bool
    {
        $this->loyalty_points += $points;
        return $this->save();
    }

    public function deductLoyaltyPoints(int $points): bool
    {
        if ($this->loyalty_points >= $points) {
            $this->loyalty_points -= $points;
            return $this->save();
        }
        return false;
    }
}
