<?php

namespace Fereydooni\Shopping\App\DTOs;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Attributes\Validation\StringType;
use Spatie\LaravelData\Attributes\Validation\IntegerType;
use Spatie\LaravelData\Attributes\Validation\BooleanType;
use Spatie\LaravelData\Attributes\Validation\Date;
use Spatie\LaravelData\Attributes\Validation\Nullable;
use Spatie\LaravelData\Attributes\Validation\Url;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Min;
use Spatie\LaravelData\Attributes\Validation\In;
use Spatie\LaravelData\Attributes\Validation\ArrayType;
use Spatie\LaravelData\Attributes\Validation\DateFormat;
use Spatie\LaravelData\Attributes\WithTransformer;
use Spatie\LaravelData\Transformers\DateTimeTransformer;
use Fereydooni\Shopping\App\Models\ProviderCertification;
use Fereydooni\Shopping\App\Enums\CertificationCategory;
use Fereydooni\Shopping\App\Enums\CertificationStatus;
use Fereydooni\Shopping\App\Enums\VerificationStatus;
use Carbon\Carbon;

class ProviderCertificationDTO extends Data
{
    public function __construct(
        #[Required, IntegerType, Min(1)]
        public int $provider_id,

        #[Required, StringType, Max(255)]
        public string $certification_name,

        #[Required, StringType, Max(100)]
        public string $certification_number,

        #[Required, StringType, Max(255)]
        public string $issuing_organization,

        #[Required, In(CertificationCategory::class)]
        public CertificationCategory $category,

        #[Nullable, StringType, Max(1000)]
        public ?string $description = null,

        #[Required, Date, DateFormat('Y-m-d')]
        public Carbon $issue_date,

        #[Nullable, Date, DateFormat('Y-m-d')]
        public ?Carbon $expiry_date = null,

        #[Nullable, Date, DateFormat('Y-m-d')]
        public ?Carbon $renewal_date = null,

        #[Required, In(CertificationStatus::class)]
        public CertificationStatus $status = CertificationStatus::ACTIVE,

        #[Required, In(VerificationStatus::class)]
        public VerificationStatus $verification_status = VerificationStatus::UNVERIFIED,

        #[Nullable, Url, Max(500)]
        public ?string $verification_url = null,

        #[Nullable, StringType, Max(500)]
        public ?string $attachment_path = null,

        #[Nullable, IntegerType, Min(0)]
        public ?int $credits_earned = null,

        #[Required, BooleanType]
        public bool $is_recurring = false,

        #[Nullable, IntegerType, Min(1)]
        public ?int $renewal_period = null,

        #[Nullable, ArrayType]
        public ?array $renewal_requirements = null,

        #[WithTransformer(DateTimeTransformer::class)]
        public ?Carbon $verified_at = null,

        #[Nullable, IntegerType, Min(1)]
        public ?int $verified_by = null,

        #[Nullable, ArrayType]
        public ?array $notes = null,

        #[WithTransformer(DateTimeTransformer::class)]
        public ?Carbon $created_at = null,

        #[WithTransformer(DateTimeTransformer::class)]
        public ?Carbon $updated_at = null,

        #[Nullable, IntegerType, Min(1)]
        public ?int $id = null,
    ) {
    }

    /**
     * Create DTO from ProviderCertification model.
     */
    public static function fromModel(ProviderCertification $certification): self
    {
        return new self(
            provider_id: $certification->provider_id,
            certification_name: $certification->certification_name,
            certification_number: $certification->certification_number,
            issuing_organization: $certification->issuing_organization,
            category: $certification->category,
            description: $certification->description,
            issue_date: $certification->issue_date,
            expiry_date: $certification->expiry_date,
            renewal_date: $certification->renewal_date,
            status: $certification->status,
            verification_status: $certification->verification_status,
            verification_url: $certification->verification_url,
            attachment_path: $certification->attachment_path,
            credits_earned: $certification->credits_earned,
            is_recurring: $certification->is_recurring,
            renewal_period: $certification->renewal_period,
            renewal_requirements: $certification->renewal_requirements,
            verified_at: $certification->verified_at,
            verified_by: $certification->verified_by,
            notes: $certification->notes,
            created_at: $certification->created_at,
            updated_at: $certification->updated_at,
            id: $certification->id,
        );
    }

