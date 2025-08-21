<?php

namespace Fereydooni\Shopping\app\DTOs;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Attributes\Validation\Email;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Min;
use Spatie\LaravelData\Attributes\Validation\Nullable;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Attributes\Validation\StringType;
use Spatie\LaravelData\Attributes\Validation\IntegerType;
use Spatie\LaravelData\Attributes\Validation\BooleanType;
use Spatie\LaravelData\Attributes\Validation\Date;
use Spatie\LaravelData\Attributes\Validation\Numeric;
use Spatie\LaravelData\Attributes\Validation\In;
use Spatie\LaravelData\Attributes\Validation\Unique;
use Illuminate\Support\Carbon;
use Fereydooni\Shopping\app\Enums\CustomerStatus;
use Fereydooni\Shopping\app\Enums\CustomerType;
use Fereydooni\Shopping\app\Enums\Gender;
use Fereydooni\Shopping\app\Models\Customer;

class CustomerDTO extends Data
{
    public function __construct(
        #[Nullable]
        public ?int $id,

        #[Required, IntegerType]
        public int $user_id,

        #[Required, StringType, Max(50), Unique('customers', 'customer_number')]
        public string $customer_number,

        #[Required, StringType, Max(100)]
        public string $first_name,

        #[Required, StringType, Max(100)]
        public string $last_name,

        #[Required, Email, Max(255), Unique('customers', 'email')]
        public string $email,

        #[Nullable, StringType, Max(20)]
        public ?string $phone,

        #[Nullable, Date]
        public ?Carbon $date_of_birth,

        #[Nullable]
        public ?Gender $gender,

        #[Nullable, StringType, Max(255)]
        public ?string $company_name,

        #[Nullable, StringType, Max(100)]
        public ?string $tax_id,

        public CustomerType $customer_type,

        public CustomerStatus $status,

        #[IntegerType, Min(0)]
        public int $loyalty_points,

        #[IntegerType, Min(0)]
        public int $total_orders,

        #[Numeric, Min(0)]
        public float $total_spent,

        #[Numeric, Min(0)]
        public float $average_order_value,

        #[Nullable]
        public ?Carbon $last_order_date,

        #[Nullable]
        public ?Carbon $first_order_date,

        #[Nullable, StringType, Max(100)]
        public ?string $preferred_payment_method,

        #[Nullable, StringType, Max(100)]
        public ?string $preferred_shipping_method,

        #[BooleanType]
        public bool $marketing_consent,

        #[BooleanType]
        public bool $newsletter_subscription,

        #[Nullable]
        public ?array $notes,

        #[Nullable]
        public ?array $tags,

        #[IntegerType, Min(0)]
        public int $address_count,

        #[IntegerType, Min(0)]
        public int $order_count,

        #[IntegerType, Min(0)]
        public int $review_count,

        #[IntegerType, Min(0)]
        public int $wishlist_count,

        #[Nullable]
        public ?Carbon $created_at,

        #[Nullable]
        public ?Carbon $updated_at,

        // Computed fields
        #[Nullable]
        public ?string $full_name = null,

        #[Nullable]
        public ?string $display_name = null,

        #[Nullable]
        public ?string $customer_number_formatted = null,

        #[Nullable]
        public ?bool $is_active = null,

        #[Nullable]
        public ?bool $can_order = null,

        #[Nullable]
        public ?bool $has_business_fields = null,

        #[Nullable]
        public ?bool $has_special_pricing = null,

        #[Nullable]
        public ?int $age = null,

        #[Nullable]
        public ?bool $is_birthday_today = null,

        #[Nullable]
        public ?bool $is_birthday_this_month = null,

        // Relationships
        #[Nullable]
        public mixed $user = null,

        #[Nullable]
        public mixed $addresses = null,

        #[Nullable]
        public mixed $orders = null,

        #[Nullable]
        public mixed $reviews = null,
    ) {
    }

