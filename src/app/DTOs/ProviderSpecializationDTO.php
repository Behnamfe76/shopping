<?php

namespace Fereydooni\Shopping\App\DTOs;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Attributes\Validation\Rule;
use Fereydooni\Shopping\App\Models\ProviderSpecialization;
use Fereydooni\Shopping\App\Enums\SpecializationCategory;
use Fereydooni\Shopping\App\Enums\ProficiencyLevel;
use Fereydooni\Shopping\App\Enums\VerificationStatus;
use Carbon\Carbon;

class ProviderSpecializationDTO extends Data
{
    public function __construct(
        public ?int $id = null,
        public int $provider_id,
        #[Rule('required|string|max:255')]
        public string $specialization_name,
        #[Rule('required|string|in:' . implode(',', SpecializationCategory::values()))]
        public string $category,
        #[Rule('nullable|string|max:1000')]
        public ?string $description = null,
        #[Rule('nullable|integer|min:0|max:50')]
        public ?int $years_experience = 0,
        #[Rule('required|string|in:' . implode(',', ProficiencyLevel::values()))]
        public string $proficiency_level,
        #[Rule('nullable|array')]
        public ?array $certifications = null,
        #[Rule('boolean')]
        public bool $is_primary = false,
        #[Rule('boolean')]
        public bool $is_active = true,
        #[Rule('required|string|in:' . implode(',', VerificationStatus::values()))]
        public string $verification_status = VerificationStatus::UNVERIFIED->value,
        #[Rule('nullable|date')]
        public ?Carbon $verified_at = null,
        #[Rule('nullable|integer|exists:users,id')]
        public ?int $verified_by = null,
        #[Rule('nullable|array')]
        public ?array $notes = null,
        #[Rule('nullable|date')]
        public ?Carbon $created_at = null,
        #[Rule('nullable|date')]
        public ?Carbon $updated_at = null,
        // Additional computed fields
        public ?string $experience_level = null,
        public ?string $verification_status_label = null,
        public ?string $verification_status_color = null,
        public ?string $category_label = null,
        public ?string $category_color = null,
        public ?string $category_icon = null,
        public ?string $proficiency_level_label = null,
        public ?string $proficiency_level_description = null,
        public ?int $proficiency_level_numeric_value = null,
        public ?int $age_in_days = null,
        public ?int $days_since_verification = null,
        public ?bool $needs_renewal = null,
    ) {
        // Set computed fields if not provided
        if ($this->experience_level === null && $this->years_experience !== null) {
            $this->experience_level = $this->calculateExperienceLevel();
        }

        if ($this->verification_status_label === null) {
            $this->verification_status_label = $this->getVerificationStatusLabel();
        }

        if ($this->verification_status_color === null) {
            $this->verification_status_color = $this->getVerificationStatusColor();
        }

        if ($this->category_label === null) {
            $this->category_label = $this->getCategoryLabel();
        }

        if ($this->category_color === null) {
            $this->category_color = $this->getCategoryColor();
        }

        if ($this->category_icon === null) {
            $this->category_icon = $this->getCategoryIcon();
        }

        if ($this->proficiency_level_label === null) {
            $this->proficiency_level_label = $this->getProficiencyLevelLabel();
        }

        if ($this->proficiency_level_description === null) {
            $this->proficiency_level_description = $this->getProficiencyLevelDescription();
        }

        if ($this->proficiency_level_numeric_value === null) {
            $this->proficiency_level_numeric_value = $this->getProficiencyLevelNumericValue();
        }

        if ($this->age_in_days === null && $this->created_at !== null) {
            $this->age_in_days = $this->calculateAgeInDays();
        }

        if ($this->days_since_verification === null && $this->verified_at !== null) {
            $this->days_since_verification = $this->calculateDaysSinceVerification();
        }

        if ($this->needs_renewal === null) {
            $this->needs_renewal = $this->calculateNeedsRenewal();
        }
    }

    /**
     * Create DTO from ProviderSpecialization model.
     */
    public static function fromModel(ProviderSpecialization $specialization): self
    {
        return new self(
            id: $specialization->id,
            provider_id: $specialization->provider_id,
            specialization_name: $specialization->specialization_name,
            category: $specialization->category->value,
            description: $specialization->description,
            years_experience: $specialization->years_experience,
            proficiency_level: $specialization->proficiency_level->value,
            certifications: $specialization->certifications,
            is_primary: $specialization->is_primary,
            is_active: $specialization->is_active,
            verification_status: $specialization->verification_status->value,
            verified_at: $specialization->verified_at,
            verified_by: $specialization->verified_by,
            notes: $specialization->notes,
            created_at: $specialization->created_at,
            updated_at: $specialization->updated_at,
        );
    }

