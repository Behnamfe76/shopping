<?php

namespace Fereydooni\Shopping\app\Models;

use Laravel\Scout\Searchable;
use Illuminate\Database\Eloquent\Model;
use Fereydooni\Shopping\app\Enums\Gender;
use Illuminate\Database\Eloquent\SoftDeletes;
use Fereydooni\Shopping\app\Enums\CustomerType;
use Modules\Core\Traits\HasEnhancedActivityLog;
use Fereydooni\Unixtime\HasTimestampEquivalents;
use Fereydooni\Shopping\app\Enums\CustomerStatus;
use Fereydooni\Shopping\app\Traits\HasUniqueColumn;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Customer extends Model
{
    use HasTimestampEquivalents;
    use HasUniqueColumn;
    use SoftDeletes;
    use Searchable;
    use HasEnhancedActivityLog;

    // Activity log configuration flags
    protected bool $logUserInfo = true;
    protected bool $logRequestMetadata = true;
    protected bool $logBrowserInfo = true;
    protected bool $logModelSnapshot = true;
    protected ?string $activityLogName = 'users';
    protected ?string $activityDescription = 'User has been {event}';

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
        'type' => 'alphanumeric',
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

    public static function toScoutModelSettings(): array
    {
        return [
            self::class => [
                'collection-schema' => self::getTypesenseCollectionSchema(),
                'search-parameters' => [
                    'query_by' => implode(',', self::searchableFields()),
                ],
            ],
        ];
    }

    public static function searchableFields(): array
    {
        return [
            'first_name',
            'last_name',
            'email',
            'phone'
        ];
    }

    /**
     * Get the indexable data array for the model.
     */
    public function toSearchableArray(): array
    {
        return [
            'id' => (string) $this->id,
            'id_numeric' => $this->id,
            'name' => $this->full_name,
            'email' => $this->email,
            'phone' => (string) $this->phone,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'customer_number' => $this->customer_number,
            'customer_type' => $this->customer_type?->value,
            'status' => $this->status?->value,
            'company_name' => $this->company_name,
            'gender' => $this->gender?->value,
            'tax_id' => $this->tax_id,
            'loyalty_points' => $this->loyalty_points ?? 0,
            'total_orders' => $this->total_orders ?? 0,
            'total_spent' => (float) $this->total_spent ?? 0.0,
            'average_order_value' => (float) $this->average_order_value ?? 0.0,
            'date_of_birth' => $this->date_of_birth?->timestamp,
            'last_order_date' => $this->last_order_date?->timestamp,
            'first_order_date' => $this->first_order_date?->timestamp,
            'preferred_payment_method' => $this->preferred_payment_method,
            'preferred_shipping_method' => $this->preferred_shipping_method,
            'marketing_consent' => $this->marketing_consent ?? false,
            'newsletter_subscription' => $this->newsletter_subscription ?? false,
            'tags' => $this->tags ?? [],
            'address_count' => $this->address_count ?? 0,
            'order_count' => $this->order_count ?? 0,
            'review_count' => $this->review_count ?? 0,
            'wishlist_count' => $this->wishlist_count ?? 0,
            'created_at' => $this->created_at?->timestamp,
            'updated_at' => $this->updated_at?->timestamp,
        ];
    }

    /**
     * Define the Typesense collection schema.
     */
    public static function getTypesenseCollectionSchema(): array
    {
        return [
            'fields' => [
                [
                    'name' => 'id',
                    'type' => 'string',
                    'facet' => false,
                ],
                [
                    'name' => 'id_numeric',
                    'type' => 'int64',
                    'facet' => false,
                ],
                [
                    'name' => 'name',
                    'type' => 'string',
                    'facet' => true,
                ],
                [
                    'name' => 'first_name',
                    'type' => 'string',
                    'facet' => false,
                    'optional' => true,
                ],
                [
                    'name' => 'last_name',
                    'type' => 'string',
                    'facet' => false,
                    'optional' => true,
                ],
                [
                    'name' => 'email',
                    'type' => 'string',
                    'facet' => false,
                    'optional' => true,
                ],
                [
                    'name' => 'phone',
                    'type' => 'string',
                    'facet' => false,
                    'optional' => true,
                ],
                [
                    'name' => 'customer_number',
                    'type' => 'string',
                    'facet' => false,
                    'optional' => true,
                ],
                [
                    'name' => 'customer_type',
                    'type' => 'string',
                    'facet' => true,
                    'optional' => true,
                ],
                [
                    'name' => 'status',
                    'type' => 'string',
                    'facet' => true,
                    'optional' => true,
                ],
                [
                    'name' => 'company_name',
                    'type' => 'string',
                    'facet' => false,
                    'optional' => true,
                ],
                [
                    'name' => 'gender',
                    'type' => 'string',
                    'facet' => true,
                    'optional' => true,
                ],
                [
                    'name' => 'tax_id',
                    'type' => 'string',
                    'facet' => false,
                    'optional' => true,
                ],
                [
                    'name' => 'loyalty_points',
                    'type' => 'int32',
                    'facet' => false,
                ],
                [
                    'name' => 'total_orders',
                    'type' => 'int32',
                    'facet' => false,
                ],
                [
                    'name' => 'total_spent',
                    'type' => 'float',
                    'facet' => false,
                ],
                [
                    'name' => 'average_order_value',
                    'type' => 'float',
                    'facet' => false,
                ],
                [
                    'name' => 'date_of_birth',
                    'type' => 'int64',
                    'facet' => false,
                    'optional' => true,
                ],
                [
                    'name' => 'last_order_date',
                    'type' => 'int64',
                    'facet' => false,
                    'optional' => true,
                ],
                [
                    'name' => 'first_order_date',
                    'type' => 'int64',
                    'facet' => false,
                    'optional' => true,
                ],
                [
                    'name' => 'preferred_payment_method',
                    'type' => 'string',
                    'facet' => true,
                    'optional' => true,
                ],
                [
                    'name' => 'preferred_shipping_method',
                    'type' => 'string',
                    'facet' => true,
                    'optional' => true,
                ],
                [
                    'name' => 'marketing_consent',
                    'type' => 'bool',
                    'facet' => true,
                ],
                [
                    'name' => 'newsletter_subscription',
                    'type' => 'bool',
                    'facet' => true,
                ],
                [
                    'name' => 'tags',
                    'type' => 'string[]',
                    'facet' => false,
                ],
                [
                    'name' => 'address_count',
                    'type' => 'int32',
                    'facet' => false,
                ],
                [
                    'name' => 'order_count',
                    'type' => 'int32',
                    'facet' => false,
                ],
                [
                    'name' => 'review_count',
                    'type' => 'int32',
                    'facet' => false,
                ],
                [
                    'name' => 'wishlist_count',
                    'type' => 'int32',
                    'facet' => false,
                ],
                [
                    'name' => 'created_at',
                    'type' => 'int64',
                    'facet' => false,
                ],
                [
                    'name' => 'updated_at',
                    'type' => 'int64',
                    'facet' => false,
                ]
            ],
            'default_sorting_field' => 'created_at',
        ];
    }

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
    public function getTagsAttribute(): array
    {
        $tags = $this->attributes['tags'] ?? null;

        if (is_string($tags)) {
            return str_getcsv(trim($tags, '"')) ?? [];
        }

        return [];
    }

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
