<?php

namespace Fereydooni\Shopping\app\DTOs;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Attributes\Validation\Nullable;
use Spatie\LaravelData\Attributes\Validation\StringType;
use Spatie\LaravelData\Attributes\Validation\IntegerType;
use Spatie\LaravelData\Attributes\Validation\BooleanType;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\In;
use Spatie\LaravelData\Attributes\Validation\Exists;
use Illuminate\Support\Carbon;
use Fereydooni\Shopping\app\Models\CustomerPreference;
use Fereydooni\Shopping\app\Enums\CustomerPreferenceType;

class CustomerPreferenceDTO extends Data
{
    public function __construct(
        #[Nullable]
        public ?int $id,

        #[Required, IntegerType, Exists('customers', 'id')]
        public int $customer_id,

        #[Required, StringType, Max(100)]
        public string $preference_key,

        #[Required, StringType, Max(1000)]
        public string $preference_value,

        #[Required, StringType, In(['string', 'integer', 'float', 'boolean', 'json', 'array', 'object'])]
        public string $preference_type,

        #[BooleanType]
        public bool $is_active,

        #[Nullable, StringType, Max(500)]
        public ?string $description,

        #[Nullable]
        public ?array $metadata,

        #[Nullable]
        public ?Carbon $created_at,

        #[Nullable]
        public ?Carbon $updated_at,

        // Relationships
        #[Nullable]
        public mixed $customer = null,

        // Computed fields
        #[Nullable]
        public ?bool $is_string_type = null,

        #[Nullable]
        public ?bool $is_numeric_type = null,

        #[Nullable]
        public ?bool $is_boolean_type = null,

        #[Nullable]
        public ?bool $is_complex_type = null,

        public mixed $typed_value = null,

        #[Nullable]
        public ?string $formatted_value = null,

        #[Nullable]
        public ?string $preference_category = null,

        #[Nullable]
        public ?bool $has_metadata = null,

        #[Nullable]
        public ?int $metadata_count = null,
    ) {
    }

    public static function fromModel(CustomerPreference $preference): static
    {
        return new static(
            id: $preference->id,
            customer_id: $preference->customer_id,
            preference_key: $preference->preference_key,
            preference_value: $preference->preference_value,
            preference_type: $preference->preference_type,
            is_active: $preference->is_active,
            description: $preference->description,
            metadata: $preference->metadata,
            created_at: $preference->created_at,
            updated_at: $preference->updated_at,

            // Relationships
            customer: $preference->customer,

            // Computed fields
            is_string_type: $preference->is_string_type,
            is_numeric_type: $preference->is_numeric_type,
            is_boolean_type: $preference->is_boolean_type,
            is_complex_type: $preference->is_complex_type,
            typed_value: $preference->typed_value,
            formatted_value: $preference->formatted_value,
            preference_category: $preference->preference_category,
            has_metadata: $preference->has_metadata,
            metadata_count: $preference->metadata_count,
        );
    }

    public static function rules(): array
    {
        return [
            'customer_id' => ['required', 'integer', 'exists:customers,id'],
            'preference_key' => ['required', 'string', 'max:100'],
            'preference_value' => ['required', 'string', 'max:1000'],
            'preference_type' => ['required', 'string', 'in:string,integer,float,boolean,json,array,object'],
            'is_active' => ['boolean'],
            'description' => ['nullable', 'string', 'max:500'],
            'metadata' => ['nullable', 'array'],
        ];
    }

    public static function messages(): array
    {
        return [
            'customer_id.required' => 'Customer ID is required.',
            'customer_id.exists' => 'The selected customer does not exist.',
            'preference_key.required' => 'Preference key is required.',
            'preference_key.max' => 'Preference key cannot exceed 100 characters.',
            'preference_value.required' => 'Preference value is required.',
            'preference_value.max' => 'Preference value cannot exceed 1000 characters.',
            'preference_type.required' => 'Preference type is required.',
            'preference_type.in' => 'Preference type must be one of: string, integer, float, boolean, json, array, object.',
            'description.max' => 'Description cannot exceed 500 characters.',
        ];
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'customer_id' => $this->customer_id,
            'preference_key' => $this->preference_key,
            'preference_value' => $this->preference_value,
            'preference_type' => $this->preference_type,
            'is_active' => $this->is_active,
            'description' => $this->description,
            'metadata' => $this->metadata,
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
            'customer' => $this->customer,
            'is_string_type' => $this->is_string_type,
            'is_numeric_type' => $this->is_numeric_type,
            'is_boolean_type' => $this->is_boolean_type,
            'is_complex_type' => $this->is_complex_type,
            'typed_value' => $this->typed_value,
            'formatted_value' => $this->formatted_value,
            'preference_category' => $this->preference_category,
            'has_metadata' => $this->has_metadata,
            'metadata_count' => $this->metadata_count,
        ];
    }

    public function getTypedValue(): mixed
    {
        if ($this->typed_value !== null) {
            return $this->typed_value;
        }

        return match($this->preference_type) {
            'string' => $this->preference_value,
            'integer' => (int) $this->preference_value,
            'float' => (float) $this->preference_value,
            'boolean' => filter_var($this->preference_value, FILTER_VALIDATE_BOOLEAN),
            'json', 'array', 'object' => json_decode($this->preference_value, true),
            default => $this->preference_value,
        };
    }

    public function getFormattedValue(): string
    {
        if ($this->formatted_value !== null) {
            return $this->formatted_value;
        }

        return match($this->preference_type) {
            'boolean' => $this->getTypedValue() ? 'Yes' : 'No',
            'json', 'array', 'object' => json_encode($this->getTypedValue(), JSON_PRETTY_PRINT),
            default => (string) $this->preference_value,
        };
    }

    public function isStringType(): bool
    {
        return $this->preference_type === 'string';
    }

    public function isNumericType(): bool
    {
        return in_array($this->preference_type, ['integer', 'float']);
    }

    public function isBooleanType(): bool
    {
        return $this->preference_type === 'boolean';
    }

    public function isComplexType(): bool
    {
        return in_array($this->preference_type, ['json', 'array', 'object']);
    }

    public function getPreferenceCategory(): string
    {
        if ($this->preference_category !== null) {
            return $this->preference_category;
        }

        // Extract category from preference key (e.g., "ui.theme" -> "ui")
        $parts = explode('.', $this->preference_key);
        return $parts[0] ?? 'general';
    }

    public function hasMetadata(): bool
    {
        return !empty($this->metadata);
    }

    public function getMetadataCount(): int
    {
        return is_array($this->metadata) ? count($this->metadata) : 0;
    }

    public function getMetadataValue(string $key, mixed $default = null): mixed
    {
        return $this->metadata[$key] ?? $default;
    }

    public function setMetadataValue(string $key, mixed $value): void
    {
        if (!is_array($this->metadata)) {
            $this->metadata = [];
        }
        $this->metadata[$key] = $value;
    }

    public function removeMetadataValue(string $key): bool
    {
        if (is_array($this->metadata) && array_key_exists($key, $this->metadata)) {
            unset($this->metadata[$key]);
            return true;
        }
        return false;
    }
}
