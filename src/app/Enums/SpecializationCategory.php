<?php

namespace Fereydooni\Shopping\App\Enums;

enum SpecializationCategory: string
{
    case MEDICAL = 'medical';
    case LEGAL = 'legal';
    case TECHNICAL = 'technical';
    case FINANCIAL = 'financial';
    case EDUCATIONAL = 'educational';
    case CONSULTING = 'consulting';
    case RETAIL = 'retail';
    case MANUFACTURING = 'manufacturing';
    case LOGISTICS = 'logistics';
    case TECHNOLOGY = 'technology';
    case HEALTHCARE = 'healthcare';
    case FINANCE = 'finance';
    case EDUCATION = 'education';
    case LEGAL_SERVICES = 'legal_services';
    case CONSULTING_SERVICES = 'consulting_services';
    case OTHER = 'other';

    public function label(): string
    {
        return match ($this) {
            self::MEDICAL => 'Medical',
            self::LEGAL => 'Legal',
            self::TECHNICAL => 'Technical',
            self::FINANCIAL => 'Financial',
            self::EDUCATIONAL => 'Educational',
            self::CONSULTING => 'Consulting',
            self::RETAIL => 'Retail',
            self::MANUFACTURING => 'Manufacturing',
            self::LOGISTICS => 'Logistics',
            self::TECHNOLOGY => 'Technology',
            self::HEALTHCARE => 'Healthcare',
            self::FINANCE => 'Finance',
            self::EDUCATION => 'Education',
            self::LEGAL_SERVICES => 'Legal Services',
            self::CONSULTING_SERVICES => 'Consulting Services',
            self::OTHER => 'Other',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::MEDICAL => 'Medical and healthcare related specializations',
            self::LEGAL => 'Legal and compliance related specializations',
            self::TECHNICAL => 'Technical and engineering specializations',
            self::FINANCIAL => 'Financial and accounting specializations',
            self::EDUCATIONAL => 'Educational and training specializations',
            self::CONSULTING => 'Consulting and advisory specializations',
            self::RETAIL => 'Retail and consumer goods specializations',
            self::MANUFACTURING => 'Manufacturing and production specializations',
            self::LOGISTICS => 'Logistics and supply chain specializations',
            self::TECHNOLOGY => 'Technology and IT specializations',
            self::HEALTHCARE => 'Healthcare and wellness specializations',
            self::FINANCE => 'Finance and investment specializations',
            self::EDUCATION => 'Education and learning specializations',
            self::LEGAL_SERVICES => 'Legal services and compliance',
            self::CONSULTING_SERVICES => 'Consulting and advisory services',
            self::OTHER => 'Other specialized areas',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::MEDICAL => 'danger',
            self::LEGAL => 'warning',
            self::TECHNICAL => 'info',
            self::FINANCIAL => 'success',
            self::EDUCATIONAL => 'primary',
            self::CONSULTING => 'secondary',
            self::RETAIL => 'light',
            self::MANUFACTURING => 'dark',
            self::LOGISTICS => 'info',
            self::TECHNOLOGY => 'primary',
            self::HEALTHCARE => 'danger',
            self::FINANCE => 'success',
            self::EDUCATION => 'primary',
            self::LEGAL_SERVICES => 'warning',
            self::CONSULTING_SERVICES => 'secondary',
            self::OTHER => 'secondary',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::MEDICAL => 'fa-stethoscope',
            self::LEGAL => 'fa-balance-scale',
            self::TECHNICAL => 'fa-cogs',
            self::FINANCIAL => 'fa-chart-line',
            self::EDUCATIONAL => 'fa-graduation-cap',
            self::CONSULTING => 'fa-lightbulb',
            self::RETAIL => 'fa-shopping-cart',
            self::MANUFACTURING => 'fa-industry',
            self::LOGISTICS => 'fa-truck',
            self::TECHNOLOGY => 'fa-laptop-code',
            self::HEALTHCARE => 'fa-heartbeat',
            self::FINANCE => 'fa-coins',
            self::EDUCATION => 'fa-book',
            self::LEGAL_SERVICES => 'fa-gavel',
            self::CONSULTING_SERVICES => 'fa-users',
            self::OTHER => 'fa-star',
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

    public static function toArray(): array
    {
        return array_map(fn ($case) => [
            'value' => $case->value,
            'label' => $case->label(),
            'description' => $case->description(),
            'color' => $case->color(),
            'icon' => $case->icon(),
        ], self::cases());
    }
}
