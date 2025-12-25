<?php

namespace Fereydooni\Shopping\app\Models;

use Fereydooni\Shopping\app\Enums\CustomerPreferenceType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class CustomerPreference extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The table associated with the model.
     */
    protected $table = 'customer_preferences';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'customer_id',
        'preference_key',
        'preference_value',
        'preference_type',
        'is_active',
        'description',
        'metadata',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'preference_type' => CustomerPreferenceType::class,
        'is_active' => 'boolean',
        'metadata' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * The attributes that should be hidden for serialization.
     */
    protected $hidden = [
        'deleted_at',
    ];

    /**
     * Get the customer that owns the preference.
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Scope a query to only include active preferences.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to only include inactive preferences.
     */
    public function scopeInactive($query)
    {
        return $query->where('is_active', false);
    }

    /**
     * Scope a query to filter by customer ID.
     */
    public function scopeByCustomer($query, int $customerId)
    {
        return $query->where('customer_id', $customerId);
    }

    /**
     * Scope a query to filter by preference key.
     */
    public function scopeByKey($query, string $key)
    {
        return $query->where('preference_key', $key);
    }

    /**
     * Scope a query to filter by preference type.
     */
    public function scopeByType($query, CustomerPreferenceType $type)
    {
        return $query->where('preference_type', $type);
    }

    /**
     * Scope a query to search preferences.
     */
    public function scopeSearch($query, string $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('preference_key', 'like', "%{$search}%")
                ->orWhere('preference_value', 'like', "%{$search}%")
                ->orWhere('description', 'like', "%{$search}%");
        });
    }

    /**
     * Get the casted preference value.
     */
    public function getCastedValueAttribute(): mixed
    {
        if (! $this->preference_type) {
            return $this->preference_value;
        }

        return $this->preference_type->castValue($this->preference_value);
    }

    /**
     * Set the preference value with proper casting.
     */
    public function setPreferenceValueAttribute($value): void
    {
        if (isset($this->attributes['preference_type']) && $this->attributes['preference_type']) {
            $type = CustomerPreferenceType::from($this->attributes['preference_type']);
            $this->attributes['preference_value'] = $type->castValue($value);
        } else {
            $this->attributes['preference_value'] = $value;
        }
    }

    /**
     * Activate the preference.
     */
    public function activate(): bool
    {
        return $this->update(['is_active' => true]);
    }

    /**
     * Deactivate the preference.
     */
    public function deactivate(): bool
    {
        return $this->update(['is_active' => false]);
    }

    /**
     * Check if the preference is active.
     */
    public function isActive(): bool
    {
        return $this->is_active;
    }

    /**
     * Check if the preference is inactive.
     */
    public function isInactive(): bool
    {
        return ! $this->is_active;
    }

    /**
     * Get the preference as an array.
     */
    public function toArray(): array
    {
        $array = parent::toArray();
        $array['casted_value'] = $this->casted_value;

        return $array;
    }

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        // Auto-cast preference value when setting preference type
        static::saving(function ($model) {
            if ($model->isDirty('preference_type') && $model->preference_value) {
                $model->preference_value = $model->preference_type->castValue($model->preference_value);
            }
        });
    }
}
