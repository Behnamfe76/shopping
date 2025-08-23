<?php

namespace Fereydooni\Shopping\App\DTOs;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Attributes\Validation\Email;
use Spatie\LaravelData\Attributes\Validation\Url;
use Spatie\LaravelData\Attributes\Validation\Numeric;
use Spatie\LaravelData\Attributes\Validation\Min;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Date;
use Spatie\LaravelData\Attributes\Validation\Nullable;
use Spatie\LaravelData\Attributes\Validation\StringType;
use Spatie\LaravelData\Attributes\Validation\IntegerType;
use Spatie\LaravelData\Attributes\Validation\FloatType;
use Spatie\LaravelData\Attributes\Validation\ArrayType;
use Spatie\LaravelData\Attributes\Validation\In;
use Spatie\LaravelData\Attributes\Validation\Regex;
use Spatie\LaravelData\Attributes\Validation\Timezone;
use Spatie\LaravelData\Attributes\Validation\Json;
use Spatie\LaravelData\Attributes\Validation\Unique;
use Spatie\LaravelData\Attributes\Validation\Exists;
use Spatie\LaravelData\Attributes\Validation\RequiredIf;
use Spatie\LaravelData\Attributes\Validation\ProhibitedIf;
use Fereydooni\Shopping\App\Enums\LocationType;
use Fereydooni\Shopping\App\Enums\Country;
use Fereydooni\Shopping\App\Models\ProviderLocation;
use Fereydooni\Shopping\App\Models\Provider;
use Carbon\Carbon;

class ProviderLocationDTO extends Data
{
    public function __construct(
        #[IntegerType, Nullable]
        public ?int $id = null,

        #[IntegerType, Exists(Provider::class, 'id')]
        public int $provider_id,

        #[StringType, Min(2), Max(255)]
        public string $location_name,

        #[StringType, Min(5), Max(500)]
        public string $address,

        #[StringType, Min(2), Max(100)]
        public string $city,

        #[StringType, Min(2), Max(100)]
        public string $state,

        #[StringType, Min(3), Max(20)]
        public string $postal_code,

        #[In(Country::class)]
        public string $country,

        #[StringType, Min(10), Max(20), Regex('/^[\+]?[1-9][\d]{0,15}$/')]
        public string $phone,

        #[Email, Nullable]
        public ?string $email = null,

        #[Url, Nullable]
        public ?string $website = null,

        #[StringType, Nullable]
        public bool $is_primary = false,

        #[StringType, Nullable]
        public bool $is_active = true,

        #[In(LocationType::class)]
        public string $location_type = LocationType::OFFICE,

        #[Json, Nullable]
        public ?array $operating_hours = null,

        #[Timezone, Nullable]
        public ?string $timezone = null,

        #[FloatType, Min(-90), Max(90), Nullable]
        public ?float $latitude = null,

        #[FloatType, Min(-180), Max(180), Nullable]
        public ?float $longitude = null,

        #[StringType, Min(2), Max(255), Nullable]
        public ?string $contact_person = null,

        #[StringType, Min(10), Max(20), Regex('/^[\+]?[1-9][\d]{0,15}$/'), Nullable]
        public ?string $contact_phone = null,

        #[Email, Nullable]
        public ?string $contact_email = null,

        #[StringType, Max(1000), Nullable]
        public ?string $notes = null,

        #[Date, Nullable]
        public ?string $created_at = null,

        #[Date, Nullable]
        public ?string $updated_at = null,
    ) {
        // Validate primary location constraints
        if ($this->is_primary) {
            $this->validatePrimaryLocation();
        }

        // Validate coordinates consistency
        if (($this->latitude !== null && $this->longitude === null) ||
            ($this->latitude === null && $this->longitude !== null)) {
            throw new \InvalidArgumentException('Both latitude and longitude must be provided together');
        }

        // Validate operating hours structure
        if ($this->operating_hours !== null) {
            $this->validateOperatingHours();
        }
    }