    /**
     * Get validation rules for creating a new certification.
     */
    public static function rules(): array
    {
        return [
            'provider_id' => ['required', 'integer', 'min:1', 'exists:providers,id'],
            'certification_name' => ['required', 'string', 'max:255'],
            'certification_number' => ['required', 'string', 'max:100', 'unique:provider_certifications,certification_number'],
            'issuing_organization' => ['required', 'string', 'max:255'],
            'category' => ['required', 'string', 'in:' . implode(',', array_column(CertificationCategory::cases(), 'value'))],
            'description' => ['nullable', 'string', 'max:1000'],
            'issue_date' => ['required', 'date', 'date_format:Y-m-d', 'before_or_equal:today'],
            'expiry_date' => ['nullable', 'date', 'date_format:Y-m-d', 'after:issue_date'],
            'renewal_date' => ['nullable', 'date', 'date_format:Y-m-d', 'after:issue_date'],
            'verification_url' => ['nullable', 'url', 'max:500'],
            'attachment_path' => ['nullable', 'string', 'max:500'],
            'credits_earned' => ['nullable', 'integer', 'min:0'],
            'is_recurring' => ['required', 'boolean'],
            'renewal_period' => ['nullable', 'integer', 'min:1'],
            'renewal_requirements' => ['nullable', 'array'],
            'renewal_requirements.*' => ['string', 'max:255'],
            'notes' => ['nullable', 'array'],
            'notes.*' => ['string', 'max:1000'],
        ];
    }

    /**
     * Get validation rules for updating an existing certification.
     */
    public static function updateRules(int $certificationId): array
    {
        $rules = self::rules();
        $rules['certification_number'] = ['required', 'string', 'max:100', 'unique:provider_certifications,certification_number,' . $certificationId];

        return $rules;
    }

    /**
     * Get validation messages.
     */
    public static function messages(): array
    {
        return [
            'provider_id.required' => 'Provider ID is required.',
            'provider_id.exists' => 'The selected provider does not exist.',
            'certification_name.required' => 'Certification name is required.',
            'certification_name.max' => 'Certification name cannot exceed 255 characters.',
            'certification_number.required' => 'Certification number is required.',
            'certification_number.unique' => 'This certification number is already in use.',
            'issuing_organization.required' => 'Issuing organization is required.',
            'category.required' => 'Certification category is required.',
            'category.in' => 'Invalid certification category selected.',
            'issue_date.required' => 'Issue date is required.',
            'issue_date.before_or_equal' => 'Issue date cannot be in the future.',
            'expiry_date.after' => 'Expiry date must be after the issue date.',
            'renewal_date.after' => 'Renewal date must be after the issue date.',
            'verification_url.url' => 'Verification URL must be a valid URL.',
            'credits_earned.min' => 'Credits earned cannot be negative.',
            'renewal_period.min' => 'Renewal period must be at least 1.',
        ];
    }

    /**
     * Check if the certification is active.
     */
    public function isActive(): bool
    {
        return $this->status === CertificationStatus::ACTIVE;
    }

    /**
     * Check if the certification is expired.
     */
    public function isExpired(): bool
    {
        return $this->status === CertificationStatus::EXPIRED;
    }

    /**
     * Check if the certification is verified.
     */
    public function isVerified(): bool
    {
        return $this->verification_status === VerificationStatus::VERIFIED;
    }

    /**
     * Check if the certification is expiring soon.
     */
    public function isExpiringSoon(int $days = 30): bool
    {
        if (!$this->expiry_date || $this->isExpired()) {
            return false;
        }

        return $this->expiry_date->diffInDays(Carbon::now(), false) <= $days;
    }

    /**
     * Check if the certification needs renewal.
     */
    public function needsRenewal(): bool
    {
        if (!$this->is_recurring || !$this->expiry_date) {
            return false;
        }

        return $this->expiry_date->diffInDays(Carbon::now(), false) <= 90;
    }

    /**
     * Get the days until expiration.
     */
    public function daysUntilExpiration(): ?int
    {
        if (!$this->expiry_date) {
            return null;
        }

        return $this->expiry_date->diffInDays(Carbon::now(), false);
    }

    /**
     * Get the days since issue.
     */
    public function daysSinceIssue(): ?int
    {
        if (!$this->issue_date) {
            return null;
        }

        return $this->issue_date->diffInDays(Carbon::now(), false);
    }

