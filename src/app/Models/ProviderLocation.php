<?php

namespace Fereydooni\Shopping\App\Models;

use Fereydooni\Shopping\App\Enums\Country;
use Fereydooni\Shopping\App\Enums\LocationType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProviderLocation extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'provider_locations';

    protected $fillable = [
        'provider_id',
        'location_name',
        'address',
        'city',
        'state',
        'postal_code',
        'country',
        'phone',
        'email',
        'website',
        'is_primary',
        'is_active',
        'location_type',
        'operating_hours',
        'timezone',
        'latitude',
        'longitude',
        'contact_person',
        'contact_phone',
        'contact_email',
        'notes',
    ];

    protected $casts = [
        'provider_id' => 'integer',
        'is_primary' => 'boolean',
        'is_active' => 'boolean',
        'location_type' => LocationType::class,
        'country' => Country::class,
        'operating_hours' => 'array',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    protected $attributes = [
        'is_primary' => false,
        'is_active' => true,
        'location_type' => LocationType::OFFICE,
        'operating_hours' => null,
        'timezone' => 'UTC',
    ];

    protected $hidden = [
        'deleted_at',
    ];

    protected $appends = [
        'full_address',
        'coordinates',
        'location_type_label',
        'country_name',
        'country_flag',
        'country_currency',
    ];

    // Relationships
    public function provider(): BelongsTo
    {
        return $this->belongsTo(Provider::class);
    }

    // Scopes
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeInactive(Builder $query): Builder
    {
        return $query->where('is_active', false);
    }

    public function scopePrimary(Builder $query): Builder
    {
        return $query->where('is_primary', true);
    }

    public function scopeByProvider(Builder $query, int $providerId): Builder
    {
        return $query->where('provider_id', $providerId);
    }

    public function scopeByLocationType(Builder $query, string $locationType): Builder
    {
        return $query->where('location_type', $locationType);
    }

    public function scopeByCountry(Builder $query, string $country): Builder
    {
        return $query->where('country', $country);
    }

    public function scopeByState(Builder $query, string $state): Builder
    {
        return $query->where('state', $state);
    }

    public function scopeByCity(Builder $query, string $city): Builder
    {
        return $query->where('city', $city);
    }

    public function scopeByPostalCode(Builder $query, string $postalCode): Builder
    {
        return $query->where('postal_code', $postalCode);
    }

    public function scopeByPhone(Builder $query, string $phone): Builder
    {
        return $query->where('phone', $phone);
    }

    public function scopeByEmail(Builder $query, string $email): Builder
    {
        return $query->where('email', $email);
    }

    public function scopeByWebsite(Builder $query, string $website): Builder
    {
        return $query->where('website', $website);
    }

    public function scopeByTimezone(Builder $query, string $timezone): Builder
    {
        return $query->where('timezone', $timezone);
    }

    public function scopeWithCoordinates(Builder $query): Builder
    {
        return $query->whereNotNull('latitude')->whereNotNull('longitude');
    }

    public function scopeNearby(Builder $query, float $latitude, float $longitude, float $radius = 10): Builder
    {
        return $query->selectRaw('*,
            ( 3959 * acos( cos( radians(?) ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians(?) ) + sin( radians(?) ) * sin( radians( latitude ) ) ) ) AS distance',
            [$latitude, $longitude, $latitude])
            ->having('distance', '<=', $radius)
            ->orderBy('distance');
    }

    public function scopeSearch(Builder $query, string $search): Builder
    {
        return $query->where(function ($q) use ($search) {
            $q->where('location_name', 'like', "%{$search}%")
                ->orWhere('address', 'like', "%{$search}%")
                ->orWhere('city', 'like', "%{$search}%")
                ->orWhere('state', 'like', "%{$search}%")
                ->orWhere('contact_person', 'like', "%{$search}%")
                ->orWhere('notes', 'like', "%{$search}%");
        });
    }

    // Accessors
    public function getFullAddressAttribute(): string
    {
        $parts = [
            $this->address,
            $this->city,
            $this->state,
            $this->postal_code,
            $this->country_name,
        ];

        return implode(', ', array_filter($parts));
    }

    public function getCoordinatesAttribute(): ?array
    {
        if ($this->latitude === null || $this->longitude === null) {
            return null;
        }

        return [
            'latitude' => (float) $this->latitude,
            'longitude' => (float) $this->longitude,
        ];
    }

    public function getLocationTypeLabelAttribute(): string
    {
        return $this->location_type->label();
    }

    public function getCountryNameAttribute(): string
    {
        return $this->country->name();
    }

    public function getCountryFlagAttribute(): string
    {
        return $this->country->flag();
    }

    public function getCountryCurrencyAttribute(): string
    {
        return $this->country->currency();
    }

    // Methods
    public function isPrimary(): bool
    {
        return $this->is_primary;
    }

    public function isActive(): bool
    {
        return $this->is_active;
    }

    public function hasCoordinates(): bool
    {
        return $this->latitude !== null && $this->longitude !== null;
    }

    public function getDistanceFrom(float $latitude, float $longitude): ?float
    {
        if (! $this->hasCoordinates()) {
            return null;
        }

        return $this->calculateDistance($latitude, $longitude);
    }

    public function getDistanceInKm(float $latitude, float $longitude): ?float
    {
        $miles = $this->getDistanceFrom($latitude, $longitude);

        return $miles ? $miles * 1.609344 : null;
    }

    public function getDistanceInMiles(float $latitude, float $longitude): ?float
    {
        return $this->getDistanceFrom($latitude, $longitude);
    }

    public function getOperatingHoursForDay(string $day): ?array
    {
        $day = strtolower($day);

        return $this->operating_hours[$day] ?? null;
    }

    public function isOpenOnDay(string $day): bool
    {
        $day = strtolower($day);

        return isset($this->operating_hours[$day]) && ! empty($this->operating_hours[$day]);
    }

    public function getFormattedPhone(): string
    {
        $phone = preg_replace('/[^0-9+]/', '', $this->phone);

        if (str_starts_with($phone, '+')) {
            return $phone;
        }

        if (strlen($phone) === 10) {
            return '+1'.$phone;
        }

        return $phone;
    }

    public function getFormattedContactPhone(): ?string
    {
        if ($this->contact_phone === null) {
            return null;
        }

        $phone = preg_replace('/[^0-9+]/', '', $this->contact_phone);

        if (str_starts_with($phone, '+')) {
            return $phone;
        }

        if (strlen($phone) === 10) {
            return '+1'.$phone;
        }

        return $phone;
    }

    public function activate(): bool
    {
        return $this->update(['is_active' => true]);
    }

    public function deactivate(): bool
    {
        return $this->update(['is_active' => false]);
    }

    public function setPrimary(): bool
    {
        // First, unset any existing primary location for this provider
        static::where('provider_id', $this->provider_id)
            ->where('is_primary', true)
            ->update(['is_primary' => false]);

        return $this->update(['is_primary' => true]);
    }

    public function unsetPrimary(): bool
    {
        return $this->update(['is_primary' => false]);
    }

    public function updateCoordinates(float $latitude, float $longitude): bool
    {
        return $this->update([
            'latitude' => $latitude,
            'longitude' => $longitude,
        ]);
    }

    public function updateOperatingHours(array $operatingHours): bool
    {
        return $this->update(['operating_hours' => $operatingHours]);
    }

    public function updateContactInfo(array $contactInfo): bool
    {
        $allowedFields = ['contact_person', 'contact_phone', 'contact_email'];
        $data = array_intersect_key($contactInfo, array_flip($allowedFields));

        return $this->update($data);
    }

    private function calculateDistance(float $lat1, float $lon1): float
    {
        $lat2 = (float) $this->latitude;
        $lon2 = (float) $this->longitude;

        $theta = $lon1 - $lon2;
        $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +
                cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
        $dist = acos($dist);
        $dist = rad2deg($dist);
        $miles = $dist * 60 * 1.1515;

        return $miles;
    }

    // Boot method for model events
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($providerLocation) {
            // Ensure only one primary location per provider
            if ($providerLocation->is_primary) {
                static::where('provider_id', $providerLocation->provider_id)
                    ->where('is_primary', true)
                    ->update(['is_primary' => false]);
            }
        });

        static::updating(function ($providerLocation) {
            // Ensure only one primary location per provider
            if ($providerLocation->isDirty('is_primary') && $providerLocation->is_primary) {
                static::where('provider_id', $providerLocation->provider_id)
                    ->where('id', '!=', $providerLocation->id)
                    ->where('is_primary', true)
                    ->update(['is_primary' => false]);
            }
        });
    }
}
