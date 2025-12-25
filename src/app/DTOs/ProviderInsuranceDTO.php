<?php

namespace Fereydooni\Shopping\App\DTOs;

use Carbon\Carbon;
use Fereydooni\Shopping\App\Enums\InsuranceStatus;
use Fereydooni\Shopping\App\Enums\InsuranceType;
use Fereydooni\Shopping\App\Enums\VerificationStatus;
use Fereydooni\Shopping\App\Models\Provider;
use Fereydooni\Shopping\App\Models\ProviderInsurance;
use Fereydooni\Shopping\App\Models\User;
use Spatie\LaravelData\Attributes\Validation\After;
use Spatie\LaravelData\Attributes\Validation\ArrayType;
use Spatie\LaravelData\Attributes\Validation\Date;
use Spatie\LaravelData\Attributes\Validation\FloatType;
use Spatie\LaravelData\Attributes\Validation\In;
use Spatie\LaravelData\Attributes\Validation\IntegerType;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Min;
use Spatie\LaravelData\Attributes\Validation\Nullable;
use Spatie\LaravelData\Attributes\Validation\Regex;
use Spatie\LaravelData\Attributes\Validation\StringType;
use Spatie\LaravelData\Data;

class ProviderInsuranceDTO extends Data
{
    public function __construct(
        #[IntegerType, Min(1)]
        public int $provider_id,

        #[In(InsuranceType::class)]
        public string $insurance_type,

        #[StringType, Min(5), Max(50), Regex('/^[A-Z0-9\-]+$/')]
        public string $policy_number,

        #[StringType, Min(2), Max(255)]
        public string $provider_name,

        #[FloatType, Min(0)]
        public float $coverage_amount,

        #[Date, After('today')]
        public string $start_date,

        #[Date, After('start_date')]
        public string $end_date,

        #[In(InsuranceStatus::class)]
        public string $status = InsuranceStatus::PENDING,

        #[ArrayType, Nullable]
        public ?array $documents = null,

        #[In(VerificationStatus::class)]
        public string $verification_status = VerificationStatus::PENDING,

        #[IntegerType, Min(1), Nullable]
        public ?int $verified_by = null,

        #[Date, Nullable]
        public ?string $verified_at = null,

        #[StringType, Max(1000), Nullable]
        public ?string $notes = null,

        #[IntegerType, Nullable]
        public ?int $id = null,

        #[Date, Nullable]
        public ?string $created_at = null,

        #[Date, Nullable]
        public ?string $updated_at = null,
    ) {}

    /**
     * Create DTO from ProviderInsurance model
     */
    public static function fromModel(ProviderInsurance $providerInsurance): self
    {
        return new self(
            provider_id: $providerInsurance->provider_id,
            insurance_type: $providerInsurance->insurance_type->value,
            policy_number: $providerInsurance->policy_number,
            provider_name: $providerInsurance->provider_name,
            coverage_amount: $providerInsurance->coverage_amount,
            start_date: $providerInsurance->start_date->format('Y-m-d'),
            end_date: $providerInsurance->end_date->format('Y-m-d'),
            status: $providerInsurance->status->value,
            documents: $providerInsurance->documents,
            verification_status: $providerInsurance->verification_status->value,
            verified_by: $providerInsurance->verified_by,
            verified_at: $providerInsurance->verified_at?->format('Y-m-d H:i:s'),
            notes: $providerInsurance->notes,
            id: $providerInsurance->id,
            created_at: $providerInsurance->created_at?->format('Y-m-d H:i:s'),
            updated_at: $providerInsurance->updated_at?->format('Y-m-d H:i:s'),
        );
    }

    /**
     * Convert DTO to array for model creation/update
     */
    public function toArray(): array
    {
        return [
            'provider_id' => $this->provider_id,
            'insurance_type' => $this->insurance_type,
            'policy_number' => $this->policy_number,
            'provider_name' => $this->provider_name,
            'coverage_amount' => $this->coverage_amount,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'status' => $this->status,
            'documents' => $this->documents,
            'verification_status' => $this->verification_status,
            'verified_by' => $this->verified_by,
            'verified_at' => $this->verified_at,
            'notes' => $this->notes,
        ];
    }