    /**
     * Get the certification age in years.
     */
    public function getAgeInYears(): ?float
    {
        if (!$this->issue_date) {
            return null;
        }

        return $this->issue_date->diffInYears(Carbon::now(), true);
    }

    /**
     * Check if the certification is valid for use.
     */
    public function isValid(): bool
    {
        return $this->isActive() && $this->isVerified();
    }

    /**
     * Get the display name for the category.
     */
    public function getCategoryLabel(): string
    {
        return $this->category->label();
    }

    /**
     * Get the display name for the status.
     */
    public function getStatusLabel(): string
    {
        return $this->status->label();
    }

    /**
     * Get the display name for the verification status.
     */
    public function getVerificationStatusLabel(): string
    {
        return $this->verification_status->label();
    }

    /**
     * Get the color class for the status.
     */
    public function getStatusColor(): string
    {
        return $this->status->color();
    }

    /**
     * Get the color class for the verification status.
     */
    public function getVerificationStatusColor(): string
    {
        return $this->verification_status->color();
    }

    /**
     * Get the renewal requirements as a formatted string.
     */
    public function getRenewalRequirementsText(): string
    {
        if (empty($this->renewal_requirements)) {
            return 'No specific requirements';
        }

        return implode(', ', $this->renewal_requirements);
    }

    /**
     * Get the notes as a formatted string.
     */
    public function getNotesText(): string
    {
        if (empty($this->notes)) {
            return '';
        }

        return implode("\n", $this->notes);
    }

    /**
     * Get the attachment URL.
     */
    public function getAttachmentUrl(): ?string
    {
        if (!$this->attachment_path) {
            return null;
        }

        return asset('storage/' . $this->attachment_path);
    }

    /**
     * Check if the certification has an attachment.
     */
    public function hasAttachment(): bool
    {
        return !empty($this->attachment_path);
    }

    /**
     * Check if the certification has a verification URL.
     */
    public function hasVerificationUrl(): bool
    {
        return !empty($this->verification_url);
    }

    /**
     * Convert to array for API responses.
     */
    public function toApiArray(): array
    {
        return [
            'id' => $this->id,
            'provider_id' => $this->provider_id,
            'certification_name' => $this->certification_name,
            'certification_number' => $this->certification_number,
            'issuing_organization' => $this->issuing_organization,
            'category' => [
                'value' => $this->category->value,
                'label' => $this->getCategoryLabel(),
            ],
            'description' => $this->description,
            'issue_date' => $this->issue_date?->format('Y-m-d'),
            'expiry_date' => $this->expiry_date?->format('Y-m-d'),
            'renewal_date' => $this->renewal_date?->format('Y-m-d'),
            'status' => [
                'value' => $this->status->value,
                'label' => $this->getStatusLabel(),
                'color' => $this->getStatusColor(),
            ],
            'verification_status' => [
                'value' => $this->verification_status->value,
                'label' => $this->getVerificationStatusLabel(),
                'color' => $this->getVerificationStatusColor(),
            ],
            'verification_url' => $this->verification_url,
            'attachment_path' => $this->attachment_path,
            'attachment_url' => $this->getAttachmentUrl(),
            'credits_earned' => $this->credits_earned,
            'is_recurring' => $this->is_recurring,
            'renewal_period' => $this->renewal_period,
            'renewal_requirements' => $this->renewal_requirements,
            'renewal_requirements_text' => $this->getRenewalRequirementsText(),
            'verified_at' => $this->verified_at?->format('Y-m-d H:i:s'),
            'verified_by' => $this->verified_by,
            'notes' => $this->notes,
            'notes_text' => $this->getNotesText(),
            'is_active' => $this->isActive(),
            'is_expired' => $this->isExpired(),
            'is_verified' => $this->isVerified(),
            'is_expiring_soon' => $this->isExpiringSoon(),
            'needs_renewal' => $this->needsRenewal(),
            'days_until_expiration' => $this->daysUntilExpiration(),
            'days_since_issue' => $this->daysSinceIssue(),
            'age_in_years' => $this->getAgeInYears(),
            'is_valid' => $this->isValid(),
            'has_attachment' => $this->hasAttachment(),
            'has_verification_url' => $this->hasVerificationUrl(),
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
