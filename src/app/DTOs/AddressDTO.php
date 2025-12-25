<?php

namespace Fereydooni\Shopping\app\DTOs;

use Fereydooni\Shopping\app\Enums\AddressType;
use Illuminate\Support\Carbon;
use Spatie\LaravelData\Data;

class AddressDTO extends Data
{
    public function __construct(
        public ?int $id,
        public ?int $user_id,
        public string $first_name,
        public string $last_name,
        public ?string $company_name,
        public string $address_line_1,
        public ?string $address_line_2,
        public string $postal_code,
        public ?string $phone,
        public ?string $email,
        public AddressType $type,
        public bool $is_default,
        public ?Carbon $created_at,
        public ?Carbon $updated_at,

        // Geographic relationships
        public ?int $country_id,
        public ?int $province_id,
        public ?int $county_id,
        public ?int $city_id,
        public ?int $village_id,

        // Legacy fields for backward compatibility
        public ?string $full_name = null,
        public ?string $street = null,
        public ?string $city = null,
        public ?string $state = null,
        public ?string $country = null,

        // Computed fields
        public ?string $full_address = null,
        public ?string $formatted_address = null,

        // Geographic data objects
        public mixed $country_data = null,
        public mixed $province_data = null,
        public mixed $county_data = null,
        public mixed $city_data = null,
        public mixed $village_data = null,
    ) {}

    public static function fromModel($address): static
    {
        return new static(
            id: $address->id,
            user_id: $address->user_id,
            first_name: $address->first_name,
            last_name: $address->last_name,
            company_name: $address->company_name,
            address_line_1: $address->address_line_1,
            address_line_2: $address->address_line_2,
            postal_code: $address->postal_code,
            phone: $address->phone,
            email: $address->email,
            type: $address->type,
            is_default: $address->is_default,
            created_at: $address->created_at,
            updated_at: $address->updated_at,

            // Geographic relationships
            country_id: $address->country_id,
            province_id: $address->province_id,
            county_id: $address->county_id,
            city_id: $address->city_id,
            village_id: $address->village_id,

            // Legacy fields
            full_name: $address->full_name,
            street: $address->street,
            city: $address->city,
            state: $address->state,
            country: $address->country,

            // Computed fields
            full_address: $address->full_address,
            formatted_address: $address->formatted_address,

            // Geographic data objects
            country_data: $address->country,
            province_data: $address->province,
            county_data: $address->county,
            city_data: $address->city,
            village_data: $address->village,
        );
    }

    public static function rules(): array
    {
        return [
            'user_id' => 'required|integer|exists:users,id',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'company_name' => 'nullable|string|max:255',
            'address_line_1' => 'required|string|max:255',
            'address_line_2' => 'nullable|string|max:255',
            'postal_code' => 'required|string|max:20',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'type' => 'required|in:'.implode(',', array_column(AddressType::cases(), 'value')),
            'is_default' => 'boolean',

            // Geographic relationships
            'country_id' => 'nullable|integer|exists:'.config('shopping.geographic_models.country_model', 'countries').',id',
            'province_id' => 'nullable|integer|exists:'.config('shopping.geographic_models.province_model', 'provinces').',id',
            'county_id' => 'nullable|integer|exists:'.config('shopping.geographic_models.county_model', 'counties').',id',
            'city_id' => 'nullable|integer|exists:'.config('shopping.geographic_models.city_model', 'cities').',id',
            'village_id' => 'nullable|integer|exists:'.config('shopping.geographic_models.village_model', 'villages').',id',

            // Legacy fields
            'full_name' => 'nullable|string|max:255',
            'street' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'state' => 'nullable|string|max:255',
            'country' => 'nullable|string|max:255',
        ];
    }

    public static function messages(): array
    {
        return [
            'user_id.required' => 'User ID is required',
            'user_id.exists' => 'Selected user does not exist',
            'first_name.required' => 'First name is required',
            'first_name.max' => 'First name cannot exceed 255 characters',
            'last_name.required' => 'Last name is required',
            'last_name.max' => 'Last name cannot exceed 255 characters',
            'company_name.max' => 'Company name cannot exceed 255 characters',
            'address_line_1.required' => 'Address line 1 is required',
            'address_line_1.max' => 'Address line 1 cannot exceed 255 characters',
            'address_line_2.max' => 'Address line 2 cannot exceed 255 characters',
            'postal_code.required' => 'Postal code is required',
            'postal_code.max' => 'Postal code cannot exceed 20 characters',
            'phone.max' => 'Phone number cannot exceed 20 characters',
            'email.email' => 'Email must be a valid email address',
            'email.max' => 'Email cannot exceed 255 characters',
            'type.required' => 'Address type is required',
            'type.in' => 'Invalid address type selected',
            'is_default.boolean' => 'Default status must be true or false',

            // Geographic validation messages
            'country_id.exists' => 'Selected country does not exist',
            'province_id.exists' => 'Selected province does not exist',
            'county_id.exists' => 'Selected county does not exist',
            'city_id.exists' => 'Selected city does not exist',
            'village_id.exists' => 'Selected village does not exist',

            // Legacy field messages
            'full_name.max' => 'Full name cannot exceed 255 characters',
            'street.max' => 'Street cannot exceed 255 characters',
            'city.max' => 'City cannot exceed 255 characters',
            'state.max' => 'State cannot exceed 255 characters',
            'country.max' => 'Country cannot exceed 255 characters',
        ];
    }
}
