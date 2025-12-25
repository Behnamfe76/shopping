<?php

namespace App\Enums;

enum SkillCategory: string
{
    case TECHNICAL = 'technical';
    case SOFT_SKILLS = 'soft_skills';
    case LANGUAGES = 'languages';
    case TOOLS = 'tools';
    case METHODOLOGIES = 'methodologies';
    case CERTIFICATIONS = 'certifications';
    case OTHER = 'other';

    public function label(): string
    {
        return match ($this) {
            self::TECHNICAL => 'Technical Skills',
            self::SOFT_SKILLS => 'Soft Skills',
            self::LANGUAGES => 'Languages',
            self::TOOLS => 'Tools',
            self::METHODOLOGIES => 'Methodologies',
            self::CERTIFICATIONS => 'Certifications',
            self::OTHER => 'Other',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::TECHNICAL => 'Technical and programming skills',
            self::SOFT_SKILLS => 'Interpersonal and communication skills',
            self::LANGUAGES => 'Spoken and written languages',
            self::TOOLS => 'Software tools and applications',
            self::METHODOLOGIES => 'Processes and methodologies',
            self::CERTIFICATIONS => 'Professional certifications',
            self::OTHER => 'Other skills not categorized',
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
            array_map(fn ($case) => $case->label(), self::cases())
        );
    }
}