    /**
     * Convert DTO to array for database operations.
     */
    public function toArray(): array
    {
        return [
            'provider_id' => $this->provider_id,
            'specialization_name' => $this->specialization_name,
            'category' => $this->category,
            'description' => $this->description,
            'years_experience' => $this->years_experience,
            'proficiency_level' => $this->proficiency_level,
            'certifications' => $this->certifications,
            'is_primary' => $this->is_primary,
            'is_active' => $this->is_active,
            'verification_status' => $this->verification_status,
            'verified_at' => $this->verified_at,
            'verified_by' => $this->verified_by,
            'notes' => $this->notes,
        ];
    }

    /**
     * Get validation rules for creating a new specialization.
     */
    public static function rules(): array
    {
        return [
            'provider_id' => ['required', 'integer', 'exists:providers,id'],
            'specialization_name' => ['required', 'string', 'max:255'],
            'category' => ['required', 'string', 'in:' . implode(',', SpecializationCategory::values())],
            'description' => ['nullable', 'string', 'max:1000'],
            'years_experience' => ['nullable', 'integer', 'min:0', 'max:50'],
            'proficiency_level' => ['required', 'string', 'in:' . implode(',', ProficiencyLevel::values())],
            'certifications' => ['nullable', 'array'],
            'is_primary' => ['boolean'],
            'is_active' => ['boolean'],
            'verification_status' => ['required', 'string', 'in:' . implode(',', VerificationStatus::values())],
            'verified_at' => ['nullable', 'date'],
            'verified_by' => ['nullable', 'integer', 'exists:users,id'],
            'notes' => ['nullable', 'array'],
        ];
    }

    /**
     * Get validation rules for updating a specialization.
     */
    public static function updateRules(int $id): array
    {
        return [
            'provider_id' => ['sometimes', 'integer', 'exists:providers,id'],
            'specialization_name' => ['sometimes', 'string', 'max:255'],
            'category' => ['sometimes', 'string', 'in:' . implode(',', SpecializationCategory::values())],
            'description' => ['nullable', 'string', 'max:1000'],
            'years_experience' => ['nullable', 'integer', 'min:0', 'max:50'],
            'proficiency_level' => ['sometimes', 'string', 'in:' . implode(',', ProficiencyLevel::values())],
            'certifications' => ['nullable', 'array'],
            'is_primary' => ['sometimes', 'boolean'],
            'is_active' => ['sometimes', 'boolean'],
            'verification_status' => ['sometimes', 'string', 'in:' . implode(',', VerificationStatus::values())],
            'verified_at' => ['nullable', 'date'],
            'verified_by' => ['nullable', 'integer', 'exists:users,id'],
            'notes' => ['nullable', 'array'],
        ];
    }

    /**
     * Get validation messages.
     */
    public static function messages(): array
    {
        return [
            'provider_id.required' => 'Provider ID is required.',
            'provider_id.exists' => 'The selected provider does not exist.',
            'specialization_name.required' => 'Specialization name is required.',
            'specialization_name.max' => 'Specialization name cannot exceed 255 characters.',
            'category.required' => 'Category is required.',
            'category.in' => 'The selected category is invalid.',
            'description.max' => 'Description cannot exceed 1000 characters.',
            'years_experience.integer' => 'Years of experience must be a number.',
            'years_experience.min' => 'Years of experience cannot be negative.',
            'years_experience.max' => 'Years of experience cannot exceed 50.',
            'proficiency_level.required' => 'Proficiency level is required.',
            'proficiency_level.in' => 'The selected proficiency level is invalid.',
            'verification_status.required' => 'Verification status is required.',
            'verification_status.in' => 'The selected verification status is invalid.',
            'verified_by.exists' => 'The selected verifier does not exist.',
        ];
    }

    /**
     * Check if the specialization is verified.
     */
    public function isVerified(): bool
    {
        return $this->verification_status === VerificationStatus::VERIFIED->value;
    }

    /**
     * Check if the specialization is pending verification.
     */
    public function isPending(): bool
    {
        return $this->verification_status === VerificationStatus::PENDING->value;
    }

    /**
     * Check if the specialization is rejected.
     */
    public function isRejected(): bool
    {
        return $this->verification_status === VerificationStatus::REJECTED->value;
    }