    public static function fromModel(Customer $customer): static
    {
        return new static(
            id: $customer->id,
            user_id: $customer->user_id,
            customer_number: $customer->customer_number,
            first_name: $customer->first_name,
            last_name: $customer->last_name,
            email: $customer->email,
            phone: $customer->phone,
            date_of_birth: $customer->date_of_birth,
            gender: $customer->gender,
            company_name: $customer->company_name,
            tax_id: $customer->tax_id,
            customer_type: $customer->customer_type,
            status: $customer->status,
            loyalty_points: $customer->loyalty_points,
            total_orders: $customer->total_orders,
            total_spent: $customer->total_spent,
            average_order_value: $customer->average_order_value,
            last_order_date: $customer->last_order_date,
            first_order_date: $customer->first_order_date,
            preferred_payment_method: $customer->preferred_payment_method,
            preferred_shipping_method: $customer->preferred_shipping_method,
            marketing_consent: $customer->marketing_consent,
            newsletter_subscription: $customer->newsletter_subscription,
            notes: $customer->notes,
            tags: $customer->tags,
            address_count: $customer->address_count,
            order_count: $customer->order_count,
            review_count: $customer->review_count,
            wishlist_count: $customer->wishlist_count,
            created_at: $customer->created_at,
            updated_at: $customer->updated_at,

            // Computed fields
            full_name: $customer->full_name,
            display_name: $customer->display_name,
            customer_number_formatted: 'CUST-' . str_pad($customer->customer_number, 8, '0', STR_PAD_LEFT),
            is_active: $customer->is_active,
            can_order: $customer->can_order,
            has_business_fields: $customer->customer_type->hasBusinessFields(),
            has_special_pricing: $customer->customer_type->hasSpecialPricing(),
            age: $customer->date_of_birth ? $customer->date_of_birth->age : null,
            is_birthday_today: $customer->date_of_birth ? $customer->date_of_birth->isBirthday(now()) : false,
            is_birthday_this_month: $customer->date_of_birth ? $customer->date_of_birth->month === now()->month : false,

            // Relationships
            user: $customer->user,
            addresses: $customer->addresses,
            orders: $customer->orders,
            reviews: $customer->reviews,
        );
    }

    public static function rules(): array
    {
        return [
            'user_id' => ['required', 'integer', 'exists:users,id'],
            'customer_number' => ['required', 'string', 'max:50', 'unique:customers,customer_number'],
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:255', 'unique:customers,email'],
            'phone' => ['nullable', 'string', 'max:20'],
            'date_of_birth' => ['nullable', 'date', 'before:today'],
            'gender' => ['nullable', 'in:' . implode(',', array_column(Gender::cases(), 'value'))],
            'company_name' => ['nullable', 'string', 'max:255'],
            'tax_id' => ['nullable', 'string', 'max:100'],
            'customer_type' => ['required', 'in:' . implode(',', array_column(CustomerType::cases(), 'value'))],
            'status' => ['required', 'in:' . implode(',', array_column(CustomerStatus::cases(), 'value'))],
            'loyalty_points' => ['integer', 'min:0'],
            'total_orders' => ['integer', 'min:0'],
            'total_spent' => ['numeric', 'min:0'],
            'average_order_value' => ['numeric', 'min:0'],
            'preferred_payment_method' => ['nullable', 'string', 'max:100'],
            'preferred_shipping_method' => ['nullable', 'string', 'max:100'],
            'marketing_consent' => ['boolean'],
            'newsletter_subscription' => ['boolean'],
            'notes' => ['nullable', 'array'],
            'tags' => ['nullable', 'array'],
        ];
    }

    public static function messages(): array
    {
        return [
            'user_id.required' => 'User ID is required.',
            'user_id.exists' => 'The selected user does not exist.',
            'customer_number.required' => 'Customer number is required.',
            'customer_number.unique' => 'This customer number is already taken.',
            'first_name.required' => 'First name is required.',
            'last_name.required' => 'Last name is required.',
            'email.required' => 'Email is required.',
            'email.email' => 'Please enter a valid email address.',
            'email.unique' => 'This email address is already registered.',
            'date_of_birth.before' => 'Date of birth must be in the past.',
            'gender.in' => 'Please select a valid gender.',
            'customer_type.required' => 'Customer type is required.',
            'customer_type.in' => 'Please select a valid customer type.',
            'status.required' => 'Status is required.',
            'status.in' => 'Please select a valid status.',
            'loyalty_points.min' => 'Loyalty points cannot be negative.',
            'total_orders.min' => 'Total orders cannot be negative.',
            'total_spent.min' => 'Total spent cannot be negative.',
            'average_order_value.min' => 'Average order value cannot be negative.',
        ];
    }

    public static function attributes(): array
    {
        return [
            'user_id' => 'user',
            'customer_number' => 'customer number',
            'first_name' => 'first name',
            'last_name' => 'last name',
            'date_of_birth' => 'date of birth',
            'company_name' => 'company name',
            'tax_id' => 'tax ID',
            'customer_type' => 'customer type',
            'loyalty_points' => 'loyalty points',
            'total_orders' => 'total orders',
            'total_spent' => 'total spent',
            'average_order_value' => 'average order value',
            'last_order_date' => 'last order date',
            'first_order_date' => 'first order date',
            'preferred_payment_method' => 'preferred payment method',
            'preferred_shipping_method' => 'preferred shipping method',
            'marketing_consent' => 'marketing consent',
            'newsletter_subscription' => 'newsletter subscription',
            'address_count' => 'address count',
            'order_count' => 'order count',
            'review_count' => 'review count',
            'wishlist_count' => 'wishlist count',
        ];
    }
}
