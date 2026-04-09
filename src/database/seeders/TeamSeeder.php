<?php

namespace Fereydooni\Shopping\database\seeders;

use Fereydooni\Shopping\app\Models\Department;
use Fereydooni\Shopping\app\Models\Employee;
use Fereydooni\Shopping\app\Models\Team;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TeamSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing data
        DB::table('team_members')->truncate();
        DB::table('teams')->truncate();

        // Create teams for each department
        $this->createEngineeringTeams();
        $this->createSalesTeams();
        $this->createMarketingTeams();
        $this->createFinanceTeams();
        $this->createHRTeams();
        $this->createCustomerSuccessTeams();
        $this->createRnDTeams();

        // Assign members to teams (if employees exist)
        $this->assignTeamMembers();
    }

    /**
     * Create Engineering teams
     */
    protected function createEngineeringTeams(): void
    {
        $engineering = Department::where('code', 'ENG')->first();
        if (! $engineering) {
            return;
        }

        $teams = [
            [
                'name' => 'Core Platform Team',
                'code' => 'ENG-CORE',
                'description' => 'Core platform development team responsible for foundational architecture',
                'department_id' => $engineering->id,
                'location' => 'San Francisco',
                'member_limit' => 8,
                'is_active' => true,
                'status' => 'active',
                'metadata' => [
                    'tech_focus' => ['Backend', 'Architecture', 'API Design'],
                    'sprint_length' => '2 weeks',
                    'meeting_schedule' => 'Daily standup, Weekly retrospective',
                    'tools' => ['Jira', 'GitHub', 'Slack'],
                ],
            ],
            [
                'name' => 'Frontend Web Team',
                'code' => 'ENG-FE-WEB',
                'description' => 'Frontend team focused on web application development',
                'department_id' => $engineering->id,
                'location' => 'San Francisco',
                'member_limit' => 10,
                'is_active' => true,
                'status' => 'active',
                'metadata' => [
                    'tech_focus' => ['React', 'Vue.js', 'TypeScript', 'CSS'],
                    'sprint_length' => '2 weeks',
                    'design_collaboration' => 'Daily with design team',
                    'testing_framework' => 'Jest + Cypress',
                ],
            ],
            [
                'name' => 'Mobile Development Team',
                'code' => 'ENG-MOBILE',
                'description' => 'Mobile app development team for iOS and Android',
                'department_id' => $engineering->id,
                'location' => 'San Francisco',
                'member_limit' => 6,
                'is_active' => true,
                'status' => 'active',
                'metadata' => [
                    'tech_focus' => ['React Native', 'Swift', 'Kotlin'],
                    'platforms' => ['iOS', 'Android'],
                    'release_cycle' => 'Bi-weekly',
                ],
            ],
            [
                'name' => 'Infrastructure Team',
                'code' => 'ENG-INFRA',
                'description' => 'DevOps and infrastructure team for cloud operations',
                'department_id' => $engineering->id,
                'location' => 'San Francisco',
                'member_limit' => 5,
                'is_active' => true,
                'status' => 'active',
                'metadata' => [
                    'tech_focus' => ['AWS', 'Kubernetes', 'Terraform', 'Monitoring'],
                    'on_call_rotation' => '24/7',
                    'incident_response' => 'Level 1-3 support',
                ],
            ],
            [
                'name' => 'Security Team',
                'code' => 'ENG-SEC',
                'description' => 'Security team for application and infrastructure security',
                'department_id' => $engineering->id,
                'location' => 'San Francisco',
                'member_limit' => 4,
                'is_active' => true,
                'status' => 'active',
                'metadata' => [
                    'tech_focus' => ['Penetration Testing', 'Security Audits', 'Compliance'],
                    'certifications' => ['CISSP', 'CEH', 'Security+'],
                    'audit_frequency' => 'Quarterly',
                ],
            ],
            [
                'name' => 'QA Automation Team',
                'code' => 'ENG-QA-AUTO',
                'description' => 'Automated testing and quality assurance team',
                'department_id' => $engineering->id,
                'location' => 'San Francisco',
                'member_limit' => 6,
                'is_active' => true,
                'status' => 'active',
                'metadata' => [
                    'tech_focus' => ['Selenium', 'Cypress', 'API Testing', 'Performance Testing'],
                    'test_coverage_goal' => '85%',
                    'automation_tools' => ['Jenkins', 'GitHub Actions'],
                ],
            ],
        ];

        foreach ($teams as $teamData) {
            Team::create($teamData);
        }
    }

    /**
     * Create Sales teams
     */
    protected function createSalesTeams(): void
    {
        $sales = Department::where('code', 'SALES')->first();
        if (! $sales) {
            return;
        }

        $teams = [
            [
                'name' => 'Enterprise Account Executives',
                'code' => 'SALES-ENT-AE',
                'description' => 'Senior account executives handling enterprise deals',
                'department_id' => $sales->id,
                'location' => 'New York',
                'member_limit' => 12,
                'is_active' => true,
                'status' => 'active',
                'metadata' => [
                    'quota_structure' => 'Annual quota with quarterly targets',
                    'deal_size' => '$100K+',
                    'sales_cycle' => '3-6 months',
                    'territory' => 'Fortune 500 companies',
                ],
            ],
            [
                'name' => 'SMB Sales Team',
                'code' => 'SALES-SMB-TEAM',
                'description' => 'Sales team for small and medium businesses',
                'department_id' => $sales->id,
                'location' => 'New York',
                'member_limit' => 15,
                'is_active' => true,
                'status' => 'active',
                'metadata' => [
                    'quota_structure' => 'Monthly quota',
                    'deal_size' => '$10K-$50K',
                    'sales_cycle' => '2-4 weeks',
                    'territory' => 'SMB market',
                ],
            ],
            [
                'name' => 'Sales Development Representatives',
                'code' => 'SALES-SDR',
                'description' => 'SDR team focused on lead generation and qualification',
                'department_id' => $sales->id,
                'location' => 'New York',
                'member_limit' => 20,
                'is_active' => true,
                'status' => 'active',
                'metadata' => [
                    'kpis' => ['Qualified leads', 'Meeting bookings', 'Pipeline contribution'],
                    'outreach_channels' => ['Email', 'Phone', 'LinkedIn'],
                    'daily_activities' => '50 calls + 100 emails',
                ],
            ],
            [
                'name' => 'Sales Operations Team',
                'code' => 'SALES-OPS',
                'description' => 'Sales operations and enablement team',
                'department_id' => $sales->id,
                'location' => 'New York',
                'member_limit' => 6,
                'is_active' => true,
                'status' => 'active',
                'metadata' => [
                    'responsibilities' => ['CRM management', 'Training', 'Process optimization'],
                    'tools' => ['Salesforce', 'Gong', 'Sales Navigator'],
                ],
            ],
        ];

        foreach ($teams as $teamData) {
            Team::create($teamData);
        }
    }

    /**
     * Create Marketing teams
     */
    protected function createMarketingTeams(): void
    {
        $marketing = Department::where('code', 'MKTG')->first();
        if (! $marketing) {
            return;
        }

        $teams = [
            [
                'name' => 'Content Creation Team',
                'code' => 'MKTG-CONTENT',
                'description' => 'Content writers and creators for blog posts, whitepapers, and case studies',
                'department_id' => $marketing->id,
                'location' => 'London',
                'member_limit' => 8,
                'is_active' => true,
                'status' => 'active',
                'metadata' => [
                    'content_types' => ['Blog posts', 'Whitepapers', 'Case studies', 'eBooks'],
                    'publishing_frequency' => '3 blog posts per week',
                    'seo_focus' => 'High-value keywords',
                ],
            ],
            [
                'name' => 'Social Media Team',
                'code' => 'MKTG-SOCIAL',
                'description' => 'Social media management and community engagement team',
                'department_id' => $marketing->id,
                'location' => 'London',
                'member_limit' => 5,
                'is_active' => true,
                'status' => 'active',
                'metadata' => [
                    'platforms' => ['LinkedIn', 'Twitter', 'Facebook', 'Instagram'],
                    'posting_schedule' => 'Daily on all platforms',
                    'engagement_goals' => '10% increase per quarter',
                ],
            ],
            [
                'name' => 'Performance Marketing Team',
                'code' => 'MKTG-PERF',
                'description' => 'Paid advertising and performance marketing team',
                'department_id' => $marketing->id,
                'location' => 'London',
                'member_limit' => 6,
                'is_active' => true,
                'status' => 'active',
                'metadata' => [
                    'channels' => ['Google Ads', 'Facebook Ads', 'LinkedIn Ads'],
                    'budget' => 'Monthly budget allocation',
                    'kpis' => ['CAC', 'ROAS', 'Conversion rate'],
                ],
            ],
            [
                'name' => 'Brand & Design Team',
                'code' => 'MKTG-BRAND',
                'description' => 'Brand strategy and visual design team',
                'department_id' => $marketing->id,
                'location' => 'London',
                'member_limit' => 7,
                'is_active' => true,
                'status' => 'active',
                'metadata' => [
                    'responsibilities' => ['Brand guidelines', 'Visual design', 'Creative assets'],
                    'design_tools' => ['Figma', 'Adobe Creative Suite', 'Canva'],
                ],
            ],
        ];

        foreach ($teams as $teamData) {
            Team::create($teamData);
        }
    }

    /**
     * Create Finance teams
     */
    protected function createFinanceTeams(): void
    {
        $finance = Department::where('code', 'FIN')->first();
        if (! $finance) {
            return;
        }

        $teams = [
            [
                'name' => 'Accounting Team',
                'code' => 'FIN-ACCT',
                'description' => 'Accounts payable, receivable, and general ledger team',
                'department_id' => $finance->id,
                'location' => 'New York',
                'member_limit' => 8,
                'is_active' => true,
                'status' => 'active',
                'metadata' => [
                    'responsibilities' => ['AP', 'AR', 'General Ledger', 'Month-end close'],
                    'software' => ['QuickBooks', 'NetSuite', 'SAP'],
                    'reporting_deadline' => '5 business days after month-end',
                ],
            ],
            [
                'name' => 'Financial Planning & Analysis',
                'code' => 'FIN-FPA',
                'description' => 'FP&A team for budgeting, forecasting, and analysis',
                'department_id' => $finance->id,
                'location' => 'New York',
                'member_limit' => 6,
                'is_active' => true,
                'status' => 'active',
                'metadata' => [
                    'responsibilities' => ['Budget planning', 'Forecasting', 'Variance analysis'],
                    'reporting_cycle' => 'Monthly + Quarterly board presentations',
                    'tools' => ['Excel', 'Tableau', 'Adaptive Planning'],
                ],
            ],
            [
                'name' => 'Payroll Team',
                'code' => 'FIN-PAYROLL',
                'description' => 'Payroll processing and compliance team',
                'department_id' => $finance->id,
                'location' => 'New York',
                'member_limit' => 4,
                'is_active' => true,
                'status' => 'active',
                'metadata' => [
                    'payroll_frequency' => 'Bi-weekly',
                    'compliance' => ['Tax withholding', 'Benefits administration'],
                    'systems' => ['ADP', 'Gusto'],
                ],
            ],
        ];

        foreach ($teams as $teamData) {
            Team::create($teamData);
        }
    }

    /**
     * Create HR teams
     */
    protected function createHRTeams(): void
    {
        $hr = Department::where('code', 'HR')->first();
        if (! $hr) {
            return;
        }

        $teams = [
            [
                'name' => 'Talent Acquisition Team',
                'code' => 'HR-TA',
                'description' => 'Recruitment and talent acquisition team',
                'department_id' => $hr->id,
                'location' => 'San Francisco',
                'member_limit' => 10,
                'is_active' => true,
                'status' => 'active',
                'metadata' => [
                    'hiring_focus' => ['Engineering', 'Sales', 'Marketing'],
                    'sourcing_channels' => ['LinkedIn', 'Indeed', 'Referrals'],
                    'interview_process' => 'Phone screen → Technical → Culture fit → Offer',
                ],
            ],
            [
                'name' => 'Employee Relations Team',
                'code' => 'HR-ER',
                'description' => 'Employee relations and engagement team',
                'department_id' => $hr->id,
                'location' => 'San Francisco',
                'member_limit' => 5,
                'is_active' => true,
                'status' => 'active',
                'metadata' => [
                    'responsibilities' => ['Conflict resolution', 'Engagement programs', 'Culture initiatives'],
                    'programs' => ['Mentorship', 'Wellness', 'Recognition'],
                ],
            ],
            [
                'name' => 'Compensation & Benefits Team',
                'code' => 'HR-CB',
                'description' => 'Compensation and benefits administration team',
                'department_id' => $hr->id,
                'location' => 'San Francisco',
                'member_limit' => 4,
                'is_active' => true,
                'status' => 'active',
                'metadata' => [
                    'responsibilities' => ['Salary benchmarking', 'Benefits enrollment', 'Equity administration'],
                    'review_cycle' => 'Annual compensation review',
                ],
            ],
        ];

        foreach ($teams as $teamData) {
            Team::create($teamData);
        }
    }

    /**
     * Create Customer Success teams
     */
    protected function createCustomerSuccessTeams(): void
    {
        $cs = Department::where('code', 'CS')->first();
        if (! $cs) {
            return;
        }

        $teams = [
            [
                'name' => 'Enterprise Customer Success',
                'code' => 'CS-ENT',
                'description' => 'Customer success managers for enterprise accounts',
                'department_id' => $cs->id,
                'location' => 'San Francisco',
                'member_limit' => 10,
                'is_active' => true,
                'status' => 'active',
                'metadata' => [
                    'account_portfolio' => '15-20 accounts per CSM',
                    'responsibilities' => ['Onboarding', 'Quarterly business reviews', 'Renewal management'],
                    'success_metrics' => ['NPS', 'Retention rate', 'Product adoption'],
                ],
            ],
            [
                'name' => 'Technical Support Team',
                'code' => 'CS-SUPPORT',
                'description' => 'Technical support team for product assistance',
                'department_id' => $cs->id,
                'location' => 'San Francisco',
                'member_limit' => 15,
                'is_active' => true,
                'status' => 'active',
                'metadata' => [
                    'support_channels' => ['Email', 'Chat', 'Phone'],
                    'sla' => 'Response within 2 hours for priority issues',
                    'coverage' => 'Business hours + on-call rotation',
                ],
            ],
        ];

        foreach ($teams as $teamData) {
            Team::create($teamData);
        }
    }

    /**
     * Create R&D teams
     */
    protected function createRnDTeams(): void
    {
        $rnd = Department::where('code', 'RND')->first();
        if (! $rnd) {
            return;
        }

        $teams = [
            [
                'name' => 'AI & Machine Learning Team',
                'code' => 'RND-AI',
                'description' => 'Research team for AI and machine learning technologies',
                'department_id' => $rnd->id,
                'location' => 'San Francisco',
                'member_limit' => 8,
                'is_active' => true,
                'status' => 'active',
                'metadata' => [
                    'research_areas' => ['NLP', 'Computer Vision', 'Recommendation Systems'],
                    'tech_stack' => ['Python', 'TensorFlow', 'PyTorch'],
                    'publication_goals' => 'Conference papers + Patents',
                ],
            ],
            [
                'name' => 'Innovation Lab',
                'code' => 'RND-LAB',
                'description' => 'Innovation lab for experimental products and features',
                'department_id' => $rnd->id,
                'location' => 'San Francisco',
                'member_limit' => 6,
                'is_active' => true,
                'status' => 'active',
                'metadata' => [
                    'methodology' => 'Lean startup approach',
                    'project_duration' => '3-6 months per experiment',
                    'success_criteria' => 'User validation + Business viability',
                ],
            ],
        ];

        foreach ($teams as $teamData) {
            Team::create($teamData);
        }
    }

    /**
     * Assign members to teams (if employees exist)
     */
    protected function assignTeamMembers(): void
    {
        // Get all teams and employees
        $teams = Team::all();
        $employees = Employee::take(50)->get(); // Get first 50 employees if they exist

        if ($employees->isEmpty()) {
            // No employees to assign
            return;
        }

        // Assign employees to teams based on their departments
        foreach ($teams as $team) {
            // Get employees from the same department
            $departmentEmployees = $employees->filter(function ($employee) use ($team) {
                return $employee->department_id === $team->department_id;
            });

            if ($departmentEmployees->isEmpty()) {
                // Try to get any employees
                $departmentEmployees = $employees->take(3);
            }

            // Assign 3-5 random employees to each team
            $teamMembers = $departmentEmployees->random(min(3, $departmentEmployees->count()));

            foreach ($teamMembers as $index => $employee) {
                // First member is a manager, others are regular members
                $isManager = ($index === 0);

                $team->members()->attach($employee->id, [
                    'is_manager' => $isManager,
                    'joined_at' => now()->subDays(rand(30, 365))->toDateString(),
                    'metadata' => json_encode([
                        'role_in_team' => $isManager ? 'Team Lead' : 'Team Member',
                    ]),
                ]);
            }
        }
    }
}
