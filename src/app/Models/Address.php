<?php

namespace Fereydooni\Shopping\app\Models;

use Fereydooni\Shopping\app\Enums\AddressType;
use Fereydooni\Shopping\app\Traits\HasGeographicData;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Address extends Model
{
    use HasGeographicData;

    protected $fillable = [
        'user_id',
        'type',
        'first_name',
        'last_name',
        'company_name',
        'address_line_1',
        'address_line_2',
        'phone',
        'email',
        'postal_code',
        'country_id',
        'province_id',
        'county_id',
        'city_id',
        'village_id',
        'full_name',
        'street',
        'city',
        'state',
        'country',
        'is_default',
    ];

    protected $casts = [
        'type' => AddressType::class,
        'is_default' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(config('shopping.user_model', 'App\Models\User'));
    }

    public function country(): BelongsTo
    {
        $countryModel = config('shopping.geographic_models.country_model', 'App\Models\Country');

        return $this->belongsTo($countryModel);
    }

    public function province(): BelongsTo
    {
        $provinceModel = config('shopping.geographic_models.province_model', 'App\Models\Province');

        return $this->belongsTo($provinceModel);
    }

    public function county(): BelongsTo
    {
        $countyModel = config('shopping.geographic_models.county_model', 'App\Models\County');

        return $this->belongsTo($countyModel);
    }

    public function city(): BelongsTo
    {
        $cityModel = config('shopping.geographic_models.city_model', 'App\Models\City');

        return $this->belongsTo($cityModel);
    }

    public function village(): BelongsTo
    {
        $villageModel = config('shopping.geographic_models.village_model', 'App\Models\Village');

        return $this->belongsTo($villageModel);
    }

    public function shippingOrders(): HasMany
    {
        return $this->hasMany(Order::class, 'shipping_address_id');
    }

    public function billingOrders(): HasMany
    {
        return $this->hasMany(Order::class, 'billing_address_id');
    }

    public function getFullNameAttribute(): string
    {
        return trim($this->first_name.' '.$this->last_name);
    }

    public function getFullAddressAttribute(): string
    {
        $parts = [
            $this->address_line_1,
            $this->address_line_2,
            $this->city?->name ?? $this->city,
            $this->state,
            $this->postal_code,
            $this->country?->name ?? $this->country,
        ];

        return implode(', ', array_filter($parts));
    }

    public function getFormattedAddressAttribute(): string
    {
        $lines = [];

        if ($this->company_name) {
            $lines[] = $this->company_name;
        }

        $lines[] = $this->full_name;
        $lines[] = $this->address_line_1;

        if ($this->address_line_2) {
            $lines[] = $this->address_line_2;
        }

        $cityState = [];
        if ($this->city?->name ?? $this->city) {
            $cityState[] = $this->city?->name ?? $this->city;
        }
        if ($this->state) {
            $cityState[] = $this->state;
        }
        if ($this->postal_code) {
            $cityState[] = $this->postal_code;
        }
        if (! empty($cityState)) {
            $lines[] = implode(', ', $cityState);
        }

        if ($this->country?->name ?? $this->country) {
            $lines[] = $this->country?->name ?? $this->country;
        }

        if ($this->phone) {
            $lines[] = 'Phone: '.$this->phone;
        }

        if ($this->email) {
            $lines[] = 'Email: '.$this->email;
        }

        return implode("\n", $lines);
    }
}
