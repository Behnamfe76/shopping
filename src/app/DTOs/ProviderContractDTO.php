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
use Spatie\LaravelData\Attributes\Validation\BooleanType;
use Fereydooni\Shopping\App\Enums\ContractType;
use Fereydooni\Shopping\App\Enums\ContractStatus;
use Fereydooni\Shopping\App\Models\ProviderContract;
use Carbon\Carbon;

class ProviderContractDTO extends Data
{
    public function __construct(
        #[IntegerType, Nullable]
        public ?int $id = null,

        #[IntegerType]
        public int $provider_id,

        #[StringType, Min(5), Max(50)]
        public string $contract_number,

        #[In(ContractType::class)]
        public string $contract_type,

        #[StringType, Min(3), Max(255)]
        public string $title,

        #[StringType, Min(10), Max(1000), Nullable]
        public ?string $description = null,

        #[Date]
        public string $start_date,

        #[Date]
        public string $end_date,

        #[ArrayType, Nullable]
        public ?array $terms = null,

        #[ArrayType, Nullable]
        public ?array $conditions = null,

        #[FloatType, Min(0), Max(100)]
        public float $commission_rate = 0.0,

        #[ArrayType, Nullable]
        public ?array $payment_terms = null,

        #[In(ContractStatus::class)]
        public string $status = ContractStatus::DRAFT,

        #[IntegerType, Nullable]
        public ?int $signed_by = null,

        #[Date, Nullable]
        public ?string $signed_at = null,

        #[Date, Nullable]
        public ?string $renewal_date = null,

        #[Date, Nullable]
        public ?string $termination_date = null,

        #[StringType, Max(500), Nullable]
        public ?string $termination_reason = null,

        #[BooleanType]
        public bool $auto_renewal = false,

        #[ArrayType, Nullable]
        public ?array $renewal_terms = null,

        #[FloatType, Min(0)]
        public float $contract_value = 0.0,

        #[StringType, Min(3), Max(3)]
        public string $currency = 'USD',

        #[ArrayType, Nullable]
        public ?array $attachments = null,

        #[StringType, Max(1000), Nullable]
        public ?string $notes = null,

        #[Date, Nullable]
        public ?string $created_at = null,

        #[Date, Nullable]
        public ?string $updated_at = null
    ) {}

    public static function fromModel(ProviderContract $contract): self
    {
        return new self(
            id: $contract->id,
            provider_id: $contract->provider_id,
            contract_number: $contract->contract_number,
            contract_type: $contract->contract_type,
            title: $contract->title,
            description: $contract->description,
            start_date: $contract->start_date?->toDateString(),
            end_date: $contract->end_date?->toDateString(),
            terms: $contract->terms,
            conditions: $contract->conditions,
            commission_rate: $contract->commission_rate,
            payment_terms: $contract->payment_terms,
            status: $contract->status,
            signed_by: $contract->signed_by,
            signed_at: $contract->signed_at?->toDateString(),
            renewal_date: $contract->renewal_date?->toDateString(),
            termination_date: $contract->termination_date?->toDateString(),
            termination_reason: $contract->termination_reason,
            auto_renewal: $contract->auto_renewal,
            renewal_terms: $contract->renewal_terms,
            contract_value: $contract->contract_value,
            currency: $contract->currency,
            attachments: $contract->attachments,
            notes: $contract->notes,
            created_at: $contract->created_at?->toDateString(),
            updated_at: $contract->updated_at?->toDateString()
        );
    }

    public static function rules(): array
    {
        return [
            'provider_id' => 'required|integer|exists:providers,id',
            'contract_number' => 'required|string|min:5|max:50|unique:provider_contracts,contract_number',
            'contract_type' => 'required|string|in:' . implode(',', ContractType::values()),
            'title' => 'required|string|min:3|max:255',
            'description' => 'nullable|string|min:10|max:1000',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after:start_date',
            'terms' => 'nullable|array',
            'conditions' => 'nullable|array',
            'commission_rate' => 'required|numeric|min:0|max:100',
            'payment_terms' => 'nullable|array',
            'status' => 'required|string|in:' . implode(',', ContractStatus::values()),
            'signed_by' => 'nullable|integer|exists:users,id',
            'signed_at' => 'nullable|date',
            'renewal_date' => 'nullable|date|after:end_date',
            'termination_date' => 'nullable|date|after:start_date',
            'termination_reason' => 'nullable|string|max:500',
            'auto_renewal' => 'boolean',
            'renewal_terms' => 'nullable|array',
            'contract_value' => 'required|numeric|min:0',
            'currency' => 'required|string|size:3',
            'attachments' => 'nullable|array',
            'notes' => 'nullable|string|max:1000',
        ];
    }

    public static function messages(): array
    {
        return [
            'provider_id.required' => 'Provider is required.',
            'provider_id.exists' => 'Selected provider does not exist.',
            'contract_number.required' => 'Contract number is required.',
            'contract_number.unique' => 'Contract number must be unique.',
            'contract_type.required' => 'Contract type is required.',
            'contract_type.in' => 'Invalid contract type selected.',
            'title.required' => 'Contract title is required.',
            'start_date.required' => 'Start date is required.',
            'start_date.after_or_equal' => 'Start date must be today or in the future.',
            'end_date.required' => 'End date is required.',
            'end_date.after' => 'End date must be after start date.',
            'commission_rate.required' => 'Commission rate is required.',
            'commission_rate.min' => 'Commission rate must be at least 0%.',
            'commission_rate.max' => 'Commission rate cannot exceed 100%.',
            'status.required' => 'Contract status is required.',
            'status.in' => 'Invalid contract status selected.',
            'signed_by.exists' => 'Selected signer does not exist.',
            'renewal_date.after' => 'Renewal date must be after end date.',
            'termination_date.after' => 'Termination date must be after start date.',
            'contract_value.required' => 'Contract value is required.',
            'contract_value.min' => 'Contract value must be at least 0.',
            'currency.required' => 'Currency is required.',
            'currency.size' => 'Currency must be exactly 3 characters.',
        ];
    }
}