    public static function rules(): array
    {
        return [
            'provider_id' => ['required', 'integer', 'exists:providers,id'],
            'location_name' => ['required', 'string', 'min:2', 'max:255'],
            'address' => ['required', 'string', 'min:5', 'max:500'],
            'city' => ['required', 'string', 'min:2', 'max:100'],
            'state' => ['required', 'string', 'min:2', 'max:100'],
            'postal_code' => ['required', 'string', 'min:3', 'max:20'],
            'country' => ['required', 'string', 'in:' . implode(',', Country::values())],
            'phone' => ['required', 'string', 'min:10', 'max:20', 'regex:/^[\+]?[1-9][\d]{0,15}$/'],
            'email' => ['nullable', 'email', 'max:255'],
            'website' => ['nullable', 'url', 'max:255'],
            'is_primary' => ['boolean'],
            'is_active' => ['boolean'],
            'location_type' => ['required', 'string', 'in:' . implode(',', LocationType::values())],
            'operating_hours' => ['nullable', 'array'],
            'timezone' => ['nullable', 'string', 'timezone'],
            'latitude' => ['nullable', 'numeric', 'min:-90', 'max:90'],
            'longitude' => ['nullable', 'numeric', 'min:-180', 'max:180'],
            'contact_person' => ['nullable', 'string', 'min:2', 'max:255'],
            'contact_phone' => ['nullable', 'string', 'min:10', 'max:20', 'regex:/^[\+]?[1-9][\d]{0,15}$/'],
            'contact_email' => ['nullable', 'email', 'max:255'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public static function messages(): array
    {
        return [
            'provider_id.required' => 'Provider ID is required.',
            'provider_id.exists' => 'The selected provider does not exist.',
            'location_name.required' => 'Location name is required.',
            'location_name.min' => 'Location name must be at least 2 characters.',
            'location_name.max' => 'Location name cannot exceed 255 characters.',
            'address.required' => 'Address is required.',
            'address.min' => 'Address must be at least 5 characters.',
            'address.max' => 'Address cannot exceed 500 characters.',
            'city.required' => 'City is required.',
            'city.min' => 'City must be at least 2 characters.',
            'city.max' => 'City cannot exceed 100 characters.',
            'state.required' => 'State is required.',
            'state.min' => 'State must be at least 2 characters.',
            'state.max' => 'State cannot exceed 100 characters.',
            'postal_code.required' => 'Postal code is required.',
            'postal_code.min' => 'Postal code must be at least 3 characters.',
            'postal_code.max' => 'Postal code cannot exceed 20 characters.',
            'country.required' => 'Country is required.',
            'country.in' => 'The selected country is invalid.',
            'phone.required' => 'Phone number is required.',
            'phone.min' => 'Phone number must be at least 10 characters.',
            'phone.max' => 'Phone number cannot exceed 20 characters.',
            'phone.regex' => 'Phone number format is invalid.',
            'email.email' => 'Email format is invalid.',
            'email.max' => 'Email cannot exceed 255 characters.',
            'website.url' => 'Website format is invalid.',
            'website.max' => 'Website cannot exceed 255 characters.',
            'location_type.required' => 'Location type is required.',
            'location_type.in' => 'The selected location type is invalid.',
            'timezone.timezone' => 'The selected timezone is invalid.',
            'latitude.numeric' => 'Latitude must be a number.',
            'latitude.min' => 'Latitude must be between -90 and 90.',
            'latitude.max' => 'Latitude must be between -90 and 90.',
            'longitude.numeric' => 'Longitude must be a number.',
            'longitude.min' => 'Longitude must be between -180 and 180.',
            'longitude.max' => 'Longitude must be between -180 and 180.',
            'contact_person.min' => 'Contact person name must be at least 2 characters.',
            'contact_person.max' => 'Contact person name cannot exceed 255 characters.',
            'contact_phone.min' => 'Contact phone must be at least 10 characters.',
            'contact_phone.max' => 'Contact phone cannot exceed 20 characters.',
            'contact_phone.regex' => 'Contact phone format is invalid.',
            'contact_email.email' => 'Contact email format is invalid.',
            'contact_email.max' => 'Contact email cannot exceed 255 characters.',
            'notes.max' => 'Notes cannot exceed 1000 characters.',
        ];
    }

    public static function fromModel(ProviderLocation $providerLocation): static
    {
        return new static(
            id: $providerLocation->id,
            provider_id: $providerLocation->provider_id,
            location_name: $providerLocation->location_name,
            address: $providerLocation->address,
            city: $providerLocation->city,
            state: $providerLocation->state,
            postal_code: $providerLocation->postal_code,
            country: $providerLocation->country,
            phone: $providerLocation->phone,
            email: $providerLocation->email,
            website: $providerLocation->website,
            is_primary: $providerLocation->is_primary,
            is_active: $providerLocation->is_active,
            location_type: $providerLocation->location_type,
            operating_hours: $providerLocation->operating_hours,
            timezone: $providerLocation->timezone,
            latitude: $providerLocation->latitude,
            longitude: $providerLocation->longitude,
            contact_person: $providerLocation->contact_person,
            contact_phone: $providerLocation->contact_phone,
            contact_email: $providerLocation->contact_email,
            notes: $providerLocation->notes,
            created_at: $providerLocation->created_at?->toDateString(),
            updated_at: $providerLocation->updated_at?->toDateString(),
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'provider_id' => $this->provider_id,
            'location_name' => $this->location_name,
            'address' => $this->address,
            'city' => $this->city,
            'state' => $this->state,
            'postal_code' => $this->postal_code,
            'country' => $this->country,
            'phone' => $this->phone,
            'email' => $this->email,
            'website' => $this->website,
            'is_primary' => $this->is_primary,
            'is_active' => $this->is_active,
            'location_type' => $this->location_type,
            'operating_hours' => $this->operating_hours,
            'timezone' => $this->timezone,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'contact_person' => $this->contact_person,
            'contact_phone' => $this->contact_phone,
            'contact_email' => $this->contact_email,
            'notes' => $this->notes,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }

    public function getFullAddress(): string
    {
        $parts = [
            $this->address,
            $this->city,
            $this->state,
            $this->postal_code,
            Country::from($this->country)->name(),
        ];

        return implode(', ', array_filter($parts));
    }

    public function getCoordinates(): ?array
    {
        if ($this->latitude === null || $this->longitude === null) {
            return null;
        }

        return [
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
        ];
    }

    public function hasCoordinates(): bool
    {
        return $this->latitude !== null && $this->longitude !== null;
    }

    public function getLocationTypeLabel(): string
    {
        return LocationType::from($this->location_type)->label();
    }

    public function getCountryName(): string
    {
        return Country::from($this->country)->name();
    }

    public function getCountryFlag(): string
    {
        return Country::from($this->country)->flag();
    }

    public function getCountryCurrency(): string
    {
        return Country::from($this->country)->currency();
    }

    public function isPrimary(): bool
    {
        return $this->is_primary;
    }

    public function isActive(): bool
    {
        return $this->is_active;
    }

    public function isHeadquarters(): bool
    {
        return $this->location_type === LocationType::HEADQUARTERS->value;
    }

    public function isWarehouse(): bool
    {
        return $this->location_type === LocationType::WAREHOUSE->value;
    }

    public function isStore(): bool
    {
        return $this->location_type === LocationType::STORE->value;
    }

    public function isOffice(): bool
    {
        return $this->location_type === LocationType::OFFICE->value;
    }

    public function isFactory(): bool
    {
        return $this->location_type === LocationType::FACTORY->value;
    }

    public function isDistributionCenter(): bool
    {
        return $this->location_type === LocationType::DISTRIBUTION_CENTER->value;
    }

    public function isRetailOutlet(): bool
    {
        return $this->location_type === LocationType::RETAIL_OUTLET->value;
    }

    public function isServiceCenter(): bool
    {
        return $this->location_type === LocationType::SERVICE_CENTER->value;
    }

    public function isOther(): bool
    {
        return $this->location_type === LocationType::OTHER->value;
    }

    private function validatePrimaryLocation(): void
    {
        // Additional validation logic for primary locations can be added here
        // For example, ensuring only one primary location per provider
    }

    private function validateOperatingHours(): void
    {
        $validDays = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];

        foreach ($this->operating_hours as $day => $hours) {
            if (!in_array(strtolower($day), $validDays)) {
                throw new \InvalidArgumentException("Invalid day of week: {$day}");
            }

            if (!is_array($hours)) {
                throw new \InvalidArgumentException("Operating hours for {$day} must be an array");
            }

            // Validate time format (HH:MM)
            foreach ($hours as $time) {
                if (!preg_match('/^([01]?[0-9]|2[0-3]):[0-5][0-9]$/', $time)) {
                    throw new \InvalidArgumentException("Invalid time format for {$day}: {$time}");
                }
            }
        }
    }

    public function getOperatingHoursForDay(string $day): ?array
    {
        $day = strtolower($day);
        return $this->operating_hours[$day] ?? null;
    }

    public function isOpenOnDay(string $day): bool
    {
        $day = strtolower($day);
        return isset($this->operating_hours[$day]) && !empty($this->operating_hours[$day]);
    }

    public function getFormattedPhone(): string
    {
        // Basic phone formatting - can be enhanced with more sophisticated logic
        $phone = preg_replace('/[^0-9+]/', '', $this->phone);

        if (str_starts_with($phone, '+')) {
            return $phone;
        }

        if (strlen($phone) === 10) {
            return '+1' . $phone;
        }

        return $phone;
    }

    public function getFormattedContactPhone(): ?string
    {
        if ($this->contact_phone === null) {
            return null;
        }

        return $this->getFormattedPhone();
    }

    public function getDistanceFrom(float $latitude, float $longitude): ?float
    {
        if (!$this->hasCoordinates()) {
            return null;
        }

        return $this->calculateDistance($latitude, $longitude);
    }

    private function calculateDistance(float $lat1, float $lon1): float
    {
        $lat2 = $this->latitude;
        $lon2 = $this->longitude;

        $theta = $lon1 - $lon2;
        $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +
                cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
        $dist = acos($dist);
        $dist = rad2deg($dist);
        $miles = $dist * 60 * 1.1515;

        return $miles;
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
}
