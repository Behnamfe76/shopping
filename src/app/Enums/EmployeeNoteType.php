<?php

namespace Fereydooni\Shopping\app\Enums;

enum EmployeeNoteType: string
{
    case PERFORMANCE = 'performance';
    case GENERAL = 'general';
    case WARNING = 'warning';
    case PRAISE = 'praise';
    case INCIDENT = 'incident';
    case TRAINING = 'training';
    case GOAL = 'goal';
    case FEEDBACK = 'feedback';
    case OTHER = 'other';

    public function label(): string
    {
        return match ($this) {
            self::PERFORMANCE => 'Performance',
            self::GENERAL => 'General',
            self::WARNING => 'Warning',
            self::PRAISE => 'Praise',
            self::INCIDENT => 'Incident',
            self::TRAINING => 'Training',
            self::GOAL => 'Goal',
            self::FEEDBACK => 'Feedback',
            self::OTHER => 'Other',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::PERFORMANCE => 'blue',
            self::GENERAL => 'gray',
            self::WARNING => 'yellow',
            self::PRAISE => 'green',
            self::INCIDENT => 'red',
            self::TRAINING => 'purple',
            self::GOAL => 'indigo',
            self::FEEDBACK => 'pink',
            self::OTHER => 'gray',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::PERFORMANCE => 'chart-line',
            self::GENERAL => 'note',
            self::WARNING => 'exclamation-triangle',
            self::PRAISE => 'star',
            self::INCIDENT => 'exclamation-circle',
            self::TRAINING => 'graduation-cap',
            self::GOAL => 'target',
            self::FEEDBACK => 'comments',
            self::OTHER => 'ellipsis-h',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::PERFORMANCE => 'Performance-related notes and evaluations',
            self::GENERAL => 'General information and notes',
            self::WARNING => 'Warning notices and disciplinary actions',
            self::PRAISE => 'Recognition and positive feedback',
            self::INCIDENT => 'Incident reports and safety issues',
            self::TRAINING => 'Training progress and completion notes',
            self::GOAL => 'Goal setting and progress tracking',
            self::FEEDBACK => 'General feedback and suggestions',
            self::OTHER => 'Miscellaneous notes and information',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function labels(): array
    {
        return array_combine(
            array_column(self::cases(), 'value'),
            array_map(fn ($case) => $case->label(), self::cases())
        );
    }

    public static function fromLabel(string $label): ?self
    {
        foreach (self::cases() as $case) {
            if ($case->label() === $label) {
                return $case;
            }
        }

        return null;
    }
}