    /**
     * Get validation rules for creating new insurance
     */
    public static function rules(): array
    {
        return [
            'provider_id' => ['required', 'integer', 'min:1', 'exists:providers,id'],
            'insurance_type' => ['required', 'string', 'in:'.implode(',', InsuranceType::values())],
            'policy_number' => ['required', 'string', 'min:5', 'max:50', 'regex:/^[A-Z0-9\-]+$/', 'unique:provider_insurances,policy_number'],
            'provider_name' => ['required', 'string', 'min:2', 'max:255'],
            'coverage_amount' => ['required', 'numeric', 'min:0'],
            'start_date' => ['required', 'date', 'after:today'],
            'end_date' => ['required', 'date', 'after:start_date'],
            'status' => ['sometimes', 'string', 'in:'.implode(',', InsuranceStatus::values())],
            'documents' => ['sometimes', 'array'],
            'documents.*' => ['string', 'max:255'],
            'verification_status' => ['sometimes', 'string', 'in:'.implode(',', VerificationStatus::values())],
            'verified_by' => ['sometimes', 'integer', 'min:1', 'exists:users,id'],
            'verified_at' => ['sometimes', 'date'],
            'notes' => ['sometimes', 'string', 'max:1000'],
        ];
    }

    /**
     * Get validation rules for updating insurance
     */
    public static function updateRules(int $insuranceId): array
    {
        $rules = self::rules();
        $rules['policy_number'] = ['required', 'string', 'min:5', 'max:50', 'regex:/^[A-Z0-9\-]+$/', 'unique:provider_insurances,policy_number,'.$insuranceId];
        $rules['provider_id'] = ['sometimes', 'integer', 'min:1', 'exists:providers,id'];
        $rules['insurance_type'] = ['sometimes', 'string', 'in:'.implode(',', InsuranceType::values())];
        $rules['provider_name'] = ['sometimes', 'string', 'min:2', 'max:255'];
        $rules['coverage_amount'] = ['sometimes', 'numeric', 'min:0'];
        $rules['start_date'] = ['sometimes', 'date'];
        $rules['end_date'] = ['sometimes', 'date', 'after:start_date'];

        return $rules;
    }

    /**
     * Get validation messages
     */
    public static function messages(): array
    {
        return [
            'provider_id.required' => 'Provider ID is required.',
            'provider_id.exists' => 'The selected provider does not exist.',
            'insurance_type.required' => 'Insurance type is required.',
            'insurance_type.in' => 'The selected insurance type is invalid.',
            'policy_number.required' => 'Policy number is required.',
            'policy_number.regex' => 'Policy number must contain only uppercase letters, numbers, and hyphens.',
            'policy_number.unique' => 'This policy number is already in use.',
            'provider_name.required' => 'Insurance provider name is required.',
            'coverage_amount.required' => 'Coverage amount is required.',
            'coverage_amount.min' => 'Coverage amount must be at least 0.',
            'start_date.required' => 'Start date is required.',
            'start_date.after' => 'Start date must be in the future.',
            'end_date.required' => 'End date is required.',
            'end_date.after' => 'End date must be after start date.',
            'status.in' => 'The selected status is invalid.',
            'verification_status.in' => 'The selected verification status is invalid.',
            'verified_by.exists' => 'The selected verifier does not exist.',
        ];
    }

    /**
     * Get insurance type label
     */
    public function getInsuranceTypeLabel(): string
    {
        return InsuranceType::from($this->insurance_type)->label();
    }

    /**
     * Get status label
     */
    public function getStatusLabel(): string
    {
        return InsuranceStatus::from($this->status)->label();
    }

    /**
     * Get verification status label
     */
    public function getVerificationStatusLabel(): string
    {
        return VerificationStatus::from($this->verification_status)->label();
    }

