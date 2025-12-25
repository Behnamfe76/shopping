<?php

namespace Database\Factories;

use App\Models\Employee;
use App\Models\EmployeeDepartment;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\EmployeeDepartment>
 */
class EmployeeDepartmentFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = EmployeeDepartment::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $departmentTypes = [
            'Engineering', 'Marketing', 'Sales', 'Finance', 'HR', 'Operations',
            'Product', 'Design', 'Support', 'Legal', 'IT', 'Research',
        ];

        $locations = [
            'New York', 'San Francisco', 'London', 'Berlin', 'Tokyo', 'Sydney',
            'Toronto', 'Paris', 'Singapore', 'Dubai', 'Mumbai', 'São Paulo',
        ];

        return [
            'name' => $this->faker->randomElement($departmentTypes).' '.$this->faker->randomElement(['Team', 'Department', 'Division', 'Group']),
            'code' => strtoupper($this->faker->unique()->lexify('???###')),
            'description' => $this->faker->paragraph(),
            'parent_id' => null, // Will be set in specific states
            'manager_id' => null, // Will be set in specific states
            'location' => $this->faker->randomElement($locations),
            'budget' => $this->faker->randomFloat(2, 10000, 1000000),
            'headcount_limit' => $this->faker->numberBetween(5, 100),
            'is_active' => $this->faker->boolean(80), // 80% chance of being active
            'status' => $this->faker->randomElement(['active', 'inactive', 'archived']),
            'metadata' => [
                'founded_date' => $this->faker->date(),
                'industry_focus' => $this->faker->word(),
                'specialization' => $this->faker->sentence(),
                'contact_email' => $this->faker->email(),
                'office_number' => $this->faker->buildingNumber(),
                'timezone' => $this->faker->timezone(),
                'working_hours' => '9:00 AM - 5:00 PM',
                'holiday_schedule' => 'Standard company holidays',
                'remote_work_policy' => $this->faker->randomElement(['Full remote', 'Hybrid', 'Office only']),
                'budget_cycle' => $this->faker->randomElement(['Monthly', 'Quarterly', 'Annually']),
                'approval_workflow' => $this->faker->randomElement(['Manager only', 'Manager + HR', 'Manager + Finance + HR']),
                'reporting_frequency' => $this->faker->randomElement(['Weekly', 'Bi-weekly', 'Monthly']),
                'key_metrics' => [
                    'employee_satisfaction',
                    'project_completion_rate',
                    'budget_utilization',
                    'turnover_rate',
                ],
            ],
        ];
    }

    /**
     * Indicate that the department is active.
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => true,
            'status' => 'active',
        ]);
    }

    /**
     * Indicate that the department is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
            'status' => 'inactive',
        ]);
    }

    /**
     * Indicate that the department is archived.
     */
    public function archived(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
            'status' => 'archived',
        ]);
    }

    /**
     * Indicate that the department is a root department (no parent).
     */
    public function root(): static
    {
        return $this->state(fn (array $attributes) => [
            'parent_id' => null,
        ]);
    }

    /**
     * Indicate that the department has a parent.
     */
    public function withParent(?EmployeeDepartment $parent = null): static
    {
        return $this->state(fn (array $attributes) => [
            'parent_id' => $parent ? $parent->id : EmployeeDepartment::factory()->create()->id,
        ]);
    }

    /**
     * Indicate that the department has a manager.
     */
    public function withManager(?Employee $manager = null): static
    {
        return $this->state(fn (array $attributes) => [
            'manager_id' => $manager ? $manager->id : Employee::factory()->create()->id,
        ]);
    }

    /**
     * Indicate that the department has a specific budget range.
     */
    public function withBudget(float $min, float $max): static
    {
        return $this->state(fn (array $attributes) => [
            'budget' => $this->faker->randomFloat(2, $min, $max),
        ]);
    }

    /**
     * Indicate that the department has a specific headcount limit.
     */
    public function withHeadcountLimit(int $min, int $max): static
    {
        return $this->state(fn (array $attributes) => [
            'headcount_limit' => $this->faker->numberBetween($min, $max),
        ]);
    }

    /**
     * Indicate that the department is in a specific location.
     */
    public function inLocation(string $location): static
    {
        return $this->state(fn (array $attributes) => [
            'location' => $location,
        ]);
    }

    /**
     * Indicate that the department is a specific type.
     */
    public function ofType(string $type): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => $type.' '.$this->faker->randomElement(['Team', 'Department', 'Division', 'Group']),
        ]);
    }

    /**
     * Create a department with realistic engineering team data.
     */
    public function engineering(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Engineering '.$this->faker->randomElement(['Team', 'Department', 'Division']),
            'description' => 'Responsible for software development, system architecture, and technical implementation.',
            'budget' => $this->faker->randomFloat(2, 200000, 2000000),
            'headcount_limit' => $this->faker->numberBetween(20, 150),
            'location' => $this->faker->randomElement(['San Francisco', 'New York', 'London', 'Berlin']),
            'metadata' => [
                'tech_stack' => $this->faker->randomElements(['PHP', 'Laravel', 'React', 'Vue.js', 'Node.js', 'Python', 'Java'], 3),
                'development_methodology' => $this->faker->randomElement(['Agile', 'Scrum', 'Kanban', 'Waterfall']),
                'code_review_policy' => 'Mandatory for all changes',
                'testing_requirements' => 'Unit tests + Integration tests',
                'deployment_frequency' => $this->faker->randomElement(['Daily', 'Weekly', 'On-demand']),
                'on_call_schedule' => '24/7 rotation',
                'documentation_standards' => 'Comprehensive API and code documentation',
            ],
        ]);
    }

    /**
     * Create a department with realistic sales team data.
     */
    public function sales(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Sales '.$this->faker->randomElement(['Team', 'Department', 'Division']),
            'description' => 'Responsible for customer acquisition, revenue generation, and market expansion.',
            'budget' => $this->faker->randomFloat(2, 150000, 1500000),
            'headcount_limit' => $this->faker->numberBetween(15, 100),
            'location' => $this->faker->randomElement(['New York', 'London', 'Dubai', 'Singapore']),
            'metadata' => [
                'sales_methodology' => $this->faker->randomElement(['BANT', 'SPIN', 'Solution Selling', 'Challenger']),
                'target_markets' => $this->faker->randomElements(['Enterprise', 'SMB', 'Startups', 'Government'], 2),
                'quota_period' => $this->faker->randomElement(['Monthly', 'Quarterly', 'Annually']),
                'commission_structure' => 'Base + Commission + Bonuses',
                'sales_tools' => ['CRM', 'Salesforce', 'HubSpot', 'LinkedIn Sales Navigator'],
                'territory_assignment' => 'Geographic + Industry vertical',
                'performance_metrics' => ['Revenue', 'Pipeline', 'Conversion Rate', 'Customer Acquisition Cost'],
            ],
        ]);
    }

    /**
     * Create a department with realistic marketing team data.
     */
    public function marketing(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Marketing '.$this->faker->randomElement(['Team', 'Department', 'Division']),
            'description' => 'Responsible for brand awareness, lead generation, and market positioning.',
            'budget' => $this->faker->randomFloat(2, 100000, 1000000),
            'headcount_limit' => $this->faker->numberBetween(10, 80),
            'location' => $this->faker->randomElement(['San Francisco', 'New York', 'London', 'Paris']),
            'metadata' => [
                'marketing_channels' => $this->faker->randomElements(['Digital', 'Social Media', 'Content', 'Email', 'Events', 'PR'], 4),
                'target_audience' => $this->faker->randomElements(['B2B', 'B2C', 'Enterprise', 'Developers', 'Decision Makers'], 2),
                'campaign_frequency' => $this->faker->randomElement(['Weekly', 'Bi-weekly', 'Monthly', 'Quarterly']),
                'kpis' => ['Lead Generation', 'Brand Awareness', 'Conversion Rate', 'ROI', 'Customer Lifetime Value'],
                'tools_platforms' => ['Google Analytics', 'HubSpot', 'Mailchimp', 'Hootsuite', 'Canva'],
                'content_strategy' => 'Educational + Thought Leadership + Product-focused',
                'budget_allocation' => [
                    'digital_ads' => '40%',
                    'content_creation' => '25%',
                    'events' => '20%',
                    'tools_platforms' => '15%',
                ],
            ],
        ]);
    }
}
