<?php

namespace App\DTOs;

use App\Enums\ProficiencyLevel;
use App\Enums\SkillCategory;
use App\Models\EmployeeSkill;
use Carbon\Carbon;
use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Casts\DateTimeInterfaceCast;
use Spatie\LaravelData\Casts\EnumCast;
use Spatie\LaravelData\Data;

class EmployeeSkillDTO extends Data
{
    public function __construct(
        public ?int $id,
        public int $employee_id,
        public string $skill_name,
        #[WithCast(EnumCast::class)]
        public SkillCategory $skill_category,
        #[WithCast(EnumCast::class)]
        public ProficiencyLevel $proficiency_level,
        public int $years_experience,
        public bool $certification_required,
        public ?string $certification_name,
        #[WithCast(DateTimeInterfaceCast::class)]
        public ?Carbon $certification_date,
        #[WithCast(DateTimeInterfaceCast::class)]
        public ?Carbon $certification_expiry,
        public bool $is_verified,
        public ?int $verified_by,
        #[WithCast(DateTimeInterfaceCast::class)]
        public ?Carbon $verified_at,
        public bool $is_active,
        public bool $is_primary,
        public bool $is_required,
        public ?string $skill_description,
        public ?array $keywords,
        public ?array $tags,
        public ?string $notes,
        public ?array $attachments,
        #[WithCast(DateTimeInterfaceCast::class)]
        public ?Carbon $created_at,
        #[WithCast(DateTimeInterfaceCast::class)]
        public ?Carbon $updated_at,
        #[WithCast(DateTimeInterfaceCast::class)]
        public ?Carbon $deleted_at,
        // Relationships
        public ?EmployeeDTO $employee = null,
        public ?UserDTO $verified_by_user = null,
    ) {}

    public static function fromModel(EmployeeSkill $skill): self
    {
        return new self(
            id: $skill->id,
            employee_id: $skill->employee_id,
            skill_name: $skill->skill_name,
            skill_category: $skill->skill_category,
            proficiency_level: $skill->proficiency_level,
            years_experience: $skill->years_experience,
            certification_required: $skill->certification_required,
            certification_name: $skill->certification_name,
            certification_date: $skill->certification_date,
            certification_expiry: $skill->certification_expiry,
            is_verified: $skill->is_verified,
            verified_by: $skill->verified_by,
            verified_at: $skill->verified_at,
            is_active: $skill->is_active,
            is_primary: $skill->is_primary,
            is_required: $skill->is_required,
            skill_description: $skill->skill_description,
            keywords: $skill->keywords,
            tags: $skill->tags,
            notes: $skill->notes,
            attachments: $skill->attachments,
            created_at: $skill->created_at,
            updated_at: $skill->updated_at,
            deleted_at: $skill->deleted_at,
            employee: $skill->employee ? EmployeeDTO::fromModel($skill->employee) : null,
            verified_by_user: $skill->verifiedBy ? UserDTO::fromModel($skill->verifiedBy) : null,
        );
    }

