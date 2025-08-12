<?php

namespace Fereydooni\Shopping\app\DTOs;

use Spatie\LaravelData\Data;
use Illuminate\Support\Carbon;
use Fereydooni\Shopping\app\Enums\AddressType;

class AddressDTO extends Data
{
    public function __construct(
        public ?int $id,
        public ?int $user_id,
        public string $first_name,
        public string $last_name,
        public string $company_name,
        public string $address_line_1,
        public ?string $address_line_2,
        public string $city,
        public string $state,
        public string $postal_code,
        public string $country,
        public string $phone,
        public ?string $email,
        public AddressType $type,
        public bool $is_default,
        public ?Carbon $created_at,
        public ?Carbon $updated_at,
        public ?string $full_name = null,
        public ?string $full_address = null,
        public ?string $formatted_address = null,
    ) {
    }

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
            city: $address->city,
            state: $address->state,
            postal_code: $address->postal_code,
            country: $address->country,
            phone: $address->phone,
            email: $address->email,
            type: $address->type,
            is_default: $address->is_default,
            created_at: $address->created_at,
            updated_at: $address->updated_at,
            full_name: $address->first_name . ' ' . $address->last_name,
            full_address: self::buildFullAddress($address),
            formatted_address: self::buildFormattedAddress($address),
        );
    }

    private static function buildFullAddress($address): string
    {
        $parts = [
            $address->address_line_1,
            $address->address_line_2,
            $address->city,
            $address->state,
            $address->postal_code,
            $address->country
        ];

        return implode(', ', array_filter($parts));
    }

    private static function buildFormattedAddress($address): string
    {
        $lines = [];

        if ($address->company_name) {
            $lines[] = $address->company_name;
        }

        $lines[] = $address->first_name . ' ' . $address->last_name;
        $lines[] = $address->address_line_1;

        if ($address->address_line_2) {
            $lines[] = $address->address_line_2;
        }

        $lines[] = $address->city . ', ' . $address->state . ' ' . $address->postal_code;
        $lines[] = $address->country;

        if ($address->phone) {
            $lines[] = 'Phone: ' . $address->phone;
        }

        if ($address->email) {
            $lines[] = 'Email: ' . $address->email;
        }

        return implode("\n", $lines);
    }

    public static function rules(): array
    {
        return [
            'user_id' => 'required|integer|exists:users,id',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'company_name' => 'required|string|max:255',
            'address_line_1' => 'required|string|max:255',
            'address_line_2' => 'nullable|string|max:255',
            'city' => 'required|string|max:255',
            'state' => 'required|string|max:255',
            'postal_code' => 'required|string|max:20',
            'country' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
            'type' => 'required|in:' . implode(',', array_column(AddressType::cases(), 'value')),
            'is_default' => 'boolean',
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
            'company_name.required' => 'Company name is required',
            'company_name.max' => 'Company name cannot exceed 255 characters',
            'address_line_1.required' => 'Address line 1 is required',
            'address_line_1.max' => 'Address line 1 cannot exceed 255 characters',
            'address_line_2.max' => 'Address line 2 cannot exceed 255 characters',
            'city.required' => 'City is required',
            'city.max' => 'City cannot exceed 255 characters',
            'state.required' => 'State is required',
            'state.max' => 'State cannot exceed 255 characters',
            'postal_code.required' => 'Postal code is required',
            'postal_code.max' => 'Postal code cannot exceed 20 characters',
            'country.required' => 'Country is required',
            'country.max' => 'Country cannot exceed 255 characters',
            'phone.required' => 'Phone number is required',
            'phone.max' => 'Phone number cannot exceed 20 characters',
            'email.email' => 'Email must be a valid email address',
            'email.max' => 'Email cannot exceed 255 characters',
            'type.required' => 'Address type is required',
            'type.in' => 'Invalid address type selected',
        ];
    }
}
