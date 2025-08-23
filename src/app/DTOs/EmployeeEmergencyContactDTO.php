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
use Spatie\LaravelData\Attributes\Validation\Regex;
use Spatie\LaravelData\Attributes\Validation\In;
use Illuminate\Support\Carbon;
use Fereydooni\Shopping\app\Enums\Relationship;
use Fereydooni\Shopping\app\Models\EmployeeEmergencyContact;

class EmployeeEmergencyContactDTO extends Data
{
    public function __construct(
        #[Nullable]
        public ?int $id,

        #[Required, IntegerType]
        public int $employee_id,

        #[Required, StringType, Max(100)]
        public string $contact_name,

        #[Required, In(Relationship::class)]
        public Relationship $relationship,

        #[Required, StringType, Max(20), Regex('/^\+?[1-9]\d{1,14}$/')]
        public string $phone_primary,

        #[Nullable, StringType, Max(20), Regex('/^\+?[1-9]\d{1,14}$/')]
        public ?string $phone_secondary,

        #[Nullable, Email, Max(255)]
        public ?string $email,

        #[Nullable, StringType, Max(255)]
        public ?string $address,

        #[Nullable, StringType, Max(100)]
        public ?string $city,

        #[Nullable, StringType, Max(100)]
        public ?string $state,

        #[Nullable, StringType, Max(20)]
        public ?string $postal_code,

        #[Nullable, StringType, Max(100)]
        public ?string $country,

        #[BooleanType]
        public bool $is_primary = false,

        #[BooleanType]
        public bool $is_active = true,

        #[Nullable, StringType, Max(1000)]
        public ?string $notes,

        #[Nullable]
        public ?Carbon $created_at,

        #[Nullable]
        public ?Carbon $updated_at,
    ) {
    }

    public static function fromModel(EmployeeEmergencyContact $contact): self
    {
        return new self(
            id: $contact->id,
            employee_id: $contact->employee_id,
            contact_name: $contact->contact_name,
            relationship: $contact->relationship,
            phone_primary: $contact->phone_primary,
            phone_secondary: $contact->phone_primary,
            email: $contact->email,
            address: $contact->address,
            city: $contact->city,
            state: $contact->state,
            postal_code: $contact->postal_code,
            country: $contact->country,
            is_primary: $contact->is_primary,
            is_active: $contact->is_active,
            notes: $contact->notes,
            created_at: $contact->created_at,
            updated_at: $contact->updated_at,
        );
    }

    public static function rules(): array
    {
        return [
            'employee_id' => ['required', 'integer', 'exists:employees,id'],
            'contact_name' => ['required', 'string', 'max:100'],
            'relationship' => ['required', 'string', 'in:' . implode(',', array_column(Relationship::cases(), 'value'))],
            'phone_primary' => ['required', 'string', 'max:20', 'regex:/^\+?[1-9]\d{1,14}$/'],
            'phone_secondary' => ['nullable', 'string', 'max:20', 'regex:/^\+?[1-9]\d{1,14}$/'],
            'email' => ['nullable', 'email', 'max:255'],
            'address' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:100'],
            'state' => ['nullable', 'string', 'max:100'],
            'postal_code' => ['nullable', 'string', 'max:20'],
            'country' => ['nullable', 'string', 'max:100'],
            'is_primary' => ['boolean'],
            'is_active' => ['boolean'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public static function messages(): array
    {
        return [
            'employee_id.required' => 'Employee ID is required.',
            'employee_id.exists' => 'The selected employee does not exist.',
            'contact_name.required' => 'Contact name is required.',
            'contact_name.max' => 'Contact name cannot exceed 100 characters.',
            'relationship.required' => 'Relationship is required.',
            'relationship.in' => 'Invalid relationship type.',
            'phone_primary.required' => 'Primary phone number is required.',
            'phone_primary.regex' => 'Primary phone number format is invalid.',
            'phone_secondary.regex' => 'Secondary phone number format is invalid.',
            'email.email' => 'Email format is invalid.',
            'address.max' => 'Address cannot exceed 255 characters.',
            'city.max' => 'City cannot exceed 100 characters.',
            'state.max' => 'State cannot exceed 100 characters.',
            'postal_code.max' => 'Postal code cannot exceed 20 characters.',
            'country.max' => 'Country cannot exceed 100 characters.',
            'notes.max' => 'Notes cannot exceed 1000 characters.',
        ];
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'employee_id' => $this->employee_id,
            'contact_name' => $this->contact_name,
            'relationship' => $this->relationship->value,
            'relationship_label' => $this->relationship->label(),
            'phone_primary' => $this->phone_primary,
            'phone_secondary' => $this->phone_secondary,
            'email' => $this->email,
            'address' => $this->address,
            'city' => $this->city,
            'state' => $this->state,
            'postal_code' => $this->postal_code,
            'country' => $this->country,
            'is_primary' => $this->is_primary,
            'is_active' => $this->is_active,
            'notes' => $this->notes,
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }

    public function getFullAddress(): string
    {
        $parts = array_filter([
            $this->address,
            $this->city,
            $this->state,
            $this->postal_code,
            $this->country
        ]);

        return implode(', ', $parts);
    }

    public function getDisplayName(): string
    {
        return "{$this->contact_name} ({$this->relationship->label()})";
    }

    public function hasValidPhone(): bool
    {
        return !empty($this->phone_primary) || !empty($this->phone_secondary);
    }

    public function hasValidEmail(): bool
    {
        return !empty($this->email) && filter_var($this->email, FILTER_VALIDATE_EMAIL);
    }

    public function hasValidAddress(): bool
    {
        return !empty($this->address) && !empty($this->city) && !empty($this->state);
    }

    public function getContactMethod(): string
    {
        if ($this->hasValidPhone()) {
            return 'phone';
        }
        if ($this->hasValidEmail()) {
            return 'email';
        }
        return 'address';
    }

    public function isPrimaryContact(): bool
    {
        return $this->is_primary;
    }

    public function isActiveContact(): bool
    {
        return $this->is_active;
    }
}