    public static function rules(): array
    {
        return [
            'employee_id' => ['required', 'integer', 'exists:employees,id'],
            'skill_name' => ['required', 'string', 'max:255'],
            'skill_category' => ['required', 'string', 'in:'.implode(',', SkillCategory::values())],
            'proficiency_level' => ['required', 'string', 'in:'.implode(',', ProficiencyLevel::values())],
            'years_experience' => ['required', 'integer', 'min:0', 'max:50'],
            'certification_required' => ['boolean'],
            'certification_name' => ['nullable', 'string', 'max:255'],
            'certification_date' => ['nullable', 'date'],
            'certification_expiry' => ['nullable', 'date', 'after:certification_date'],
            'is_verified' => ['boolean'],
            'verified_by' => ['nullable', 'integer', 'exists:users,id'],
            'verified_at' => ['nullable', 'date'],
            'is_active' => ['boolean'],
            'is_primary' => ['boolean'],
            'is_required' => ['boolean'],
            'skill_description' => ['nullable', 'string', 'max:1000'],
            'keywords' => ['nullable', 'array'],
            'keywords.*' => ['string', 'max:100'],
            'tags' => ['nullable', 'array'],
            'tags.*' => ['string', 'max:100'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'attachments' => ['nullable', 'array'],
            'attachments.*' => ['string', 'max:255'],
        ];
    }

    public static function messages(): array
    {
        return [
            'employee_id.required' => 'Employee ID is required.',
            'employee_id.exists' => 'The selected employee does not exist.',
            'skill_name.required' => 'Skill name is required.',
            'skill_name.max' => 'Skill name cannot exceed 255 characters.',
            'skill_category.required' => 'Skill category is required.',
            'skill_category.in' => 'Invalid skill category selected.',
            'proficiency_level.required' => 'Proficiency level is required.',
            'proficiency_level.in' => 'Invalid proficiency level selected.',
            'years_experience.required' => 'Years of experience is required.',
            'years_experience.min' => 'Years of experience cannot be negative.',
            'years_experience.max' => 'Years of experience cannot exceed 50.',
            'certification_name.max' => 'Certification name cannot exceed 255 characters.',
            'certification_expiry.after' => 'Certification expiry date must be after certification date.',
            'verified_by.exists' => 'The selected verifier does not exist.',
            'skill_description.max' => 'Skill description cannot exceed 1000 characters.',
            'keywords.*.max' => 'Keyword cannot exceed 100 characters.',
            'tags.*.max' => 'Tag cannot exceed 100 characters.',
            'notes.max' => 'Notes cannot exceed 1000 characters.',
            'attachments.*.max' => 'Attachment path cannot exceed 255 characters.',
        ];
    }

    /**
     * Get the skill's display name with category.
     */
    public function getDisplayName(): string
    {
        return "{$this->skill_name} ({$this->skill_category->label()})";
    }

    /**
     * Get the skill's full description with proficiency level.
     */
    public function getFullDescription(): string
    {
        $description = "{$this->skill_name} - {$this->proficiency_level->label()}";

        if ($this->years_experience > 0) {
            $description .= " ({$this->years_experience} years)";
        }

        if ($this->certification_name) {
            $description .= " - Certified: {$this->certification_name}";
        }

        return $description;
    }

    /**
     * Check if the skill certification is expiring soon.
     */
    public function isCertificationExpiring(int $days = 30): bool
    {
        if (! $this->certification_expiry) {
            return false;
        }

        return $this->certification_expiry->isBetween(
            now(),
            now()->addDays($days)
        );
    }

    /**
     * Check if the skill certification has expired.
     */
    public function isCertificationExpired(): bool
    {
        if (! $this->certification_expiry) {
            return false;
        }

        return $this->certification_expiry->isPast();
    }

    /**
     * Get the skill's proficiency level as a numeric value.
     */
    public function getProficiencyNumericValue(): int
    {
        return $this->proficiency_level->numericValue();
    }

    /**
     * Check if the skill is a technical skill.
     */
    public function isTechnical(): bool
    {
        return $this->skill_category === SkillCategory::TECHNICAL;
    }

    /**
     * Check if the skill is a soft skill.
     */
    public function isSoftSkill(): bool
    {
        return $this->skill_category === SkillCategory::SOFT_SKILLS;
    }

    /**
     * Check if the skill is a language.
     */
    public function isLanguage(): bool
    {
        return $this->skill_category === SkillCategory::LANGUAGES;
    }

    /**
     * Get the skill's keywords as a string.
     */
    public function getKeywordsString(): string
    {
        return $this->keywords ? implode(', ', $this->keywords) : '';
    }

    /**
     * Get the skill's tags as a string.
     */
    public function getTagsString(): string
    {
        return $this->tags ? implode(', ', $this->tags) : '';
    }

    /**
     * Check if the skill has a specific keyword.
     */
    public function hasKeyword(string $keyword): bool
    {
        return $this->keywords && in_array(strtolower($keyword), array_map('strtolower', $this->keywords));
    }

    /**
     * Check if the skill has a specific tag.
     */
    public function hasTag(string $tag): bool
    {
        return $this->tags && in_array(strtolower($tag), array_map('strtolower', $this->tags));
    }

    /**
     * Get the skill's status summary.
     */
    public function getStatusSummary(): string
    {
        $status = [];

        if ($this->is_verified) {
            $status[] = 'Verified';
        }

        if ($this->is_primary) {
            $status[] = 'Primary';
        }

        if ($this->is_required) {
            $status[] = 'Required';
        }

        if ($this->certification_name) {
            $status[] = 'Certified';
        }

        if ($this->isCertificationExpired()) {
            $status[] = 'Certification Expired';
        } elseif ($this->isCertificationExpiring()) {
            $status[] = 'Certification Expiring';
        }

        return $status ? implode(', ', $status) : 'Active';
    }
}
