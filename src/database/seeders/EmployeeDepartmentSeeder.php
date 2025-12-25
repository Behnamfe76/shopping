<?php

namespace Database\Seeders;

use App\Models\Employee;
use App\Models\EmployeeDepartment;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EmployeeDepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing data
        DB::table('employee_departments')->truncate();

        // Create root departments
        $this->createRootDepartments();

        // Create child departments
        $this->createChildDepartments();

        // Assign managers to departments
        $this->assignManagers();

        // Create additional sample departments
        $this->createSampleDepartments();
    }

    /**
     * Create root departments
     */
    protected function createRootDepartments(): void
    {
        $rootDepartments = [
            [
                'name' => 'Engineering',
                'code' => 'ENG',
                'description' => 'Core engineering team responsible for product development',
                'location' => 'San Francisco',
                'budget' => 2500000.00,
                'headcount_limit' => 150,
                'is_active' => true,
                'status' => 'active',
                'metadata' => [
                    'tech_stack' => ['PHP', 'Laravel', 'React', 'Vue.js'],
                    'development_methodology' => 'Agile',
                    'code_review_policy' => 'Mandatory for all changes',
                    'testing_requirements' => 'Unit tests + Integration tests',
                    'deployment_frequency' => 'Daily',
                    'on_call_schedule' => '24/7 rotation',
                ],
            ],
            [
                'name' => 'Sales',
                'code' => 'SALES',
                'description' => 'Sales team responsible for revenue generation',
                'location' => 'New York',
                'budget' => 1800000.00,
                'headcount_limit' => 100,
                'is_active' => true,
                'status' => 'active',
                'metadata' => [
                    'sales_methodology' => 'BANT',
                    'target_markets' => ['Enterprise', 'SMB'],
                    'quota_period' => 'Quarterly',
                    'commission_structure' => 'Base + Commission + Bonuses',
                    'sales_tools' => ['CRM', 'Salesforce', 'HubSpot'],
                    'territory_assignment' => 'Geographic + Industry vertical',
                ],
            ],
            [
                'name' => 'Marketing',
                'code' => 'MKTG',
                'description' => 'Marketing team responsible for brand awareness and lead generation',
                'location' => 'London',
                'budget' => 1200000.00,
                'headcount_limit' => 80,
                'is_active' => true,
                'status' => 'active',
                'metadata' => [
                    'marketing_channels' => ['Digital', 'Social Media', 'Content', 'Email'],
                    'target_audience' => ['B2B', 'Enterprise'],
                    'campaign_frequency' => 'Weekly',
                    'kpis' => ['Lead Generation', 'Brand Awareness', 'Conversion Rate'],
                    'tools_platforms' => ['Google Analytics', 'HubSpot', 'Mailchimp'],
                    'content_strategy' => 'Educational + Thought Leadership',
                ],
            ],
            [
                'name' => 'Finance',
                'code' => 'FIN',
                'description' => 'Finance team responsible for financial planning and reporting',
                'location' => 'New York',
                'budget' => 800000.00,
                'headcount_limit' => 50,
                'is_active' => true,
                'status' => 'active',
                'metadata' => [
                    'reporting_frequency' => 'Monthly',
                    'budget_cycle' => 'Quarterly',
                    'approval_workflow' => 'Manager + Finance + HR',
                    'key_metrics' => ['Budget Variance', 'Cash Flow', 'Profitability'],
                    'compliance_requirements' => ['SOX', 'GAAP', 'Tax Regulations'],
                ],
            ],
            [
                'name' => 'Human Resources',
                'code' => 'HR',
                'description' => 'HR team responsible for talent management and employee relations',
                'location' => 'San Francisco',
                'budget' => 600000.00,
                'headcount_limit' => 40,
                'is_active' => true,
                'status' => 'active',
                'metadata' => [
                    'recruitment_focus' => ['Engineering', 'Sales', 'Marketing'],
                    'employee_benefits' => ['Health Insurance', '401k', 'Stock Options'],
                    'performance_management' => 'Quarterly Reviews',
                    'training_programs' => ['Leadership', 'Technical Skills', 'Soft Skills'],
                    'diversity_initiatives' => ['Inclusive Hiring', 'Equal Pay', 'Cultural Training'],
                ],
            ],
        ];

        foreach ($rootDepartments as $department) {
            EmployeeDepartment::create($department);
        }
    }

    /**
     * Create child departments
     */
    protected function createChildDepartments(): void
    {
        // Get root departments
        $engineering = EmployeeDepartment::where('code', 'ENG')->first();
        $sales = EmployeeDepartment::where('code', 'SALES')->first();
        $marketing = EmployeeDepartment::where('code', 'MKTG')->first();

        // Engineering sub-departments
        if ($engineering) {
            $this->createEngineeringSubDepartments($engineering->id);
        }

        // Sales sub-departments
        if ($sales) {
            $this->createSalesSubDepartments($sales->id);
        }

        // Marketing sub-departments
        if ($marketing) {
            $this->createMarketingSubDepartments($marketing->id);
        }
    }

    /**
     * Create engineering sub-departments
     */
    protected function createEngineeringSubDepartments(int $parentId): void
    {
        $subDepartments = [
            [
                'name' => 'Frontend Development',
                'code' => 'ENG-FE',
                'description' => 'Frontend development team focusing on user interface',
                'location' => 'San Francisco',
                'budget' => 800000.00,
                'headcount_limit' => 45,
                'parent_id' => $parentId,
            ],
            [
                'name' => 'Backend Development',
                'code' => 'ENG-BE',
                'description' => 'Backend development team focusing on server-side logic',
                'location' => 'San Francisco',
                'budget' => 900000.00,
                'headcount_limit' => 50,
                'parent_id' => $parentId,
            ],
            [
                'name' => 'DevOps',
                'code' => 'ENG-DEVOPS',
                'description' => 'DevOps team responsible for infrastructure and deployment',
                'location' => 'San Francisco',
                'budget' => 600000.00,
                'headcount_limit' => 30,
                'parent_id' => $parentId,
            ],
            [
                'name' => 'Quality Assurance',
                'code' => 'ENG-QA',
                'description' => 'QA team responsible for testing and quality control',
                'location' => 'San Francisco',
                'budget' => 400000.00,
                'headcount_limit' => 25,
                'parent_id' => $parentId,
            ],
        ];

        foreach ($subDepartments as $department) {
            EmployeeDepartment::create($department);
        }
    }

    /**
     * Create sales sub-departments
     */
    protected function createSalesSubDepartments(int $parentId): void
    {
        $subDepartments = [
            [
                'name' => 'Enterprise Sales',
                'code' => 'SALES-ENT',
                'description' => 'Enterprise sales team for large corporate clients',
                'location' => 'New York',
                'budget' => 1000000.00,
                'headcount_limit' => 50,
                'parent_id' => $parentId,
            ],
            [
                'name' => 'SMB Sales',
                'code' => 'SALES-SMB',
                'description' => 'SMB sales team for small and medium businesses',
                'location' => 'New York',
                'budget' => 500000.00,
                'headcount_limit' => 30,
                'parent_id' => $parentId,
            ],
            [
                'name' => 'Sales Development',
                'code' => 'SALES-SDR',
                'description' => 'Sales development representatives for lead generation',
                'location' => 'New York',
                'budget' => 300000.00,
                'headcount_limit' => 20,
                'parent_id' => $parentId,
            ],
        ];

        foreach ($subDepartments as $department) {
            EmployeeDepartment::create($department);
        }
    }

    /**
     * Create marketing sub-departments
     */
    protected function createMarketingSubDepartments(int $parentId): void
    {
        $subDepartments = [
            [
                'name' => 'Digital Marketing',
                'code' => 'MKTG-DIG',
                'description' => 'Digital marketing team for online campaigns',
                'location' => 'London',
                'budget' => 500000.00,
                'headcount_limit' => 35,
                'parent_id' => $parentId,
            ],
            [
                'name' => 'Content Marketing',
                'code' => 'MKTG-CONT',
                'description' => 'Content marketing team for thought leadership',
                'location' => 'London',
                'budget' => 400000.00,
                'headcount_limit' => 25,
                'parent_id' => $parentId,
            ],
            [
                'name' => 'Product Marketing',
                'code' => 'MKTG-PROD',
                'description' => 'Product marketing team for product positioning',
                'location' => 'London',
                'budget' => 300000.00,
                'headcount_limit' => 20,
                'parent_id' => $parentId,
            ],
        ];

        foreach ($subDepartments as $department) {
            EmployeeDepartment::create($department);
        }
    }

    /**
     * Assign managers to departments
     */
    protected function assignManagers(): void
    {
        // This would typically assign managers from existing employees
        // For now, we'll just update some departments with placeholder manager IDs

        $departments = EmployeeDepartment::all();
        foreach ($departments as $department) {
            if ($department->code === 'ENG' || $department->code === 'SALES' || $department->code === 'MKTG') {
                // Assign manager ID 1 to root departments (assuming employee ID 1 exists)
                $department->update(['manager_id' => 1]);
            }
        }
    }

    /**
     * Create additional sample departments
     */
    protected function createSampleDepartments(): void
    {
        $sampleDepartments = [
            [
                'name' => 'Customer Success',
                'code' => 'CS',
                'description' => 'Customer success team for retention and satisfaction',
                'location' => 'San Francisco',
                'budget' => 500000.00,
                'headcount_limit' => 30,
                'is_active' => true,
                'status' => 'active',
            ],
            [
                'name' => 'Legal',
                'code' => 'LEGAL',
                'description' => 'Legal team for compliance and contracts',
                'location' => 'New York',
                'budget' => 400000.00,
                'headcount_limit' => 15,
                'is_active' => true,
                'status' => 'active',
            ],
            [
                'name' => 'Research & Development',
                'code' => 'RND',
                'description' => 'R&D team for innovation and new technologies',
                'location' => 'San Francisco',
                'budget' => 1500000.00,
                'headcount_limit' => 60,
                'is_active' => true,
                'status' => 'active',
            ],
        ];

        foreach ($sampleDepartments as $department) {
            EmployeeDepartment::create($department);
        }
    }
}
