<?php

namespace Fereydooni\Shopping\Database\Seeders;

use Illuminate\Database\Seeder;
use Fereydooni\Shopping\Models\EmployeeTraining;
use Fereydooni\Shopping\Models\Employee;
use Fereydooni\Shopping\Enums\TrainingType;
use Fereydooni\Shopping\Enums\TrainingStatus;
use Fereydooni\Shopping\Enums\TrainingMethod;
use Carbon\Carbon;

class EmployeeTrainingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $employees = Employee::all();

        if ($employees->isEmpty()) {
            $this->command->warn('No employees found. Please run EmployeeSeeder first.');
            return;
        }

        $this->command->info('Seeding employee training data...');

        // Create mandatory compliance trainings
        $this->createMandatoryComplianceTrainings($employees);

        // Create technical trainings
        $this->createTechnicalTrainings($employees);

        // Create soft skills trainings
        $this->createSoftSkillsTrainings($employees);

        // Create safety trainings
        $this->createSafetyTrainings($employees);

        // Create leadership trainings
        $this->createLeadershipTrainings($employees);

        // Create product trainings
        $this->createProductTrainings($employees);

        $this->command->info('Employee training data seeded successfully!');
    }

    /**
     * Create mandatory compliance trainings.
     */
    protected function createMandatoryComplianceTrainings($employees): void
    {
        $complianceTrainings = [
            [
                'name' => 'Workplace Safety Training',
                'provider' => 'Safety First Institute',
                'description' => 'Mandatory workplace safety training covering emergency procedures, hazard recognition, and safety protocols.',
                'total_hours' => 4.0,
                'cost' => 150.00,
                'is_mandatory' => true,
                'is_certification' => true,
                'is_renewable' => true,
                'expiry_date' => now()->addYear(),
            ],
            [
                'name' => 'Anti-Harassment Training',
                'provider' => 'HR Compliance Solutions',
                'description' => 'Training on preventing workplace harassment, discrimination, and creating an inclusive environment.',
                'total_hours' => 2.0,
                'cost' => 100.00,
                'is_mandatory' => true,
                'is_certification' => false,
                'is_renewable' => false,
            ],
            [
                'name' => 'Data Protection & Privacy',
                'provider' => 'Cyber Security Academy',
                'description' => 'Training on data protection laws, privacy regulations, and secure handling of sensitive information.',
                'total_hours' => 3.0,
                'cost' => 200.00,
                'is_mandatory' => true,
                'is_certification' => true,
                'is_renewable' => true,
                'expiry_date' => now()->addMonths(18),
            ],
        ];

        foreach ($complianceTrainings as $trainingData) {
            $this->createTrainingForEmployees($employees, $trainingData, TrainingType::COMPLIANCE, TrainingMethod::ONLINE);
        }
    }

    /**
     * Create technical trainings.
     */
    protected function createTechnicalTrainings($employees): void
    {
        $technicalTrainings = [
            [
                'name' => 'Advanced JavaScript Development',
                'provider' => 'Code Academy',
                'description' => 'Advanced JavaScript concepts including ES6+, async programming, and modern frameworks.',
                'total_hours' => 20.0,
                'cost' => 500.00,
                'is_mandatory' => false,
                'is_certification' => true,
                'is_renewable' => false,
            ],
            [
                'name' => 'Python for Data Science',
                'provider' => 'Data Science Institute',
                'description' => 'Python programming for data analysis, machine learning, and statistical computing.',
                'total_hours' => 25.0,
                'cost' => 750.00,
                'is_mandatory' => false,
                'is_certification' => true,
                'is_renewable' => false,
            ],
            [
                'name' => 'AWS Cloud Architecture',
                'provider' => 'Cloud Computing Academy',
                'description' => 'Amazon Web Services cloud architecture, deployment, and management.',
                'total_hours' => 30.0,
                'cost' => 1200.00,
                'is_mandatory' => false,
                'is_certification' => true,
                'is_renewable' => true,
                'expiry_date' => now()->addYears(2),
            ],
        ];

        foreach ($technicalTrainings as $trainingData) {
            $this->createTrainingForEmployees($employees->random(rand(3, 8)), $trainingData, TrainingType::TECHNICAL, TrainingMethod::HYBRID);
        }
    }

    /**
     * Create soft skills trainings.
     */
    protected function createSoftSkillsTrainings($employees): void
    {
        $softSkillsTrainings = [
            [
                'name' => 'Effective Communication Skills',
                'provider' => 'Communication Institute',
                'description' => 'Improving verbal and written communication skills for professional success.',
                'total_hours' => 8.0,
                'cost' => 300.00,
                'is_mandatory' => false,
                'is_certification' => false,
                'is_renewable' => false,
            ],
            [
                'name' => 'Leadership Development',
                'provider' => 'Leadership Academy',
                'description' => 'Developing leadership skills, team management, and strategic thinking.',
                'total_hours' => 16.0,
                'cost' => 600.00,
                'is_mandatory' => false,
                'is_certification' => true,
                'is_renewable' => false,
            ],
            [
                'name' => 'Conflict Resolution',
                'provider' => 'Workplace Solutions',
                'description' => 'Techniques for resolving workplace conflicts and improving team dynamics.',
                'total_hours' => 6.0,
                'cost' => 250.00,
                'is_mandatory' => false,
                'is_certification' => false,
                'is_renewable' => false,
            ],
        ];

        foreach ($softSkillsTrainings as $trainingData) {
            $this->createTrainingForEmployees($employees->random(rand(5, 12)), $trainingData, TrainingType::SOFT_SKILLS, TrainingMethod::IN_PERSON);
        }
    }

    /**
     * Create safety trainings.
     */
    protected function createSafetyTrainings($employees): void
    {
        $safetyTrainings = [
            [
                'name' => 'First Aid Certification',
                'provider' => 'Red Cross Training',
                'description' => 'First aid and CPR certification for workplace emergency response.',
                'total_hours' => 6.0,
                'cost' => 150.00,
                'is_mandatory' => false,
                'is_certification' => true,
                'is_renewable' => true,
                'expiry_date' => now()->addYears(2),
            ],
            [
                'name' => 'Fire Safety Training',
                'provider' => 'Fire Safety Institute',
                'description' => 'Fire prevention, evacuation procedures, and fire extinguisher usage.',
                'total_hours' => 3.0,
                'cost' => 100.00,
                'is_mandatory' => false,
                'is_certification' => true,
                'is_renewable' => true,
                'expiry_date' => now()->addYear(),
            ],
        ];

        foreach ($safetyTrainings as $trainingData) {
            $this->createTrainingForEmployees($employees->random(rand(2, 6)), $trainingData, TrainingType::SAFETY, TrainingMethod::WORKSHOP);
        }
    }

    /**
     * Create leadership trainings.
     */
    protected function createLeadershipTrainings($employees): void
    {
        $leadershipTrainings = [
            [
                'name' => 'Strategic Leadership',
                'provider' => 'Executive Leadership Institute',
                'description' => 'Strategic thinking, decision making, and organizational leadership.',
                'total_hours' => 24.0,
                'cost' => 1500.00,
                'is_mandatory' => false,
                'is_certification' => true,
                'is_renewable' => false,
            ],
            [
                'name' => 'Change Management',
                'provider' => 'Change Management Academy',
                'description' => 'Leading organizational change, managing resistance, and implementing new initiatives.',
                'total_hours' => 12.0,
                'cost' => 800.00,
                'is_mandatory' => false,
                'is_certification' => true,
                'is_renewable' => false,
            ],
        ];

        // Only assign to senior employees or managers
        $seniorEmployees = $employees->filter(function ($employee) {
            return $employee->position && in_array($employee->position->level, ['senior', 'lead', 'manager', 'director']);
        });

        if ($seniorEmployees->isNotEmpty()) {
            foreach ($leadershipTrainings as $trainingData) {
                $this->createTrainingForEmployees($seniorEmployees->random(rand(1, 4)), $trainingData, TrainingType::LEADERSHIP, TrainingMethod::SEMINAR);
            }
        }
    }

    /**
     * Create product trainings.
     */
    protected function createProductTrainings($employees): void
    {
        $productTrainings = [
            [
                'name' => 'Product Knowledge Training',
                'provider' => 'Product Academy',
                'description' => 'Comprehensive training on company products, features, and use cases.',
                'total_hours' => 10.0,
                'cost' => 200.00,
                'is_mandatory' => false,
                'is_certification' => false,
                'is_renewable' => false,
            ],
            [
                'name' => 'Sales Techniques & Product Demo',
                'provider' => 'Sales Excellence Institute',
                'description' => 'Sales methodologies and product demonstration techniques.',
                'total_hours' => 14.0,
                'cost' => 400.00,
                'is_mandatory' => false,
                'is_certification' => true,
                'is_renewable' => false,
            ],
        ];

        // Assign to sales and customer-facing employees
        $salesEmployees = $employees->filter(function ($employee) {
            return $employee->position && in_array($employee->position->title, ['sales', 'account manager', 'customer success', 'support']);
        });

        if ($salesEmployees->isNotEmpty()) {
            foreach ($productTrainings as $trainingData) {
                $this->createTrainingForEmployees($salesEmployees->random(rand(2, 8)), $trainingData, TrainingType::PRODUCT, TrainingMethod::HYBRID);
            }
        }
    }

    /**
     * Create training for specific employees.
     */
    protected function createTrainingForEmployees($employees, array $trainingData, TrainingType $type, TrainingMethod $method): void
    {
        foreach ($employees as $employee) {
            $startDate = $this->getRandomStartDate();
            $endDate = $startDate->copy()->addDays(rand(7, 60));
            $isCompleted = rand(1, 100) <= 70; // 70% completion rate
            $completionDate = $isCompleted ? $this->getRandomCompletionDate($startDate, $endDate) : null;
            $score = $isCompleted ? rand(70, 100) : null;
            $grade = $score ? $this->getGradeFromScore($score) : null;
            $hoursCompleted = $isCompleted ? $trainingData['total_hours'] : rand(0, (int)$trainingData['total_hours']);

            EmployeeTraining::create([
                'employee_id' => $employee->id,
                'training_type' => $type->value,
                'training_name' => $trainingData['name'],
                'provider' => $trainingData['provider'],
                'description' => $trainingData['description'],
                'start_date' => $startDate,
                'end_date' => $endDate,
                'completion_date' => $completionDate,
                'status' => $this->getStatus($isCompleted, $startDate, $endDate),
                'score' => $score,
                'grade' => $grade,
                'certificate_number' => $trainingData['is_certification'] && $isCompleted ? 'CERT-' . rand(1000, 9999) : null,
                'certificate_url' => $trainingData['is_certification'] && $isCompleted ? 'https://certificates.example.com/' . rand(1000, 9999) : null,
                'hours_completed' => $hoursCompleted,
                'total_hours' => $trainingData['total_hours'],
                'cost' => $trainingData['cost'],
                'is_mandatory' => $trainingData['is_mandatory'],
                'is_certification' => $trainingData['is_certification'],
                'is_renewable' => $trainingData['is_renewable'],
                'renewal_date' => $trainingData['is_renewable'] && $isCompleted ? $this->getRandomRenewalDate($completionDate) : null,
                'expiry_date' => $trainingData['expiry_date'] ?? null,
                'instructor' => $this->getRandomInstructor(),
                'location' => $this->getRandomLocation($method),
                'training_method' => $method->value,
                'materials' => $this->getRandomMaterials(),
                'notes' => rand(1, 100) <= 30 ? $this->getRandomNotes() : null,
                'attachments' => $this->getRandomAttachments(),
                'failure_reason' => null,
                'cancellation_reason' => null,
            ]);
        }
    }

    /**
     * Get random start date.
     */
    protected function getRandomStartDate(): Carbon
    {
        $options = [
            now()->subMonths(6), // Past
            now()->subMonths(3), // Recent past
            now()->subDays(rand(1, 30)), // Recent
            now()->addDays(rand(1, 30)), // Near future
            now()->addMonths(rand(1, 3)), // Future
        ];

        return $options[array_rand($options)];
    }

    /**
     * Get random completion date.
     */
    protected function getRandomCompletionDate(Carbon $startDate, Carbon $endDate): Carbon
    {
        return $startDate->copy()->addDays(rand(1, $startDate->diffInDays($endDate)));
    }

    /**
     * Get random renewal date.
     */
    protected function getRandomRenewalDate(?Carbon $completionDate): ?Carbon
    {
        if (!$completionDate) return null;
        return $completionDate->copy()->addMonths(rand(6, 12));
    }

    /**
     * Get status based on completion and dates.
     */
    protected function getStatus(bool $isCompleted, Carbon $startDate, Carbon $endDate): string
    {
        if ($isCompleted) {
            return TrainingStatus::COMPLETED->value;
        }

        $now = now();

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
    protected function getGradeFromScore(int $score): string
    {
        if ($score >= 90) return 'A';
        if ($score >= 80) return 'B';
        if ($score >= 70) return 'C';
        if ($score >= 60) return 'D';
        return 'F';
    }

    /**
     * Get random instructor.
     */
    protected function getRandomInstructor(): string
    {
        $instructors = [
            'Dr. Sarah Johnson',
            'Prof. Michael Chen',
            'Lisa Rodriguez',
            'David Thompson',
            'Dr. Emily Davis',
            'James Wilson',
            'Maria Garcia',
            'Robert Brown',
            'Dr. Jennifer Lee',
            'Thomas Anderson',
        ];

        return $instructors[array_rand($instructors)];
    }

    /**
     * Get random location based on method.
     */
    protected function getRandomLocation(TrainingMethod $method): string
    {
        if ($method === TrainingMethod::ONLINE) {
            return 'Online Platform';
        }

        $locations = [
            'Main Office Training Room',
            'Downtown Conference Center',
            'Tech Hub Campus',
            'Professional Development Center',
            'Corporate Training Facility',
            'Innovation Lab',
            'Learning Center',
            'Executive Conference Room',
        ];

        return $locations[array_rand($locations)];
    }

    /**
     * Get random materials.
     */
    protected function getRandomMaterials(): array
    {
        $materials = [
            'handbooks' => rand(0, 1),
            'videos' => rand(0, 1),
            'presentations' => rand(0, 1),
            'exercises' => rand(0, 1),
            'assessments' => rand(0, 1),
            'certificates' => rand(0, 1),
        ];

        return array_filter($materials);
    }

    /**
     * Get random notes.
     */
    protected function getRandomNotes(): string
    {
        $notes = [
            'Employee showed excellent engagement during the training.',
            'Additional practice materials recommended for better understanding.',
            'Training completed ahead of schedule with outstanding performance.',
            'Employee requested follow-up session for advanced topics.',
            'Training adapted well to different learning styles.',
            'Excellent participation in group activities and discussions.',
            'Employee demonstrated strong practical application skills.',
            'Training materials were well-received and effective.',
        ];

        return $notes[array_rand($notes)];
    }

    /**
     * Get random attachments.
     */
    protected function getRandomAttachments(): array
    {
        $attachments = [];
        $count = rand(0, 3);

        for ($i = 0; $i < $count; $i++) {
            $attachments[] = [
                'name' => 'Training_Material_' . ($i + 1) . '.pdf',
                'url' => 'https://training.example.com/materials/' . rand(1000, 9999) . '.pdf',
                'size' => rand(100, 5000) . 'KB',
                'type' => 'application/pdf'
            ];
        }

        return $attachments;
    }
}
