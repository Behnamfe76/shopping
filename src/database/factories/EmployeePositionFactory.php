<?php

namespace Fereydooni\Shopping\database\factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Fereydooni\Shopping\app\Models\EmployeePosition;
use Fereydooni\Shopping\app\Models\EmployeeDepartment;
use Fereydooni\Shopping\app\Enums\PositionStatus;
use Fereydooni\Shopping\app\Enums\PositionLevel;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\Fereydooni\Shopping\app\Models\EmployeePosition>
 */
class EmployeePositionFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = EmployeePosition::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $level = $this->faker->randomElement(PositionLevel::cases());
        $status = $this->faker->randomElement(PositionStatus::cases());

        // Generate realistic salary ranges based on level
        $salaryRanges = $this->getSalaryRangeForLevel($level);
        $hourlyRates = $this->getHourlyRateRangeForLevel($level);

        // Generate realistic skills based on level
        $skills = $this->getSkillsForLevel($level);
        $requirements = $this->getRequirementsForLevel($level);
        $responsibilities = $this->getResponsibilitiesForLevel($level);
        $education = $this->getEducationForLevel($level);

        return [
            'title' => $this->faker->jobTitle(),
            'code' => $this->faker->unique()->regexify('[A-Z]{2}[0-9]{3}'),
            'description' => $this->faker->paragraph(3),
            'department_id' => EmployeeDepartment::factory(),
            'level' => $level,
            'salary_min' => $salaryRanges['min'],
            'salary_max' => $salaryRanges['max'],
            'hourly_rate_min' => $hourlyRates['min'],
            'hourly_rate_max' => $hourlyRates['max'],
            'is_active' => $status !== PositionStatus::ARCHIVED,
            'status' => $status,
            'requirements' => $requirements,
            'responsibilities' => $responsibilities,
            'skills_required' => $skills,
            'experience_required' => $this->getExperienceForLevel($level),
            'education_required' => $education,
            'is_remote' => $this->faker->boolean(30), // 30% chance of remote
            'is_travel_required' => $this->faker->boolean(20), // 20% chance of travel
            'travel_percentage' => $this->faker->optional(0.2)->randomFloat(2, 5, 50),
            'metadata' => [
                'benefits' => $this->faker->randomElements(['health_insurance', 'dental_insurance', 'vision_insurance', '401k', 'stock_options', 'bonus'], $this->faker->numberBetween(2, 4)),
                'work_schedule' => $this->faker->randomElement(['9-5', 'flexible', 'shift_work']),
                'overtime_eligible' => $this->faker->boolean(),
                'probation_period' => $this->faker->randomElement([30, 60, 90]),
            ],
        ];
    }

    /**
     * Indicate that the position is active.
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => true,
            'status' => PositionStatus::ACTIVE,
        ]);
    }

    /**
     * Indicate that the position is hiring.
     */
    public function hiring(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => true,
            'status' => PositionStatus::HIRING,
        ]);
    }

    /**
     * Indicate that the position is remote.
     */
    public function remote(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_remote' => true,
        ]);
    }

    /**
     * Indicate that the position requires travel.
     */
    public function travelRequired(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_travel_required' => true,
            'travel_percentage' => $this->faker->randomFloat(2, 10, 50),
        ]);
    }

    /**
     * Indicate that the position is entry level.
     */
    public function entryLevel(): static
    {
        return $this->state(fn (array $attributes) => [
            'level' => PositionLevel::ENTRY,
            'salary_min' => $this->faker->numberBetween(30000, 45000),
            'salary_max' => $this->faker->numberBetween(45001, 60000),
            'hourly_rate_min' => $this->faker->randomFloat(2, 15, 20),
            'hourly_rate_max' => $this->faker->randomFloat(2, 20, 25),
            'experience_required' => 0,
        ]);
    }

    /**
     * Indicate that the position is senior level.
     */
    public function seniorLevel(): static
    {
        return $this->state(fn (array $attributes) => [
            'level' => PositionLevel::SENIOR,
            'salary_min' => $this->faker->numberBetween(80000, 100000),
            'salary_max' => $this->faker->numberBetween(100001, 130000),
            'hourly_rate_min' => $this->faker->randomFloat(2, 35, 45),
            'hourly_rate_max' => $this->faker->randomFloat(2, 45, 60),
            'experience_required' => $this->faker->numberBetween(5, 8),
        ]);
    }

    /**
     * Indicate that the position is management level.
     */
    public function management(): static
    {
        return $this->state(fn (array $attributes) => [
            'level' => $this->faker->randomElement([PositionLevel::MANAGER, PositionLevel::DIRECTOR, PositionLevel::EXECUTIVE]),
            'salary_min' => $this->faker->numberBetween(120000, 150000),
            'salary_max' => $this->faker->numberBetween(150001, 250000),
            'hourly_rate_min' => $this->faker->randomFloat(2, 60, 80),
            'hourly_rate_max' => $this->faker->randomFloat(2, 80, 120),
            'experience_required' => $this->faker->numberBetween(8, 15),
        ]);
    }

    /**
     * Get salary range for a specific level.
     */
    protected function getSalaryRangeForLevel(PositionLevel $level): array
    {
        return match($level) {
            PositionLevel::ENTRY => [
                'min' => $this->faker->numberBetween(30000, 45000),
                'max' => $this->faker->numberBetween(45001, 60000)
            ],
            PositionLevel::JUNIOR => [
                'min' => $this->faker->numberBetween(45000, 60000),
                'max' => $this->faker->numberBetween(60001, 75000)
            ],
            PositionLevel::MID => [
                'min' => $this->faker->numberBetween(60000, 80000),
                'max' => $this->faker->numberBetween(80001, 100000)
            ],
            PositionLevel::SENIOR => [
                'min' => $this->faker->numberBetween(80000, 100000),
                'max' => $this->faker->numberBetween(100001, 130000)
            ],
            PositionLevel::LEAD => [
                'min' => $this->faker->numberBetween(90000, 110000),
                'max' => $this->faker->numberBetween(110001, 140000)
            ],
            PositionLevel::MANAGER => [
                'min' => $this->faker->numberBetween(120000, 150000),
                'max' => $this->faker->numberBetween(150001, 180000)
            ],
            PositionLevel::DIRECTOR => [
                'min' => $this->faker->numberBetween(150000, 200000),
                'max' => $this->faker->numberBetween(200001, 250000)
            ],
            PositionLevel::EXECUTIVE => [
                'min' => $this->faker->numberBetween(200000, 300000),
                'max' => $this->faker->numberBetween(300001, 500000)
            ],
        };
    }

    /**
     * Get hourly rate range for a specific level.
     */
    protected function getHourlyRateRangeForLevel(PositionLevel $level): array
    {
        return match($level) {
            PositionLevel::ENTRY => [
                'min' => $this->faker->randomFloat(2, 15, 20),
                'max' => $this->faker->randomFloat(2, 20, 25)
            ],
            PositionLevel::JUNIOR => [
                'min' => $this->faker->randomFloat(2, 20, 25),
                'max' => $this->faker->randomFloat(2, 25, 30)
            ],
            PositionLevel::MID => [
                'min' => $this->faker->randomFloat(2, 25, 30),
                'max' => $this->faker->randomFloat(2, 30, 40)
            ],
            PositionLevel::SENIOR => [
                'min' => $this->faker->randomFloat(2, 35, 45),
                'max' => $this->faker->randomFloat(2, 45, 60)
            ],
            PositionLevel::LEAD => [
                'min' => $this->faker->randomFloat(2, 40, 50),
                'max' => $this->faker->randomFloat(2, 50, 65)
            ],
            PositionLevel::MANAGER => [
                'min' => $this->faker->randomFloat(2, 60, 80),
                'max' => $this->faker->randomFloat(2, 80, 100)
            ],
            PositionLevel::DIRECTOR => [
                'min' => $this->faker->randomFloat(2, 80, 100),
                'max' => $this->faker->randomFloat(2, 100, 130)
            ],
            PositionLevel::EXECUTIVE => [
                'min' => $this->faker->randomFloat(2, 100, 150),
                'max' => $this->faker->randomFloat(2, 150, 250)
            ],
        };
    }

    /**
     * Get skills for a specific level.
     */
    protected function getSkillsForLevel(PositionLevel $level): array
    {
        $baseSkills = ['communication', 'teamwork', 'problem_solving'];

        $levelSkills = match($level) {
            PositionLevel::ENTRY => ['basic_technical_skills', 'willingness_to_learn', 'attention_to_detail'],
            PositionLevel::JUNIOR => ['technical_proficiency', 'project_management', 'documentation'],
            PositionLevel::MID => ['advanced_technical_skills', 'mentoring', 'project_planning'],
            PositionLevel::SENIOR => ['expert_technical_skills', 'architecture_design', 'strategic_thinking'],
            PositionLevel::LEAD => ['technical_leadership', 'team_management', 'process_improvement'],
            PositionLevel::MANAGER => ['people_management', 'budget_management', 'strategic_planning'],
            PositionLevel::DIRECTOR => ['executive_leadership', 'business_strategy', 'stakeholder_management'],
            PositionLevel::EXECUTIVE => ['board_governance', 'corporate_strategy', 'investor_relations'],
        };

        return array_merge($baseSkills, $levelSkills);
    }

    /**
     * Get requirements for a specific level.
     */
    protected function getRequirementsForLevel(PositionLevel $level): array
    {
        return match($level) {
            PositionLevel::ENTRY => [
                'Bachelor\'s degree in related field or equivalent experience',
                'Strong communication skills',
                'Ability to work in a team environment'
            ],
            PositionLevel::JUNIOR => [
                'Bachelor\'s degree in related field',
                '1-3 years of relevant experience',
                'Proven track record of project delivery'
            ],
            PositionLevel::MID => [
                'Bachelor\'s degree in related field',
                '3-5 years of relevant experience',
                'Experience with complex projects'
            ],
            PositionLevel::SENIOR => [
                'Bachelor\'s degree in related field (Master\'s preferred)',
                '5+ years of relevant experience',
                'Proven leadership experience'
            ],
            PositionLevel::LEAD => [
                'Bachelor\'s degree in related field (Master\'s preferred)',
                '7+ years of relevant experience',
                'Demonstrated team leadership'
            ],
            PositionLevel::MANAGER => [
                'Bachelor\'s degree in related field (Master\'s preferred)',
                '8+ years of relevant experience',
                'Proven management experience'
            ],
            PositionLevel::DIRECTOR => [
                'Bachelor\'s degree in related field (Master\'s preferred)',
                '10+ years of relevant experience',
                'Senior management experience'
            ],
            PositionLevel::EXECUTIVE => [
                'Bachelor\'s degree in related field (Master\'s preferred)',
                '15+ years of relevant experience',
                'Executive leadership experience'
            ],
        };
    }

    /**
     * Get responsibilities for a specific level.
     */
    protected function getResponsibilitiesForLevel(PositionLevel $level): array
    {
        return match($level) {
            PositionLevel::ENTRY => [
                'Complete assigned tasks under supervision',
                'Learn company processes and procedures',
                'Contribute to team projects'
            ],
            PositionLevel::JUNIOR => [
                'Complete assigned projects independently',
                'Collaborate with team members',
                'Contribute to process improvements'
            ],
            PositionLevel::MID => [
                'Lead small to medium projects',
                'Mentor junior team members',
                'Contribute to strategic decisions'
            ],
            PositionLevel::SENIOR => [
                'Lead complex projects',
                'Mentor team members',
                'Contribute to department strategy'
            ],
            PositionLevel::LEAD => [
                'Lead technical teams',
                'Drive technical decisions',
                'Mentor senior team members'
            ],
            PositionLevel::MANAGER => [
                'Manage team performance',
                'Set team goals and objectives',
                'Manage department budget'
            ],
            PositionLevel::DIRECTOR => [
                'Set department strategy',
                'Manage multiple teams',
                'Represent department to executives'
            ],
            PositionLevel::EXECUTIVE => [
                'Set company strategy',
                'Manage company operations',
                'Report to board of directors'
            ],
        };
    }

    /**
     * Get education requirements for a specific level.
     */
    protected function getEducationForLevel(PositionLevel $level): array
    {
        return match($level) {
            PositionLevel::ENTRY => ['Bachelor\'s degree or equivalent experience'],
            PositionLevel::JUNIOR => ['Bachelor\'s degree'],
            PositionLevel::MID => ['Bachelor\'s degree (Master\'s preferred)'],
            PositionLevel::SENIOR => ['Bachelor\'s degree (Master\'s preferred)'],
            PositionLevel::LEAD => ['Bachelor\'s degree (Master\'s preferred)'],
            PositionLevel::MANAGER => ['Bachelor\'s degree (Master\'s preferred)'],
            PositionLevel::DIRECTOR => ['Bachelor\'s degree (Master\'s preferred)'],
            PositionLevel::EXECUTIVE => ['Bachelor\'s degree (Master\'s preferred)'],
        };
    }

    /**
     * Get experience requirements for a specific level.
     */
    protected function getExperienceForLevel(PositionLevel $level): int
    {
        return match($level) {
            PositionLevel::ENTRY => 0,
            PositionLevel::JUNIOR => $this->faker->numberBetween(1, 3),
            PositionLevel::MID => $this->faker->numberBetween(3, 5),
            PositionLevel::SENIOR => $this->faker->numberBetween(5, 8),
            PositionLevel::LEAD => $this->faker->numberBetween(7, 10),
            PositionLevel::MANAGER => $this->faker->numberBetween(8, 12),
            PositionLevel::DIRECTOR => $this->faker->numberBetween(10, 15),
            PositionLevel::EXECUTIVE => $this->faker->numberBetween(15, 20),
        };
    }
};
