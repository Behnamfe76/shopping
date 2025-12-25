<?php

namespace Fereydooni\Shopping\App\DTOs;

use Fereydooni\Shopping\App\Enums\ProviderStatus;
use Fereydooni\Shopping\App\Enums\ProviderType;
use Fereydooni\Shopping\App\Models\Provider;
use Spatie\LaravelData\Attributes\Validation\ArrayType;
use Spatie\LaravelData\Attributes\Validation\Date;
use Spatie\LaravelData\Attributes\Validation\Email;
use Spatie\LaravelData\Attributes\Validation\FloatType;
use Spatie\LaravelData\Attributes\Validation\In;
use Spatie\LaravelData\Attributes\Validation\IntegerType;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Min;
use Spatie\LaravelData\Attributes\Validation\Nullable;
use Spatie\LaravelData\Attributes\Validation\StringType;
use Spatie\LaravelData\Attributes\Validation\Url;
use Spatie\LaravelData\Data;

class ProviderDTO extends Data
{
    public function __construct(
        #[IntegerType]
        public int $user_id,

        #[StringType, Min(3), Max(50)]
        public string $provider_number,

        #[StringType, Min(2), Max(255)]
        public string $company_name,

        #[StringType, Min(2), Max(255)]
        public string $contact_person,

        #[Email]
        public string $email,

        #[StringType, Min(10), Max(20)]
        public string $phone,

        #[Url, Nullable]
        public ?string $website,

        #[StringType, Min(5), Max(50), Nullable]
        public ?string $tax_id,

        #[StringType, Min(5), Max(100), Nullable]
        public ?string $business_license,

        #[In(ProviderType::class)]
        public string $provider_type,

        #[In(ProviderStatus::class)]
        public string $status,

        #[FloatType, Min(0), Max(5), Nullable]
        public ?float $rating,

        #[IntegerType, Min(0)]
        public int $total_orders,

        #[FloatType, Min(0)]
        public float $total_spent,

        #[FloatType, Min(0)]
        public float $average_order_value,

        #[Date, Nullable]
        public ?string $last_order_date,

        #[Date, Nullable]
        public ?string $first_order_date,

        #[StringType, Max(255), Nullable]
        public ?string $payment_terms,

        #[FloatType, Min(0)]
        public float $credit_limit,

        #[FloatType]
        public float $current_balance,

        #[StringType, Max(255)]
        public string $address,

        #[StringType, Max(100)]
        public string $city,

        #[StringType, Max(100)]
        public string $state,

        #[StringType, Max(20)]
        public string $postal_code,

        #[StringType, Max(100)]
        public string $country,

        #[StringType, Max(255), Nullable]
        public ?string $bank_name = null,

        #[StringType, Max(50), Nullable]
        public ?string $bank_account_number = null,

        #[StringType, Max(20), Nullable]
        public ?string $bank_routing_number = null,

        #[StringType, Max(1000), Nullable]
        public ?string $contact_notes = null,

        #[ArrayType, Nullable]
        public ?array $specializations = null,

        #[ArrayType, Nullable]
        public ?array $certifications = null,

        #[ArrayType, Nullable]
        public ?array $insurance_info = null,

        #[Date, Nullable]
        public ?string $contract_start_date = null,

        #[Date, Nullable]
        public ?string $contract_end_date = null,

        #[FloatType, Min(0), Max(100)]
        public float $commission_rate = 0.0,

        #[FloatType, Min(0), Max(100)]
        public float $discount_rate = 0.0,

        #[ArrayType, Nullable]
        public ?array $shipping_methods = null,

        #[ArrayType, Nullable]
        public ?array $payment_methods = null,

        #[FloatType, Min(0), Max(5), Nullable]
        public ?float $quality_rating = null,

        #[FloatType, Min(0), Max(5), Nullable]
        public ?float $delivery_rating = null,

        #[FloatType, Min(0), Max(5), Nullable]
        public ?float $communication_rating = null,

        #[IntegerType, Min(0), Nullable]
        public ?int $response_time = null,

        #[FloatType, Min(0), Max(100), Nullable]
        public ?float $on_time_delivery_rate = null,

        #[FloatType, Min(0), Max(100), Nullable]
        public ?float $return_rate = null,

        #[FloatType, Min(0), Max(100), Nullable]
        public ?float $defect_rate = null,

        #[Date, Nullable]
        public ?string $created_at = null,

        #[Date, Nullable]
        public ?string $updated_at = null
    ) {}

