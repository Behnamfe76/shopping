<?php

namespace Fereydooni\Shopping\Database\Factories;

use Carbon\Carbon;
use Fereydooni\Shopping\Enums\TrainingMethod;
use Fereydooni\Shopping\Enums\TrainingStatus;
use Fereydooni\Shopping\Enums\TrainingType;
use Fereydooni\Shopping\Models\Employee;
use Fereydooni\Shopping\Models\EmployeeTraining;
use Illuminate\Database\Eloquent\Factories\Factory;

class EmployeeTrainingFactory extends Factory
{
    protected $model = EmployeeTraining::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        $startDate = $this->faker->dateTimeBetween('-6 months', '+1 month');
        $endDate = $this->faker->dateTimeBetween($startDate, '+6 months');
        $isCompleted = $this->faker->boolean(70);
        $completionDate = $isCompleted ? $this->faker->dateTimeBetween($startDate, $endDate) : null;
        $score = $isCompleted ? $this->faker->randomFloat(2, 60, 100) : null;
        $grade = $score ? $this->getGradeFromScore($score) : null;
        $hoursCompleted = $isCompleted ? $this->faker->randomFloat(2, 1, 40) : 0;
        $totalHours = $this->faker->randomFloat(2, 1, 40);
        $isCertification = $this->faker->boolean(30);
        $expiryDate = $isCertification ? $this->faker->dateTimeBetween('+1 month', '+2 years') : null;

