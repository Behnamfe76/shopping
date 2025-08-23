<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\EmployeeSalaryHistory;
use App\Models\Employee;
use App\Models\User;
use App\Enums\SalaryChangeType;
use Carbon\Carbon;

class EmployeeSalaryHistorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Seeding Employee Salary History data...');

        // Get existing employees
        $employees = Employee::all();

        if ($employees->isEmpty()) {
            $this->command->warn('No employees found. Please run EmployeeSeeder first.');
            return;
        }

        // Get existing users for approvers
        $users = User::all();

        if ($users->isEmpty()) {
            $this->command->warn('No users found. Please run UserSeeder first.');
            return;
        }

        $this->seedSalaryHistory($employees, $users);

        $this->command->info('Employee Salary History seeding completed!');
    }

    /**
     * Seed salary history data
     */
    private function seedSalaryHistory($employees, $users): void
    {
        $totalRecords = 0;
        $changeTypes = SalaryChangeType::cases();

        foreach ($employees as $employee) {
            // Generate 2-6 salary history records per employee
            $recordCount = rand(2, 6);

            for ($i = 0; $i < $recordCount; $i++) {
                $this->createSalaryHistoryRecord($employee, $users, $changeTypes, $i);
                $totalRecords++;
            }
        }

        $this->command->info("Created {$totalRecords} salary history records.");
    }

    /**
     * Create a single salary history record
     */
    private function createSalaryHistoryRecord($employee, $users, $changeTypes, $index): void
    {
        // Start with a base salary and build history
        $baseSalary = $this->getBaseSalary($employee, $index);
        $changeType = $this->selectChangeType($changeTypes, $index);
        $changePercentage = $this->calculateChangePercentage($changeType, $index);
        $changeAmount = ($baseSalary * $changePercentage) / 100;
        $newSalary = $baseSalary + $changeAmount;

        // Calculate effective date (spread over the last 2 years)
        $effectiveDate = $this->calculateEffectiveDate($index);

        // Determine status based on date and type
        $status = $this->determineStatus($effectiveDate, $changeType);

        // Set approval/rejection data
        $approvalData = $this->getApprovalData($status, $users, $effectiveDate);

        // Set retroactive data if applicable
        $retroactiveData = $this->getRetroactiveData($changeType, $effectiveDate, $status === 'approved');

        // Create the record
        EmployeeSalaryHistory::create([
            'employee_id' => $employee->id,
            'old_salary' => $baseSalary,
            'new_salary' => $newSalary,
            'change_amount' => $changeAmount,
            'change_percentage' => $changePercentage,
            'change_type' => $changeType,
            'effective_date' => $effectiveDate,
            'reason' => $this->generateReason($changeType, $employee),
            'approved_by' => $approvalData['approved_by'],
            'approved_at' => $approvalData['approved_at'],
            'rejected_by' => $approvalData['rejected_by'],
            'rejected_at' => $approvalData['rejected_at'],
            'status' => $status,
            'processed_at' => $approvalData['processed_at'],
            'is_retroactive' => $retroactiveData['is_retroactive'],
            'retroactive_start_date' => $retroactiveData['start_date'],
            'retroactive_end_date' => $retroactiveData['end_date'],
            'notes' => $this->generateNotes($changeType, $changePercentage),
            'attachments' => $this->generateAttachments($changeType),
            'metadata' => $this->generateMetadata($changeType, $employee),
            'rejection_reason' => $status === 'rejected' ? $this->generateRejectionReason() : null,
        ]);
    }

    /**
     * Get base salary for the employee at this point in history
     */
    private function getBaseSalary($employee, $index): float
    {
        // Start with a reasonable base salary and increase over time
        $baseSalary = 45000; // Starting salary

        // Add some variation based on employee
        $baseSalary += ($employee->id % 10) * 2000;

        // Increase over time (index represents chronological order)
        $baseSalary += $index * 3000;

        return round($baseSalary, 2);
    }

    /**
     * Select appropriate change type based on index
     */
    private function selectChangeType($changeTypes, $index): SalaryChangeType
    {
        // First record is usually a hiring bonus or initial salary
        if ($index === 0) {
            return $this->faker->randomElement([
                SalaryChangeType::HIRING_BONUS,
                SalaryChangeType::MARKET_ADJUSTMENT,
            ]);
        }

        // Later records are more varied
        $commonTypes = [
            SalaryChangeType::MERIT,
            SalaryChangeType::COST_OF_LIVING,
            SalaryChangeType::MARKET_ADJUSTMENT,
            SalaryChangeType::PERFORMANCE_BONUS,
        ];

        $promotionTypes = [
            SalaryChangeType::PROMOTION,
            SalaryChangeType::SKILL_ADJUSTMENT,
            SalaryChangeType::EXPERIENCE_ADJUSTMENT,
        ];

        // 70% chance of common types, 30% chance of promotion types
        if ($this->faker->boolean(70)) {
            return $this->faker->randomElement($commonTypes);
        } else {
            return $this->faker->randomElement($promotionTypes);
        }
    }

    /**
     * Calculate change percentage based on type and index
     */
    private function calculateChangePercentage($changeType, $index): float
    {
        return match($changeType) {
            SalaryChangeType::PROMOTION => $this->faker->randomFloat(2, 8, 20),
            SalaryChangeType::MERIT => $this->faker->randomFloat(2, 3, 8),
            SalaryChangeType::COST_OF_LIVING => $this->faker->randomFloat(2, 2, 5),
            SalaryChangeType::MARKET_ADJUSTMENT => $this->faker->randomFloat(2, 5, 15),
            SalaryChangeType::PERFORMANCE_BONUS => $this->faker->randomFloat(2, 2, 8),
            SalaryChangeType::HIRING_BONUS => $this->faker->randomFloat(2, 5, 15),
            SalaryChangeType::SKILL_ADJUSTMENT => $this->faker->randomFloat(2, 3, 10),
            SalaryChangeType::EXPERIENCE_ADJUSTMENT => $this->faker->randomFloat(2, 2, 8),
            default => $this->faker->randomFloat(2, 2, 10),
        };
    }

    /**
     * Calculate effective date for the record
     */
    private function calculateEffectiveDate($index): Carbon
    {
        // Spread records over the last 2 years
        $baseDate = Carbon::now()->subYears(2);
        $interval = 730 / 6; // 2 years / 6 records max

        return $baseDate->copy()->addDays($index * $interval);
    }

    /**
     * Determine status based on date and type
     */
    private function determineStatus($effectiveDate, $changeType): string
    {
        $now = Carbon::now();

        // Future dates are pending
        if ($effectiveDate->isFuture()) {
            return 'pending';
        }

        // Past dates are usually approved/processed
        if ($effectiveDate->isPast()) {
            // 80% chance of approved, 20% chance of processed
            if ($this->faker->boolean(80)) {
                return 'approved';
            } else {
                return 'processed';
            }
        }

        return 'pending';
    }

    /**
     * Get approval data based on status
     */
    private function getApprovalData($status, $users, $effectiveDate): array
    {
        $data = [
            'approved_by' => null,
            'approved_at' => null,
            'rejected_by' => null,
            'rejected_at' => null,
            'processed_at' => null,
        ];

        if ($status === 'approved' || $status === 'processed') {
            $data['approved_by'] = $users->random()->id;
            $data['approved_at'] = $effectiveDate->copy()->addDays(rand(1, 30));

            if ($status === 'processed') {
                $data['processed_at'] = $data['approved_at']->copy()->addDays(rand(30, 90));
            }
        }

        return $data;
    }

    /**
     * Get retroactive data if applicable
     */
    private function getRetroactiveData($changeType, $effectiveDate, $isApproved): array
    {
        $data = [
            'is_retroactive' => false,
            'start_date' => null,
            'end_date' => null,
        ];

        // Only approved records can be retroactive
        if (!$isApproved) {
            return $data;
        }

        // 15% chance of being retroactive for eligible types
        if ($changeType->isRetroactiveEligible() && $this->faker->boolean(15)) {
            $data['is_retroactive'] = true;
            $data['start_date'] = $effectiveDate->copy()->subMonths(rand(1, 6));
            $data['end_date'] = $effectiveDate->copy()->subDays(rand(1, 30));
        }

        return $data;
    }

    /**
     * Generate reason for the change
     */
    private function generateReason($changeType, $employee): string
    {
        return match($changeType) {
            SalaryChangeType::PROMOTION => "Promotion to {$this->getNextPosition($employee)}",
            SalaryChangeType::MERIT => $this->faker->randomElement([
                'Annual merit increase',
                'Performance-based raise',
                'Exceeds expectations',
                'Outstanding performance review',
            ]),
            SalaryChangeType::COST_OF_LIVING => $this->faker->randomElement([
                'Annual cost of living adjustment',
                'Inflation adjustment',
                'CPI-based increase',
            ]),
            SalaryChangeType::MARKET_ADJUSTMENT => $this->faker->randomElement([
                'Market rate adjustment',
                'Competitive salary increase',
                'Industry standard adjustment',
            ]),
            SalaryChangeType::PERFORMANCE_BONUS => $this->faker->randomElement([
                'Q4 performance bonus',
                'Annual performance bonus',
                'Project completion bonus',
                'Exceeds expectations bonus',
            ]),
            SalaryChangeType::HIRING_BONUS => 'Hiring bonus for new position',
            SalaryChangeType::SKILL_ADJUSTMENT => $this->faker->randomElement([
                'New certification bonus',
                'Skill development recognition',
                'Technical expertise bonus',
            ]),
            SalaryChangeType::EXPERIENCE_ADJUSTMENT => $this->faker->randomElement([
                'Experience milestone recognition',
                'Tenure-based adjustment',
                'Career progression bonus',
            ]),
            default => 'Salary adjustment',
        };
    }

    /**
     * Get next position for promotion
     */
    private function getNextPosition($employee): string
    {
        $positions = [
            'Junior Developer' => 'Developer',
            'Developer' => 'Senior Developer',
            'Senior Developer' => 'Team Lead',
            'Team Lead' => 'Manager',
            'Manager' => 'Director',
            'Director' => 'VP',
        ];

        $currentPosition = $employee->position?->title ?? 'Developer';
        return $positions[$currentPosition] ?? 'Senior Developer';
    }

    /**
     * Generate notes for the change
     */
    private function generateNotes($changeType, $changePercentage): ?string
    {
        if ($this->faker->boolean(60)) {
            return match($changeType) {
                SalaryChangeType::PROMOTION => "Promotion reflects increased responsibilities and leadership capabilities.",
                SalaryChangeType::MERIT => "Merit increase based on consistent high performance and contributions.",
                SalaryChangeType::MARKET_ADJUSTMENT => "Adjustment brings salary in line with market rates for similar positions.",
                SalaryChangeType::PERFORMANCE_BONUS => "Bonus recognizes exceptional performance and project delivery.",
                default => "Salary adjustment based on company policy and performance review.",
            };
        }

        return null;
    }

    /**
     * Generate attachments data
     */
    private function generateAttachments($changeType): array
    {
        if ($this->faker->boolean(40)) {
            $attachments = [
                'documents' => [],
                'images' => [],
                'other' => [],
            ];

            // Add relevant documents based on change type
            if ($changeType === SalaryChangeType::PROMOTION) {
                $attachments['documents'][] = 'promotion_letter.pdf';
            } elseif ($changeType === SalaryChangeType::PERFORMANCE_BONUS) {
                $attachments['documents'][] = 'performance_review.pdf';
            } elseif ($changeType === SalaryChangeType::MARKET_ADJUSTMENT) {
                $attachments['documents'][] = 'market_analysis.pdf';
            }

            return $attachments;
        }

        return [];
    }

    /**
     * Generate metadata for the change
     */
    private function generateMetadata($changeType, $employee): array
    {
        $metadata = [
            'change_category' => $changeType->getCategory(),
            'requires_approval' => $changeType->requiresApproval(),
            'retroactive_eligible' => $changeType->isRetroactiveEligible(),
            'impact_level' => $this->faker->randomElement(['low', 'medium', 'high']),
            'review_cycle' => $this->faker->randomElement(['annual', 'biannual', 'quarterly', 'as_needed']),
            'employee_department' => $employee->department?->name ?? 'Unknown',
            'employee_position' => $employee->position?->title ?? 'Unknown',
        ];

        // Add type-specific metadata
        if ($changeType === SalaryChangeType::PROMOTION) {
            $metadata['promotion_level'] = $this->faker->randomElement(['Junior to Mid', 'Mid to Senior', 'Senior to Lead', 'Lead to Manager']);
        }

        if ($changeType === SalaryChangeType::MARKET_ADJUSTMENT) {
            $metadata['market_data_source'] = $this->faker->randomElement(['Salary.com', 'Glassdoor', 'Payscale', 'Industry Survey']);
            $metadata['market_percentile'] = $this->faker->randomElement(['25th', '50th', '75th', '90th']);
        }

        return $metadata;
    }

    /**
     * Generate rejection reason
     */
    private function generateRejectionReason(): string
    {
        return $this->faker->randomElement([
            'Budget constraints prevent approval at this time',
            'Insufficient justification provided',
            'Timing not appropriate for this change',
            'Additional documentation required',
            'Does not meet current approval criteria',
            'Market conditions do not support this adjustment',
        ]);
    }
}
