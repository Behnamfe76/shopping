<?php

namespace Database\Factories;

use App\Enums\SalaryChangeType;
use App\Models\Employee;
use App\Models\EmployeeSalaryHistory;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class EmployeeSalaryHistoryFactory extends Factory
{
    protected $model = EmployeeSalaryHistory::class;

    public function definition(): array
    {
        $oldSalary = $this->faker->randomFloat(2, 30000, 150000);
        $changePercentage = $this->faker->randomFloat(2, -20, 25); // -20% to +25%
        $changeAmount = ($oldSalary * $changePercentage) / 100;
        $newSalary = $oldSalary + $changeAmount;

        $changeType = $this->faker->randomElement(SalaryChangeType::cases());
        $effectiveDate = $this->faker->dateTimeBetween('-2 years', '+6 months');
        $isRetroactive = $this->faker->boolean(20); // 20% chance of being retroactive

        $retroactiveStartDate = null;
        $retroactiveEndDate = null;

        if ($isRetroactive) {
            $retroactiveStartDate = $this->faker->dateTimeBetween('-6 months', $effectiveDate);
            $retroactiveEndDate = $this->faker->dateTimeBetween($retroactiveStartDate, $effectiveDate);
        }

        $status = $this->faker->randomElement(['pending', 'approved', 'rejected', 'processed']);
        $approvedBy = null;
        $approvedAt = null;
        $rejectedBy = null;
        $rejectedAt = null;
        $processedAt = null;

        if ($status === 'approved') {
            $approvedBy = User::factory()->create()->id;
            $approvedAt = $this->faker->dateTimeBetween($effectiveDate, '+1 month');
        } elseif ($status === 'rejected') {
            $rejectedBy = User::factory()->create()->id;
            $rejectedAt = $this->faker->dateTimeBetween($effectiveDate, '+1 month');
        } elseif ($status === 'processed') {
            $approvedBy = User::factory()->create()->id;
            $approvedAt = $this->faker->dateTimeBetween($effectiveDate, '+1 month');
            $processedAt = $this->faker->dateTimeBetween($approvedAt, '+2 months');
        }

        return [
            'employee_id' => Employee::factory(),
            'old_salary' => $oldSalary,
            'new_salary' => $newSalary,
            'change_amount' => $changeAmount,
            'change_percentage' => $changePercentage,
            'change_type' => $changeType,
            'effective_date' => $effectiveDate,
            'reason' => $this->generateReason($changeType),
            'approved_by' => $approvedBy,
            'approved_at' => $approvedAt,
            'rejected_by' => $rejectedBy,
            'rejected_at' => $rejectedAt,
            'status' => $status,
            'processed_at' => $processedAt,
            'is_retroactive' => $isRetroactive,
            'retroactive_start_date' => $retroactiveStartDate,
            'retroactive_end_date' => $retroactiveEndDate,
            'notes' => $this->faker->optional(0.7)->paragraph(),
            'attachments' => $this->generateAttachments(),
            'metadata' => $this->generateMetadata($changeType),
            'rejection_reason' => $status === 'rejected' ? $this->faker->sentence() : null,
        ];
    }

    /**
     * Generate a promotion salary change
     */
    public function promotion(): static
    {
        return $this->state(function (array $attributes) {
            $oldSalary = $this->faker->randomFloat(2, 40000, 120000);
            $changePercentage = $this->faker->randomFloat(2, 8, 20); // 8% to 20% increase
            $changeAmount = ($oldSalary * $changePercentage) / 100;

            return [
                'change_type' => SalaryChangeType::PROMOTION,
                'old_salary' => $oldSalary,
                'new_salary' => $oldSalary + $changeAmount,
                'change_amount' => $changeAmount,
                'change_percentage' => $changePercentage,
                'reason' => $this->faker->randomElement([
                    'Promotion to Senior Developer',
                    'Promotion to Team Lead',
                    'Promotion to Manager',
                    'Promotion to Director',
                    'Promotion to VP',
                ]),
                'status' => 'approved',
            ];
        });
    }

    /**
     * Generate a merit increase
     */
    public function merit(): static
    {
        return $this->state(function (array $attributes) {
            $oldSalary = $this->faker->randomFloat(2, 35000, 100000);
            $changePercentage = $this->faker->randomFloat(2, 3, 8); // 3% to 8% increase
            $changeAmount = ($oldSalary * $changePercentage) / 100;

            return [
                'change_type' => SalaryChangeType::MERIT,
                'old_salary' => $oldSalary,
                'new_salary' => $oldSalary + $changeAmount,
                'change_amount' => $changeAmount,
                'change_percentage' => $changePercentage,
                'reason' => $this->faker->randomElement([
                    'Annual merit increase',
                    'Performance-based raise',
                    'Exceeds expectations',
                    'Outstanding performance',
                    'Consistent high performance',
                ]),
                'status' => 'approved',
            ];
        });
    }

    /**
     * Generate a cost of living adjustment
     */
    public function costOfLiving(): static
    {
        return $this->state(function (array $attributes) {
            $oldSalary = $this->faker->randomFloat(2, 30000, 150000);
            $changePercentage = $this->faker->randomFloat(2, 2, 5); // 2% to 5% increase
            $changeAmount = ($oldSalary * $changePercentage) / 100;

            return [
                'change_type' => SalaryChangeType::COST_OF_LIVING,
                'old_salary' => $oldSalary,
                'new_salary' => $oldSalary + $changeAmount,
                'change_amount' => $changeAmount,
                'change_percentage' => $changePercentage,
                'reason' => $this->faker->randomElement([
                    'Annual cost of living adjustment',
                    'Inflation adjustment',
                    'CPI-based increase',
                    'Standard COL adjustment',
                ]),
                'status' => 'approved',
            ];
        });
    }

    /**
     * Generate a market adjustment
     */
    public function marketAdjustment(): static
    {
        return $this->state(function (array $attributes) {
            $oldSalary = $this->faker->randomFloat(2, 40000, 120000);
            $changePercentage = $this->faker->randomFloat(2, 5, 15); // 5% to 15% increase
            $changeAmount = ($oldSalary * $changePercentage) / 100;

            return [
                'change_type' => SalaryChangeType::MARKET_ADJUSTMENT,
                'old_salary' => $oldSalary,
                'new_salary' => $oldSalary + $changeAmount,
                'change_amount' => $changeAmount,
                'change_percentage' => $changePercentage,
                'reason' => $this->faker->randomElement([
                    'Market rate adjustment',
                    'Competitive salary increase',
                    'Industry standard adjustment',
                    'Market competitiveness review',
                ]),
                'status' => 'approved',
            ];
        });
    }

    /**
     * Generate a performance bonus
     */
    public function performanceBonus(): static
    {
        return $this->state(function (array $attributes) {
            $oldSalary = $this->faker->randomFloat(2, 40000, 100000);
            $changeAmount = $this->faker->randomFloat(2, 1000, 10000); // $1k to $10k bonus
            $changePercentage = ($changeAmount / $oldSalary) * 100;

            return [
                'change_type' => SalaryChangeType::PERFORMANCE_BONUS,
                'old_salary' => $oldSalary,
                'new_salary' => $oldSalary + $changeAmount,
                'change_amount' => $changeAmount,
                'change_percentage' => $changePercentage,
                'reason' => $this->faker->randomElement([
                    'Q4 performance bonus',
                    'Annual performance bonus',
                    'Project completion bonus',
                    'Exceeds expectations bonus',
                    'Special recognition bonus',
                ]),
                'status' => 'approved',
            ];
        });
    }

    /**
     * Generate a retroactive adjustment
     */
    public function retroactive(): static
    {
        return $this->state(function (array $attributes) {
            $oldSalary = $this->faker->randomFloat(2, 35000, 100000);
            $changePercentage = $this->faker->randomFloat(2, 3, 10); // 3% to 10% increase
            $changeAmount = ($oldSalary * $changePercentage) / 100;
            $effectiveDate = $this->faker->dateTimeBetween('-6 months', 'now');
            $retroactiveStartDate = $this->faker->dateTimeBetween('-1 year', $effectiveDate);
            $retroactiveEndDate = $this->faker->dateTimeBetween($retroactiveStartDate, $effectiveDate);

            return [
                'old_salary' => $oldSalary,
                'new_salary' => $oldSalary + $changeAmount,
                'change_amount' => $changeAmount,
                'change_percentage' => $changePercentage,
                'effective_date' => $effectiveDate,
                'is_retroactive' => true,
                'retroactive_start_date' => $retroactiveStartDate,
                'retroactive_end_date' => $retroactiveEndDate,
                'reason' => $this->faker->randomElement([
                    'Retroactive merit increase',
                    'Retroactive promotion',
                    'Retroactive market adjustment',
                    'Retroactive equity adjustment',
                ]),
                'status' => 'approved',
            ];
        });
    }

    /**
     * Generate a pending approval record
     */
    public function pending(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'pending',
                'approved_by' => null,
                'approved_at' => null,
                'rejected_by' => null,
                'rejected_at' => null,
                'processed_at' => null,
            ];
        });
    }

    /**
     * Generate a rejected record
     */
    public function rejected(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'rejected',
                'approved_by' => null,
                'approved_at' => null,
                'rejected_by' => User::factory()->create()->id,
                'rejected_at' => $this->faker->dateTimeBetween('now', '+1 month'),
                'processed_at' => null,
                'rejection_reason' => $this->faker->randomElement([
                    'Budget constraints',
                    'Insufficient justification',
                    'Timing not appropriate',
                    'Need additional documentation',
                    'Does not meet criteria',
                ]),
            ];
        });
    }

    /**
     * Generate a processed record
     */
    public function processed(): static
    {
        return $this->state(function (array $attributes) {
            $approvedAt = $this->faker->dateTimeBetween('-2 months', '-1 month');
            $processedAt = $this->faker->dateTimeBetween($approvedAt, 'now');

            return [
                'status' => 'processed',
                'approved_by' => User::factory()->create()->id,
                'approved_at' => $approvedAt,
                'rejected_by' => null,
                'rejected_at' => null,
                'processed_at' => $processedAt,
            ];
        });
    }

    /**
     * Generate a reason based on change type
     */
    private function generateReason(SalaryChangeType $changeType): string
    {
        return match ($changeType) {
            SalaryChangeType::PROMOTION => $this->faker->randomElement([
                'Promotion to Senior Developer',
                'Promotion to Team Lead',
                'Promotion to Manager',
                'Promotion to Director',
                'Promotion to VP',
            ]),
            SalaryChangeType::MERIT => $this->faker->randomElement([
                'Annual merit increase',
                'Performance-based raise',
                'Exceeds expectations',
                'Outstanding performance',
                'Consistent high performance',
            ]),
            SalaryChangeType::COST_OF_LIVING => $this->faker->randomElement([
                'Annual cost of living adjustment',
                'Inflation adjustment',
                'CPI-based increase',
                'Standard COL adjustment',
            ]),
            SalaryChangeType::MARKET_ADJUSTMENT => $this->faker->randomElement([
                'Market rate adjustment',
                'Competitive salary increase',
                'Industry standard adjustment',
                'Market competitiveness review',
            ]),
            SalaryChangeType::PERFORMANCE_BONUS => $this->faker->randomElement([
                'Q4 performance bonus',
                'Annual performance bonus',
                'Project completion bonus',
                'Exceeds expectations bonus',
                'Special recognition bonus',
            ]),
            SalaryChangeType::EQUITY_ADJUSTMENT => $this->faker->randomElement([
                'Pay equity adjustment',
                'Gender pay gap correction',
                'Equal pay for equal work',
                'Equity review adjustment',
            ]),
            SalaryChangeType::COMPRESSION_ADJUSTMENT => $this->faker->randomElement([
                'Pay compression correction',
                'Experience-based adjustment',
                'Tenure recognition',
                'Compression review adjustment',
            ]),
            default => $this->faker->sentence(),
        };
    }

    /**
     * Generate attachments data
     */
    private function generateAttachments(): array
    {
        if ($this->faker->boolean(30)) { // 30% chance of having attachments
            return [
                'documents' => [
                    $this->faker->randomElement([
                        'performance_review.pdf',
                        'promotion_letter.pdf',
                        'market_analysis.pdf',
                        'equity_review.pdf',
                        'bonus_justification.pdf',
                    ]),
                ],
                'images' => [],
                'other' => [],
            ];
        }

        return [];
    }

    /**
     * Generate metadata based on change type
     */
    private function generateMetadata(SalaryChangeType $changeType): array
    {
        $metadata = [
            'change_category' => $changeType->getCategory(),
            'requires_approval' => $changeType->requiresApproval(),
            'retroactive_eligible' => $changeType->isRetroactiveEligible(),
            'impact_level' => $this->faker->randomElement(['low', 'medium', 'high']),
            'review_cycle' => $this->faker->randomElement(['annual', 'biannual', 'quarterly', 'as_needed']),
        ];

        if ($changeType === SalaryChangeType::PROMOTION) {
            $metadata['previous_position'] = $this->faker->randomElement(['Developer', 'Senior Developer', 'Team Lead', 'Manager']);
            $metadata['new_position'] = $this->faker->randomElement(['Senior Developer', 'Team Lead', 'Manager', 'Director', 'VP']);
            $metadata['promotion_reason'] = $this->faker->randomElement(['Performance', 'Leadership', 'Skills', 'Experience', 'Business Need']);
        }

        if ($changeType === SalaryChangeType::MARKET_ADJUSTMENT) {
            $metadata['market_data_source'] = $this->faker->randomElement(['Salary.com', 'Glassdoor', 'Payscale', 'Industry Survey', 'Internal Analysis']);
            $metadata['market_percentile'] = $this->faker->randomElement(['25th', '50th', '75th', '90th']);
        }

        if ($changeType === SalaryChangeType::EQUITY_ADJUSTMENT) {
            $metadata['equity_factor'] = $this->faker->randomElement(['Gender', 'Race', 'Age', 'Experience', 'Performance']);
            $metadata['adjustment_basis'] = $this->faker->randomElement(['Internal Equity', 'External Benchmark', 'Performance Parity', 'Tenure Recognition']);
        }

        return $metadata;
    }
}