        return [
            'employee_id' => Employee::factory(),
            'training_type' => $this->faker->randomElement(TrainingType::cases())->value,
            'training_name' => $this->getTrainingName(),
            'provider' => $this->faker->company(),
            'description' => $this->faker->paragraph(),
            'start_date' => $startDate,
            'end_date' => $endDate,
            'completion_date' => $completionDate,
            'status' => $this->getStatus($isCompleted, $startDate, $endDate),
            'score' => $score,
            'grade' => $grade,
            'certificate_number' => $isCertification && $isCompleted ? 'CERT-'.$this->faker->unique()->numberBetween(1000, 9999) : null,
            'certificate_url' => $isCertification && $isCompleted ? $this->faker->url() : null,
            'hours_completed' => $hoursCompleted,
            'total_hours' => $totalHours,
            'cost' => $this->faker->randomFloat(2, 50, 2000),
            'is_mandatory' => $this->faker->boolean(20),
            'is_certification' => $isCertification,
            'is_renewable' => $isCertification ? $this->faker->boolean(60) : false,
            'renewal_date' => $isCertification && $isCompleted ? $this->faker->dateTimeBetween($completionDate, '+1 year') : null,
            'expiry_date' => $expiryDate,
            'instructor' => $this->faker->name(),
            'location' => $this->faker->city(),
            'training_method' => $this->faker->randomElement(TrainingMethod::cases())->value,
            'materials' => $this->getMaterials(),
            'notes' => $this->faker->optional(0.3)->paragraph(),
            'attachments' => $this->getAttachments(),
            'failure_reason' => null,
            'cancellation_reason' => null,
        ];
    }

    /**
     * Training in not started status.
     */
    public function notStarted(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => TrainingStatus::NOT_STARTED->value,
                'start_date' => $this->faker->dateTimeBetween('+1 week', '+3 months'),
                'end_date' => $this->faker->dateTimeBetween('+3 months', '+6 months'),
                'completion_date' => null,
                'score' => null,
                'grade' => null,
                'hours_completed' => 0,
                'certificate_number' => null,
                'certificate_url' => null,
            ];
        });
    }

    /**
     * Training in progress status.
     */
    public function inProgress(): static
    {
        return $this->state(function (array $attributes) {
            $startDate = $this->faker->dateTimeBetween('-2 months', 'now');
            $endDate = $this->faker->dateTimeBetween('+1 month', '+3 months');
            $hoursCompleted = $this->faker->randomFloat(2, 1, $attributes['total_hours'] ?? 20);

            return [
                'status' => TrainingStatus::IN_PROGRESS->value,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'completion_date' => null,
                'score' => null,
                'grade' => null,
                'hours_completed' => $hoursCompleted,
                'certificate_number' => null,
                'certificate_url' => null,
            ];
        });
    }

    /**
     * Completed training.
     */
    public function completed(): static
    {
        return $this->state(function (array $attributes) {
            $startDate = $this->faker->dateTimeBetween('-6 months', '-1 month');
            $endDate = $this->faker->dateTimeBetween($startDate, 'now');
            $completionDate = $this->faker->dateTimeBetween($startDate, $endDate);
            $score = $this->faker->randomFloat(2, 70, 100);
            $grade = $this->getGradeFromScore($score);

            return [
                'status' => TrainingStatus::COMPLETED->value,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'completion_date' => $completionDate,
                'score' => $score,
                'grade' => $grade,
                'hours_completed' => $attributes['total_hours'] ?? 20,
                'certificate_number' => $attributes['is_certification'] ? 'CERT-'.$this->faker->unique()->numberBetween(1000, 9999) : null,
                'certificate_url' => $attributes['is_certification'] ? $this->faker->url() : null,
            ];
        });
    }

    /**
     * Failed training.
     */
    public function failed(): static
    {
        return $this->state(function (array $attributes) {
            $startDate = $this->faker->dateTimeBetween('-3 months', '-1 month');
            $endDate = $this->faker->dateTimeBetween($startDate, 'now');
            $completionDate = $this->faker->dateTimeBetween($startDate, $endDate);
            $score = $this->faker->randomFloat(2, 0, 69);
            $grade = $this->getGradeFromScore($score);

            return [
                'status' => TrainingStatus::FAILED->value,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'completion_date' => $completionDate,
                'score' => $score,
                'grade' => $grade,
                'hours_completed' => $this->faker->randomFloat(2, 1, $attributes['total_hours'] ?? 20),
                'failure_reason' => $this->faker->randomElement([
                    'Insufficient study time',
                    'Difficulty understanding material',
                    'Technical issues during training',
                    'Personal circumstances',
                    'Inadequate preparation',
                ]),
                'certificate_number' => null,
                'certificate_url' => null,
            ];
        });
    }

    /**
     * Cancelled training.
     */
    public function cancelled(): static
    {
        return $this->state(function (array $attributes) {
            $startDate = $this->faker->dateTimeBetween('-2 months', 'now');
            $endDate = $this->faker->dateTimeBetween('+1 month', '+3 months');

            return [
                'status' => TrainingStatus::CANCELLED->value,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'completion_date' => null,
                'score' => null,
                'grade' => null,
                'hours_completed' => 0,
                'cancellation_reason' => $this->faker->randomElement([
                    'Schedule conflict',
                    'Budget constraints',
                    'Change in priorities',
                    'Employee request',
                    'Organizational changes',
                ]),
                'certificate_number' => null,
                'certificate_url' => null,
            ];
        });
    }

    /**
     * Mandatory training.
     */
    public function mandatory(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'is_mandatory' => true,
                'status' => $this->faker->randomElement([
                    TrainingStatus::NOT_STARTED->value,
                    TrainingStatus::IN_PROGRESS->value,
                    TrainingStatus::COMPLETED->value,
                ]),
            ];
        });
    }

    /**
     * Certification training.
     */
    public function certification(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'is_certification' => true,
                'is_renewable' => $this->faker->boolean(70),
                'expiry_date' => $this->faker->dateTimeBetween('+6 months', '+2 years'),
            ];
        });
    }

    /**
     * Technical training.
     */
    public function technical(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'training_type' => TrainingType::TECHNICAL->value,
                'training_name' => $this->faker->randomElement([
                    'Advanced JavaScript Development',
                    'Python for Data Science',
                    'AWS Cloud Architecture',
                    'DevOps Fundamentals',
                    'Machine Learning Basics',
                    'Database Design Principles',
                    'API Development',
                    'Cybersecurity Fundamentals',
                ]),
            ];
        });
    }

    /**
     * Soft skills training.
     */
    public function softSkills(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'training_type' => TrainingType::SOFT_SKILLS->value,
                'training_name' => $this->faker->randomElement([
                    'Effective Communication',
                    'Leadership Development',
                    'Team Building',
                    'Conflict Resolution',
                    'Time Management',
                    'Presentation Skills',
                    'Emotional Intelligence',
                    'Customer Service Excellence',
                ]),
            ];
        });
    }

    /**
     * Compliance training.
     */
    public function compliance(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'training_type' => TrainingType::COMPLIANCE->value,
                'training_name' => $this->faker->randomElement([
                    'Data Protection & Privacy',
                    'Anti-Harassment Training',
                    'Workplace Safety',
                    'Financial Compliance',
                    'Industry Regulations',
                    'Code of Conduct',
                    'Risk Management',
                    'Legal Compliance',
                ]),
                'is_mandatory' => true,
            ];
        });
    }

    /**
     * Online training.
     */
    public function online(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'training_method' => TrainingMethod::ONLINE->value,
                'location' => 'Online Platform',
            ];
        });
    }

    /**
     * In-person training.
     */
    public function inPerson(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'training_method' => TrainingMethod::IN_PERSON->value,
                'location' => $this->faker->city().' Training Center',
            ];
        });
    }

    /**
     * Get training name based on type.
     */
    protected function getTrainingName(): string
    {
        $names = [
            'technical' => [
                'Advanced Programming Concepts',
                'Web Development Fundamentals',
                'Database Management Systems',
                'Cloud Computing Basics',
                'Software Testing Methods',
                'System Architecture Design',
                'Network Security',
                'Mobile App Development',
            ],
            'soft_skills' => [
                'Effective Communication Skills',
                'Leadership and Management',
                'Team Collaboration',
                'Problem Solving Techniques',
                'Public Speaking',
                'Negotiation Skills',
                'Stress Management',
                'Creative Thinking',
            ],
            'compliance' => [
                'Workplace Safety Training',
                'Data Privacy Compliance',
                'Anti-Discrimination Training',
                'Financial Regulations',
                'Industry Standards',
                'Legal Requirements',
                'Risk Assessment',
                'Emergency Procedures',
            ],
            'safety' => [
                'Fire Safety Training',
                'First Aid Certification',
                'Workplace Hazard Recognition',
                'Emergency Response',
                'Safety Equipment Usage',
                'Accident Prevention',
                'Chemical Safety',
                'Electrical Safety',
            ],
            'leadership' => [
                'Strategic Leadership',
                'Team Management',
                'Decision Making',
                'Change Management',
                'Performance Management',
                'Coaching and Mentoring',
                'Strategic Planning',
                'Organizational Development',
            ],
            'product' => [
                'Product Knowledge Training',
                'Feature Overview',
                'Customer Use Cases',
                'Product Updates',
                'Competitive Analysis',
                'Sales Techniques',
                'Product Demonstration',
                'Customer Support',
            ],
            'other' => [
                'Company Policies',
                'New Employee Orientation',
                'Diversity Training',
                'Wellness Programs',
                'Language Skills',
                'Cultural Awareness',
                'Professional Development',
                'Industry Trends',
            ],
        ];

        $type = $this->faker->randomElement(array_keys($names));

        return $this->faker->randomElement($names[$type]);
    }

    /**
     * Get materials array.
     */
    protected function getMaterials(): array
    {
        return [
            'handbooks' => $this->faker->randomElement([true, false]),
            'videos' => $this->faker->randomElement([true, false]),
            'presentations' => $this->faker->randomElement([true, false]),
            'exercises' => $this->faker->randomElement([true, false]),
            'assessments' => $this->faker->randomElement([true, false]),
            'certificates' => $this->faker->randomElement([true, false]),
        ];
    }

    /**
     * Get attachments array.
     */
    protected function getAttachments(): array
    {
        $attachments = [];
        $count = $this->faker->numberBetween(0, 3);

        for ($i = 0; $i < $count; $i++) {
            $attachments[] = [
                'name' => $this->faker->words(3, true).'.pdf',
                'url' => $this->faker->url(),
                'size' => $this->faker->numberBetween(100, 5000).'KB',
                'type' => 'application/pdf',
            ];
        }

        return $attachments;
    }

    /**
     * Get status based on completion and dates.
     */
    protected function getStatus(bool $isCompleted, $startDate, $endDate): string
    {
        if ($isCompleted) {
            return TrainingStatus::COMPLETED->value;
        }

        $now = now();
        $startDate = Carbon::parse($startDate);
        $endDate = Carbon::parse($endDate);

        if ($now < $startDate) {
            return TrainingStatus::NOT_STARTED->value;
        } elseif ($now >= $startDate && $now <= $endDate) {
            return TrainingStatus::IN_PROGRESS->value;
        } else {
            return TrainingStatus::FAILED->value;
        }
    }

    /**
     * Get grade from score.
     */
    protected function getGradeFromScore(float $score): string
    {
        if ($score >= 90) {
            return 'A';
        }
        if ($score >= 80) {
            return 'B';
        }
        if ($score >= 70) {
            return 'C';
        }
        if ($score >= 60) {
            return 'D';
        }

        return 'F';
    }
}
