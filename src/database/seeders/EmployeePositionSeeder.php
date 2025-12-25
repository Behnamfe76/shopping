<?php

namespace Fereydooni\Shopping\database\seeders;

use Fereydooni\Shopping\app\Enums\PositionLevel;
use Fereydooni\Shopping\app\Enums\PositionStatus;
use Fereydooni\Shopping\app\Models\EmployeeDepartment;
use Fereydooni\Shopping\app\Models\EmployeePosition;
use Illuminate\Database\Seeder;

class EmployeePositionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get existing departments or create default ones
        $departments = EmployeeDepartment::all();

        if ($departments->isEmpty()) {
            $departments = EmployeeDepartment::factory(5)->create();
        }

        // Create sample positions for each department
        foreach ($departments as $department) {
            $this->createPositionsForDepartment($department);
        }

        // Create some additional specialized positions
        $this->createSpecializedPositions($departments);

        $this->command->info('Employee positions seeded successfully!');
    }

    /**
     * Create positions for a specific department.
     */
    protected function createPositionsForDepartment(EmployeeDepartment $department): void
    {
        $positions = [
            // Entry Level Positions
            [
                'title' => 'Junior Developer',
                'code' => 'DEV001',
                'level' => PositionLevel::ENTRY,
                'status' => PositionStatus::HIRING,
                'salary_min' => 45000,
                'salary_max' => 60000,
                'hourly_rate_min' => 20,
                'hourly_rate_max' => 25,
                'is_remote' => true,
                'is_travel_required' => false,
                'experience_required' => 0,
                'requirements' => [
                    'Bachelor\'s degree in Computer Science or related field',
                    'Basic knowledge of programming languages',
                    'Strong problem-solving skills',
                    'Willingness to learn new technologies',
                ],
                'responsibilities' => [
                    'Write clean, maintainable code',
                    'Participate in code reviews',
                    'Learn from senior developers',
                    'Contribute to team projects',
                ],
                'skills_required' => [
                    'programming',
                    'problem_solving',
                    'teamwork',
                    'communication',
                ],
                'education_required' => [
                    'Bachelor\'s degree in Computer Science or related field',
                ],
            ],
            [
                'title' => 'Marketing Assistant',
                'code' => 'MKT001',
                'level' => PositionLevel::ENTRY,
                'status' => PositionStatus::ACTIVE,
                'salary_min' => 35000,
                'salary_max' => 45000,
                'hourly_rate_min' => 18,
                'hourly_rate_max' => 22,
                'is_remote' => false,
                'is_travel_required' => false,
                'experience_required' => 0,
                'requirements' => [
                    'Bachelor\'s degree in Marketing or related field',
                    'Strong written and verbal communication skills',
                    'Proficiency in Microsoft Office',
                    'Creative thinking abilities',
                ],
                'responsibilities' => [
                    'Assist with marketing campaigns',
                    'Create social media content',
                    'Support event planning',
                    'Conduct market research',
                ],
                'skills_required' => [
                    'marketing',
                    'communication',
                    'creativity',
                    'organization',
                ],
                'education_required' => [
                    'Bachelor\'s degree in Marketing or related field',
                ],
            ],

            // Mid Level Positions
            [
                'title' => 'Software Engineer',
                'code' => 'DEV002',
                'level' => PositionLevel::MID,
                'status' => PositionStatus::ACTIVE,
                'salary_min' => 75000,
                'salary_max' => 95000,
                'hourly_rate_min' => 35,
                'hourly_rate_max' => 45,
                'is_remote' => true,
                'is_travel_required' => false,
                'experience_required' => 3,
                'requirements' => [
                    'Bachelor\'s degree in Computer Science or related field',
                    '3+ years of software development experience',
                    'Proficiency in multiple programming languages',
                    'Experience with software development methodologies',
                ],
                'responsibilities' => [
                    'Design and implement software solutions',
                    'Collaborate with cross-functional teams',
                    'Mentor junior developers',
                    'Participate in technical architecture decisions',
                ],
                'skills_required' => [
                    'software_development',
                    'system_design',
                    'mentoring',
                    'project_management',
                ],
                'education_required' => [
                    'Bachelor\'s degree in Computer Science or related field',
                ],
            ],
            [
                'title' => 'Marketing Specialist',
                'code' => 'MKT002',
                'level' => PositionLevel::MID,
                'status' => PositionStatus::ACTIVE,
                'salary_min' => 55000,
                'salary_max' => 75000,
                'hourly_rate_min' => 25,
                'hourly_rate_max' => 35,
                'is_remote' => true,
                'is_travel_required' => false,
                'experience_required' => 3,
                'requirements' => [
                    'Bachelor\'s degree in Marketing or related field',
                    '3+ years of marketing experience',
                    'Experience with digital marketing tools',
                    'Analytical and creative thinking skills',
                ],
                'responsibilities' => [
                    'Develop and execute marketing strategies',
                    'Manage digital marketing campaigns',
                    'Analyze marketing performance metrics',
                    'Collaborate with creative teams',
                ],
                'skills_required' => [
                    'digital_marketing',
                    'strategy_development',
                    'analytics',
                    'project_management',
                ],
                'education_required' => [
                    'Bachelor\'s degree in Marketing or related field',
                ],
            ],

            // Senior Level Positions
            [
                'title' => 'Senior Software Engineer',
                'code' => 'DEV003',
                'level' => PositionLevel::SENIOR,
                'status' => PositionStatus::ACTIVE,
                'salary_min' => 95000,
                'salary_max' => 125000,
                'hourly_rate_min' => 45,
                'hourly_rate_max' => 60,
                'is_remote' => true,
                'is_travel_required' => false,
                'experience_required' => 6,
                'requirements' => [
                    'Bachelor\'s degree in Computer Science or related field',
                    '6+ years of software development experience',
                    'Experience with system architecture',
                    'Strong leadership and mentoring skills',
                ],
                'responsibilities' => [
                    'Lead technical projects and initiatives',
                    'Mentor and guide development teams',
                    'Make architectural decisions',
                    'Collaborate with product and business teams',
                ],
                'skills_required' => [
                    'system_architecture',
                    'technical_leadership',
                    'mentoring',
                    'strategic_thinking',
                ],
                'education_required' => [
                    'Bachelor\'s degree in Computer Science or related field (Master\'s preferred)',
                ],
            ],
            [
                'title' => 'Senior Marketing Manager',
                'code' => 'MKT003',
                'level' => PositionLevel::SENIOR,
                'status' => PositionStatus::ACTIVE,
                'salary_min' => 85000,
                'salary_max' => 110000,
                'hourly_rate_min' => 40,
                'hourly_rate_max' => 55,
                'is_remote' => true,
                'is_travel_required' => false,
                'experience_required' => 6,
                'requirements' => [
                    'Bachelor\'s degree in Marketing or related field',
                    '6+ years of marketing experience',
                    'Experience with team leadership',
                    'Strategic planning and execution skills',
                ],
                'responsibilities' => [
                    'Develop comprehensive marketing strategies',
                    'Lead marketing teams and initiatives',
                    'Analyze market trends and opportunities',
                    'Collaborate with executive leadership',
                ],
                'skills_required' => [
                    'strategic_planning',
                    'team_leadership',
                    'market_analysis',
                    'executive_communication',
                ],
                'education_required' => [
                    'Bachelor\'s degree in Marketing or related field (Master\'s preferred)',
                ],
            ],

            // Management Positions
            [
                'title' => 'Engineering Manager',
                'code' => 'DEV004',
                'level' => PositionLevel::MANAGER,
                'status' => PositionStatus::ACTIVE,
                'salary_min' => 130000,
                'salary_max' => 170000,
                'hourly_rate_min' => 65,
                'hourly_rate_max' => 85,
                'is_remote' => true,
                'is_travel_required' => false,
                'experience_required' => 8,
                'requirements' => [
                    'Bachelor\'s degree in Computer Science or related field',
                    '8+ years of software development experience',
                    '3+ years of team leadership experience',
                    'Strong project management skills',
                ],
                'responsibilities' => [
                    'Manage engineering teams and projects',
                    'Set technical direction and strategy',
                    'Hire and develop engineering talent',
                    'Collaborate with product and business stakeholders',
                ],
                'skills_required' => [
                    'people_management',
                    'technical_strategy',
                    'project_management',
                    'stakeholder_management',
                ],
                'education_required' => [
                    'Bachelor\'s degree in Computer Science or related field (Master\'s preferred)',
                ],
            ],
            [
                'title' => 'Marketing Director',
                'code' => 'MKT004',
                'level' => PositionLevel::DIRECTOR,
                'status' => PositionStatus::ACTIVE,
                'salary_min' => 150000,
                'salary_max' => 200000,
                'hourly_rate_min' => 75,
                'hourly_rate_max' => 100,
                'is_remote' => true,
                'is_travel_required' => false,
                'experience_required' => 10,
                'requirements' => [
                    'Bachelor\'s degree in Marketing or related field',
                    '10+ years of marketing experience',
                    '5+ years of leadership experience',
                    'Experience with budget management and strategic planning',
                ],
                'responsibilities' => [
                    'Set overall marketing strategy and vision',
                    'Manage marketing budget and resources',
                    'Lead cross-functional marketing initiatives',
                    'Represent marketing to executive leadership',
                ],
                'skills_required' => [
                    'executive_leadership',
                    'strategic_planning',
                    'budget_management',
                    'stakeholder_management',
                ],
                'education_required' => [
                    'Bachelor\'s degree in Marketing or related field (Master\'s preferred)',
                ],
            ],
        ];

        foreach ($positions as $positionData) {
            EmployeePosition::create(array_merge($positionData, [
                'department_id' => $department->id,
                'description' => $this->generateDescription($positionData['title'], $positionData['level']),
                'is_active' => $positionData['status'] !== PositionStatus::ARCHIVED,
                'metadata' => [
                    'benefits' => ['health_insurance', 'dental_insurance', '401k', 'stock_options'],
                    'work_schedule' => 'flexible',
                    'overtime_eligible' => false,
                    'probation_period' => 90,
                ],
            ]));
        }
    }

    /**
     * Create specialized positions across departments.
     */
    protected function createSpecializedPositions($departments): void
    {
        $specializedPositions = [
            [
                'title' => 'Data Scientist',
                'code' => 'DS001',
                'level' => PositionLevel::SENIOR,
                'status' => PositionStatus::HIRING,
                'salary_min' => 100000,
                'salary_max' => 140000,
                'hourly_rate_min' => 50,
                'hourly_rate_max' => 70,
                'is_remote' => true,
                'is_travel_required' => false,
                'experience_required' => 5,
                'department_id' => $departments->random()->id,
                'requirements' => [
                    'Master\'s degree in Data Science, Statistics, or related field',
                    '5+ years of experience in data analysis and machine learning',
                    'Proficiency in Python, R, and SQL',
                    'Experience with big data technologies',
                ],
                'responsibilities' => [
                    'Develop predictive models and algorithms',
                    'Analyze complex datasets',
                    'Communicate insights to stakeholders',
                    'Collaborate with engineering teams',
                ],
                'skills_required' => [
                    'machine_learning',
                    'statistical_analysis',
                    'data_visualization',
                    'programming',
                ],
                'education_required' => [
                    'Master\'s degree in Data Science, Statistics, or related field',
                ],
            ],
            [
                'title' => 'UX/UI Designer',
                'code' => 'UX001',
                'level' => PositionLevel::MID,
                'status' => PositionStatus::ACTIVE,
                'salary_min' => 70000,
                'salary_max' => 90000,
                'hourly_rate_min' => 35,
                'hourly_rate_max' => 45,
                'is_remote' => true,
                'is_travel_required' => false,
                'experience_required' => 3,
                'department_id' => $departments->random()->id,
                'requirements' => [
                    'Bachelor\'s degree in Design or related field',
                    '3+ years of UX/UI design experience',
                    'Proficiency in design tools (Figma, Sketch, Adobe Creative Suite)',
                    'Portfolio demonstrating user-centered design',
                ],
                'responsibilities' => [
                    'Create user interface designs',
                    'Conduct user research and testing',
                    'Collaborate with product and development teams',
                    'Maintain design system and guidelines',
                ],
                'skills_required' => [
                    'user_experience_design',
                    'user_interface_design',
                    'user_research',
                    'design_tools',
                ],
                'education_required' => [
                    'Bachelor\'s degree in Design or related field',
                ],
            ],
        ];

        foreach ($specializedPositions as $positionData) {
            EmployeePosition::create(array_merge($positionData, [
                'description' => $this->generateDescription($positionData['title'], $positionData['level']),
                'is_active' => $positionData['status'] !== PositionStatus::ARCHIVED,
                'metadata' => [
                    'benefits' => ['health_insurance', 'dental_insurance', '401k', 'stock_options'],
                    'work_schedule' => 'flexible',
                    'overtime_eligible' => false,
                    'probation_period' => 90,
                ],
            ]));
        }
    }

    /**
     * Generate a description for a position.
     */
    protected function generateDescription(string $title, PositionLevel $level): string
    {
        $descriptions = [
            PositionLevel::ENTRY => "An exciting entry-level opportunity for individuals looking to start their career in {$title}. This role provides hands-on experience and mentorship from experienced professionals.",
            PositionLevel::JUNIOR => "A junior-level position for {$title} that offers growth opportunities and the chance to work on meaningful projects while developing professional skills.",
            PositionLevel::MID => "A mid-level {$title} position that requires proven experience and offers opportunities for leadership and technical growth.",
            PositionLevel::SENIOR => "A senior-level {$title} role that requires extensive experience and offers opportunities to lead projects and mentor team members.",
            PositionLevel::LEAD => "A lead {$title} position that requires strong technical leadership skills and the ability to guide teams toward successful project delivery.",
            PositionLevel::MANAGER => "A management role for {$title} that requires strong leadership skills and the ability to manage teams and projects effectively.",
            PositionLevel::DIRECTOR => "A director-level {$title} position that requires strategic thinking and the ability to set direction for entire departments.",
            PositionLevel::EXECUTIVE => "An executive-level {$title} role that requires strategic vision and the ability to lead organizational change and growth.",
        ];

        return $descriptions[$level] ?? $descriptions[PositionLevel::MID];
    }
}
