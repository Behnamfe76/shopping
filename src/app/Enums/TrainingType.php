<?php

namespace Fereydooni\Shopping\Enums;

enum TrainingType: string
{
    case TECHNICAL = 'technical';
    case SOFT_SKILLS = 'soft_skills';
    case COMPLIANCE = 'compliance';
    case SAFETY = 'safety';
    case LEADERSHIP = 'leadership';
    case PRODUCT = 'product';
    case OTHER = 'other';

    public function label(): string
    {
        return match($this) {
            self::TECHNICAL => 'Technical Training',
            self::SOFT_SKILLS => 'Soft Skills Training',
            self::COMPLIANCE => 'Compliance Training',
            self::SAFETY => 'Safety Training',
            self::LEADERSHIP => 'Leadership Training',
            self::PRODUCT => 'Product Training',
            self::OTHER => 'Other Training',
        };
    }

    public function description(): string
    {
        return match($this) {
            self::TECHNICAL => 'Technical skills and knowledge development',
            self::SOFT_SKILLS => 'Communication, teamwork, and interpersonal skills',
            self::COMPLIANCE => 'Regulatory and policy compliance training',
            self::SAFETY => 'Workplace safety and emergency procedures',
            self::LEADERSHIP => 'Management and leadership development',
            self::PRODUCT => 'Product knowledge and features training',
            self::OTHER => 'Miscellaneous training programs',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function labels(): array
    {
        return array_combine(
            self::values(),
            array_map(fn($case) => $case->label(), self::cases())
        );
    }
}
