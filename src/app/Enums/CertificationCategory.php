<?php

namespace Fereydooni\Shopping\App\Enums;

enum CertificationCategory: string
{
    case PROFESSIONAL = 'professional';
    case TECHNICAL = 'technical';
    case SAFETY = 'safety';
    case COMPLIANCE = 'compliance';
    case EDUCATIONAL = 'educational';
    case INDUSTRY_SPECIFIC = 'industry_specific';
    case OTHER = 'other';

    /**
     * Get the display name for the certification category.
     */
    public function label(): string
    {
        return match($this) {
            self::PROFESSIONAL => 'Professional',
            self::TECHNICAL => 'Technical',
            self::SAFETY => 'Safety',
            self::COMPLIANCE => 'Compliance',
            self::EDUCATIONAL => 'Educational',
            self::INDUSTRY_SPECIFIC => 'Industry Specific',
            self::OTHER => 'Other',
        };
    }

    /**
     * Get the description for the certification category.
     */
    public function description(): string
    {
        return match($this) {
            self::PROFESSIONAL => 'Professional certifications and licenses',
            self::TECHNICAL => 'Technical skills and competencies',
            self::SAFETY => 'Safety training and certifications',
            self::COMPLIANCE => 'Regulatory and compliance certifications',
            self::EDUCATIONAL => 'Educational qualifications and degrees',
            self::INDUSTRY_SPECIFIC => 'Industry-specific certifications',
            self::OTHER => 'Other types of certifications',
        };
    }

    /**
     * Get all certification categories as an array.
     */
    public static function toArray(): array
    {
        return array_map(fn($case) => [
            'value' => $case->value,
            'label' => $case->label(),
            'description' => $case->description(),
        ], self::cases());
    }

    /**
     * Check if the certification category is industry-specific.
     */
    public function isIndustrySpecific(): bool
    {
        return $this === self::INDUSTRY_SPECIFIC;
    }

    /**
     * Check if the certification category requires renewal.
     */
    public function requiresRenewal(): bool
    {
        return in_array($this, [
            self::PROFESSIONAL,
            self::TECHNICAL,
            self::SAFETY,
            self::COMPLIANCE,
        ]);
    }
}