    /**
     * Check if insurance is active
     */
    public function isActive(): bool
    {
        $now = Carbon::now();
        $startDate = Carbon::parse($this->start_date);
        $endDate = Carbon::parse($this->end_date);

        return $this->status === InsuranceStatus::ACTIVE->value &&
               $startDate <= $now &&
               $endDate >= $now;
    }

    /**
     * Check if insurance has expired
     */
    public function isExpired(): bool
    {
        return Carbon::parse($this->end_date) < Carbon::now();
    }

    /**
     * Check if insurance is expiring soon
     */
    public function isExpiringSoon(int $days = 30): bool
    {
        $endDate = Carbon::parse($this->end_date);
        $now = Carbon::now();

        return $endDate->diffInDays($now) <= $days && ! $this->isExpired();
    }

    /**
     * Check if insurance is verified
     */
    public function isVerified(): bool
    {
        return $this->verification_status === VerificationStatus::VERIFIED->value;
    }

    /**
     * Check if insurance is pending verification
     */
    public function isPendingVerification(): bool
    {
        return $this->verification_status === VerificationStatus::PENDING->value;
    }

    /**
     * Get remaining days until expiration
     */
    public function getDaysUntilExpiration(): int
    {
        $endDate = Carbon::parse($this->end_date);
        $now = Carbon::now();

        return max(0, $endDate->diffInDays($now));
    }

    /**
     * Get insurance duration in days
     */
    public function getDurationInDays(): int
    {
        $startDate = Carbon::parse($this->start_date);
        $endDate = Carbon::parse($this->end_date);

        return $startDate->diffInDays($endDate);
    }

    /**
     * Get insurance duration in months
     */
    public function getDurationInMonths(): int
    {
        $startDate = Carbon::parse($this->start_date);
        $endDate = Carbon::parse($this->end_date);

        return $startDate->diffInMonths($endDate);
    }

    /**
     * Get insurance duration in years
     */
    public function getDurationInYears(): int
    {
        $startDate = Carbon::parse($this->start_date);
        $endDate = Carbon::parse($this->end_date);

        return $startDate->diffInYears($endDate);
    }

    /**
     * Get provider relationship
     */
    public function getProvider(): ?Provider
    {
        return Provider::find($this->provider_id);
    }

    /**
     * Get verifier relationship
     */
    public function getVerifier(): ?User
    {
        return $this->verified_by ? User::find($this->verified_by) : null;
    }

    /**
     * Get formatted coverage amount
     */
    public function getFormattedCoverageAmount(): string
    {
        return '$'.number_format($this->coverage_amount, 2);
    }

    /**
     * Get formatted start date
     */
    public function getFormattedStartDate(): string
    {
        return Carbon::parse($this->start_date)->format('M d, Y');
    }

    /**
     * Get formatted end date
     */
    public function getFormattedEndDate(): string
    {
        return Carbon::parse($this->end_date)->format('M d, Y');
    }

    /**
     * Get formatted verified date
     */
    public function getFormattedVerifiedDate(): ?string
    {
        return $this->verified_at ? Carbon::parse($this->verified_at)->format('M d, Y H:i') : null;
    }

    /**
     * Get document count
     */
    public function getDocumentCount(): int
    {
        return $this->documents ? count($this->documents) : 0;
    }

    /**
     * Check if insurance has documents
     */
    public function hasDocuments(): bool
    {
        return $this->getDocumentCount() > 0;
    }

    /**
     * Get insurance type description
     */
    public function getInsuranceTypeDescription(): string
    {
        return InsuranceType::from($this->insurance_type)->description();
    }

    /**
     * Get status description
     */
    public function getStatusDescription(): string
    {
        return InsuranceStatus::from($this->status)->description();
    }

    /**
     * Get verification status description
     */
    public function getVerificationStatusDescription(): string
    {
        return VerificationStatus::from($this->verification_status)->description();
    }
}