    public static function fromModel(Provider $provider): self
    {
        return new self(
            user_id: $provider->user_id,
            provider_number: $provider->provider_number,
            company_name: $provider->company_name,
            contact_person: $provider->contact_person,
            email: $provider->email,
            phone: $provider->phone,
            website: $provider->website,
            tax_id: $provider->tax_id,
            business_license: $provider->business_license,
            provider_type: $provider->provider_type,
            status: $provider->status,
            rating: $provider->rating,
            total_orders: $provider->total_orders,
            total_spent: $provider->total_spent,
            average_order_value: $provider->average_order_value,
            last_order_date: $provider->last_order_date?->toDateString(),
            first_order_date: $provider->first_order_date?->toDateString(),
            payment_terms: $provider->payment_terms,
            credit_limit: $provider->credit_limit,
            current_balance: $provider->current_balance,
            address: $provider->address,
            city: $provider->city,
            state: $provider->state,
            postal_code: $provider->postal_code,
            country: $provider->country,
            bank_name: $provider->bank_name,
            bank_account_number: $provider->bank_account_number,
            bank_routing_number: $provider->bank_routing_number,
            contact_notes: $provider->contact_notes,
            specializations: $provider->specializations,
            certifications: $provider->certifications,
            insurance_info: $provider->insurance_info,
            contract_start_date: $provider->contract_start_date?->toDateString(),
            contract_end_date: $provider->contract_end_date?->toDateString(),
            commission_rate: $provider->commission_rate,
            discount_rate: $provider->discount_rate,
            shipping_methods: $provider->shipping_methods,
            payment_methods: $provider->payment_methods,
            quality_rating: $provider->quality_rating,
            delivery_rating: $provider->delivery_rating,
            communication_rating: $provider->communication_rating,
            response_time: $provider->response_time,
            on_time_delivery_rate: $provider->on_time_delivery_rate,
            return_rate: $provider->return_rate,
            defect_rate: $provider->defect_rate,
            created_at: $provider->created_at?->toDateString(),
            updated_at: $provider->updated_at?->toDateString()
        );
    }

    public static function rules(): array
    {
        return [
            'user_id' => 'required|integer|exists:users,id',
            'provider_number' => 'required|string|min:3|max:50|unique:providers,provider_number',
            'company_name' => 'required|string|min:2|max:255',
            'contact_person' => 'required|string|min:2|max:255',
            'email' => 'required|email|unique:providers,email',
            'phone' => 'required|string|min:10|max:20',
            'website' => 'nullable|url|max:255',
            'tax_id' => 'nullable|string|min:5|max:50|unique:providers,tax_id',
            'business_license' => 'nullable|string|min:5|max:100',
            'provider_type' => 'required|string|in:'.implode(',', ProviderType::values()),
            'status' => 'required|string|in:'.implode(',', ProviderStatus::values()),
            'rating' => 'nullable|numeric|min:0|max:5',
            'total_orders' => 'integer|min:0',
            'total_spent' => 'numeric|min:0',
            'average_order_value' => 'numeric|min:0',
            'last_order_date' => 'nullable|date',
            'first_order_date' => 'nullable|date',
            'payment_terms' => 'nullable|string|max:255',
            'credit_limit' => 'numeric|min:0',
            'current_balance' => 'numeric',
            'address' => 'required|string|max:255',
            'city' => 'required|string|max:100',
            'state' => 'required|string|max:100',
            'postal_code' => 'required|string|max:20',
            'country' => 'required|string|max:100',
            'bank_name' => 'nullable|string|max:255',
            'bank_account_number' => 'nullable|string|max:50',
            'bank_routing_number' => 'nullable|string|max:20',
            'contact_notes' => 'nullable|string|max:1000',
            'specializations' => 'nullable|array',
            'certifications' => 'nullable|array',
            'insurance_info' => 'nullable|array',
            'contract_start_date' => 'nullable|date',
            'contract_end_date' => 'nullable|date|after:contract_start_date',
            'commission_rate' => 'numeric|min:0|max:100',
            'discount_rate' => 'numeric|min:0|max:100',
            'shipping_methods' => 'nullable|array',
            'payment_methods' => 'nullable|array',
            'quality_rating' => 'nullable|numeric|min:0|max:5',
            'delivery_rating' => 'nullable|numeric|min:0|max:5',
            'communication_rating' => 'nullable|numeric|min:0|max:5',
            'response_time' => 'nullable|integer|min:0',
            'on_time_delivery_rate' => 'nullable|numeric|min:0|max:100',
            'return_rate' => 'nullable|numeric|min:0|max:100',
            'defect_rate' => 'nullable|numeric|min:0|max:100',
        ];
    }

    public static function messages(): array
    {
        return [
            'user_id.required' => 'User ID is required.',
            'user_id.exists' => 'The specified user does not exist.',
            'provider_number.required' => 'Provider number is required.',
            'provider_number.unique' => 'Provider number must be unique.',
            'company_name.required' => 'Company name is required.',
            'contact_person.required' => 'Contact person is required.',
            'email.required' => 'Email is required.',
            'email.unique' => 'Email must be unique.',
            'phone.required' => 'Phone number is required.',
            'provider_type.required' => 'Provider type is required.',
            'provider_type.in' => 'Invalid provider type.',
            'status.required' => 'Status is required.',
            'status.in' => 'Invalid status.',
            'address.required' => 'Address is required.',
            'city.required' => 'City is required.',
            'state.required' => 'State is required.',
            'postal_code.required' => 'Postal code is required.',
            'country.required' => 'Country is required.',
            'contract_end_date.after' => 'Contract end date must be after start date.',
        ];
    }
}