    /**
     * Check if the specialization is unverified.
     */
    public function isUnverified(): bool
    {
        return $this->verification_status === VerificationStatus::UNVERIFIED->value;
    }

    /**
     * Check if the specialization is primary.
     */
    public function isPrimary(): bool
    {
        return $this->is_primary;
    }

    /**
     * Check if the specialization is active.
     */
    public function isActive(): bool
    {
        return $this->is_active;
    }

    /**
     * Calculate experience level based on years of experience.
     */
    private function calculateExperienceLevel(): string
    {
        if ($this->years_experience >= 10) {
            return 'Senior';
        } elseif ($this->years_experience >= 5) {
            return 'Mid-level';
        } elseif ($this->years_experience >= 2) {
            return 'Junior';
        } else {
            return 'Entry-level';
        }
    }

    /**
     * Get verification status label.
     */
    private function getVerificationStatusLabel(): string
    {
        return VerificationStatus::from($this->verification_status)->label();
    }

    /**
     * Get verification status color.
     */
    private function getVerificationStatusColor(): string
    {
        return VerificationStatus::from($this->verification_status)->color();
    }

    /**
     * Get category label.
     */
    private function getCategoryLabel(): string
    {
        return SpecializationCategory::from($this->category)->label();
    }

    /**
     * Get category color.
     */
    private function getCategoryColor(): string
    {
        return SpecializationCategory::from($this->category)->color();
    }

    /**
     * Get category icon.
     */
    private function getCategoryIcon(): string
    {
        return SpecializationCategory::from($this->category)->icon();
    }

    /**
     * Get proficiency level label.
     */
    private function getProficiencyLevelLabel(): string
    {
        return ProficiencyLevel::from($this->proficiency_level)->label();
    }

    /**
     * Get proficiency level description.
     */
    private function getProficiencyLevelDescription(): string
    {
        return ProficiencyLevel::from($this->proficiency_level)->description();
    }

    /**
     * Get proficiency level numeric value.
     */
    private function getProficiencyLevelNumericValue(): int
    {
        return ProficiencyLevel::from($this->proficiency_level)->numericValue();
    }

    /**
     * Calculate age in days.
     */
    private function calculateAgeInDays(): int
    {
        if (!$this->created_at) {
            return 0;
        }
        return $this->created_at->diffInDays(now());
    }

    /**
     * Calculate days since verification.
     */
    private function calculateDaysSinceVerification(): ?int
    {
        if (!$this->verified_at) {
            return null;
        }
        return $this->verified_at->diffInDays(now());
    }

    /**
     * Calculate if renewal is needed.
     */
    private function calculateNeedsRenewal(int $renewalThresholdDays = 365): bool
    {
        if (!$this->verified_at) {
            return false;
        }
        return $this->verified_at->diffInDays(now()) > $renewalThresholdDays;
    }

    /**
     * Add a note to the specialization.
     */
    public function addNote(string $note, string $type = 'general'): void
    {
        $notes = $this->notes ?? [];
        $notes[] = [
            'type' => $type,
            'note' => $note,
            'timestamp' => now()->toISOString(),
        ];
        $this->notes = $notes;
    }

    /**
     * Get the most recent note of a specific type.
     */
    public function getLatestNote(string $type = 'general'): ?array
    {
        if (!$this->notes) {
            return null;
        }

        $typeNotes = array_filter($this->notes, fn($note) => $note['type'] === $type);

        if (empty($typeNotes)) {
            return null;
        }

        usort($typeNotes, fn($a, $b) => strtotime($b['timestamp']) - strtotime($a['timestamp']));
        return reset($typeNotes);
    }

    /**
     * Get all notes of a specific type.
     */
    public function getNotesByType(string $type): array
    {
        if (!$this->notes) {
            return [];
        }

        return array_filter($this->notes, fn($note) => $note['type'] === $type);
    }

    /**
     * Check if the specialization has certifications.
     */
    public function hasCertifications(): bool
    {
        return !empty($this->certifications);
    }

    /**
     * Get the number of certifications.
     */
    public function getCertificationCount(): int
    {
        return is_array($this->certifications) ? count($this->certifications) : 0;
    }

    /**
     * Get the specialization summary.
     */
    public function getSummary(): string
    {
        $parts = [
            $this->specialization_name,
            "({$this->category_label})",
            "- {$this->proficiency_level_label}",
        ];

        if ($this->years_experience > 0) {
            $parts[] = "({$this->years_experience} years)";
        }

        if ($this->is_primary) {
            $parts[] = "[Primary]";
        }

        return implode(' ', $parts);
    }
}
