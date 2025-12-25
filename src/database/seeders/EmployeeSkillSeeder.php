<?php

namespace Database\Seeders;

use App\Enums\ProficiencyLevel;
use App\Enums\SkillCategory;
use App\Models\Employee;
use App\Models\EmployeeSkill;
use App\Models\User;
use Illuminate\Database\Seeder;

class EmployeeSkillSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Seeding Employee Skills...');

        // Get existing employees
        $employees = Employee::with('user')->get();

        if ($employees->isEmpty()) {
            $this->command->warn('No employees found. Please run EmployeeSeeder first.');

            return;
        }

        // Get users for verification
        $users = User::where('role', 'admin')->orWhere('role', 'manager')->get();
        $verifier = $users->first();

        // Sample skill data for different categories
        $sampleSkills = $this->getSampleSkills();

        $skillsCreated = 0;
        $skillsVerified = 0;

        foreach ($employees as $employee) {
            // Determine how many skills this employee should have (1-8 skills)
            $skillCount = rand(1, 8);

            // Select random skills for this employee
            $employeeSkills = $this->faker->randomElements($sampleSkills, $skillCount);

            foreach ($employeeSkills as $skillData) {
                // Adjust proficiency based on employee's experience
                $proficiencyLevel = $this->adjustProficiencyForEmployee($employee, $skillData['proficiency_level']);
                $yearsExperience = $this->calculateYearsExperience($proficiencyLevel);

                $skill = EmployeeSkill::create([
                    'employee_id' => $employee->id,
                    'skill_name' => $skillData['name'],
                    'skill_category' => $skillData['category'],
                    'proficiency_level' => $proficiencyLevel,
                    'years_experience' => $yearsExperience,
                    'certification_required' => $skillData['certification_required'] ?? false,
                    'certification_name' => $this->generateCertificationName($skillData['name']),
                    'certification_date' => $this->generateCertificationDate($yearsExperience),
                    'certification_expiry' => $this->generateCertificationExpiry(),
                    'is_verified' => $this->faker->boolean(80), // 80% chance of being verified
                    'verified_by' => $verifier ? $verifier->id : null,
                    'verified_at' => $this->faker->optional(0.8)->dateTimeBetween('-1 year', 'now'),
                    'is_active' => $this->faker->boolean(95), // 95% chance of being active
                    'is_primary' => $this->faker->boolean(15), // 15% chance of being primary
                    'is_required' => $skillData['required'] ?? false,
                    'skill_description' => $this->generateSkillDescription($skillData['name'], $skillData['category']),
                    'keywords' => $this->generateKeywords($skillData['name'], $skillData['category']),
                    'tags' => $this->generateTags($skillData['category']),
                    'notes' => $this->faker->optional(0.3)->sentence(),
                    'attachments' => $this->faker->optional(0.2)->randomElements([
                        'certificate.pdf', 'portfolio.pdf', 'reference.pdf', 'assessment.pdf',
                    ], rand(1, 2)),
                ]);

                $skillsCreated++;

                if ($skill->is_verified) {
                    $skillsVerified++;
                }
            }
        }

        // Ensure at least one primary skill per employee
        $this->ensurePrimarySkills($employees);

        $this->command->info("Created {$skillsCreated} employee skills");
        $this->command->info("Verified {$skillsVerified} skills");
        $this->command->info('Employee Skills seeded successfully!');
    }

    /**
     * Get sample skills data
     */
    private function getSampleSkills(): array
    {
        return [
            // Technical Skills
            ['name' => 'PHP', 'category' => SkillCategory::TECHNICAL, 'proficiency_level' => ProficiencyLevel::INTERMEDIATE, 'certification_required' => true, 'required' => false],
            ['name' => 'JavaScript', 'category' => SkillCategory::TECHNICAL, 'proficiency_level' => ProficiencyLevel::ADVANCED, 'certification_required' => false, 'required' => false],
            ['name' => 'Python', 'category' => SkillCategory::TECHNICAL, 'proficiency_level' => ProficiencyLevel::INTERMEDIATE, 'certification_required' => true, 'required' => false],
            ['name' => 'Java', 'category' => SkillCategory::TECHNICAL, 'proficiency_level' => ProficiencyLevel::ADVANCED, 'certification_required' => true, 'required' => false],
            ['name' => 'React', 'category' => SkillCategory::TECHNICAL, 'proficiency_level' => ProficiencyLevel::INTERMEDIATE, 'certification_required' => false, 'required' => false],
            ['name' => 'Vue.js', 'category' => SkillCategory::TECHNICAL, 'proficiency_level' => ProficiencyLevel::BEGINNER, 'certification_required' => false, 'required' => false],
            ['name' => 'Node.js', 'category' => SkillCategory::TECHNICAL, 'proficiency_level' => ProficiencyLevel::INTERMEDIATE, 'certification_required' => false, 'required' => false],
            ['name' => 'Laravel', 'category' => SkillCategory::TECHNICAL, 'proficiency_level' => ProficiencyLevel::INTERMEDIATE, 'certification_required' => false, 'required' => false],
            ['name' => 'Django', 'category' => SkillCategory::TECHNICAL, 'proficiency_level' => ProficiencyLevel::BEGINNER, 'certification_required' => false, 'required' => false],
            ['name' => 'Spring', 'category' => SkillCategory::TECHNICAL, 'proficiency_level' => ProficiencyLevel::ADVANCED, 'certification_required' => true, 'required' => false],
            ['name' => 'MySQL', 'category' => SkillCategory::TECHNICAL, 'proficiency_level' => ProficiencyLevel::INTERMEDIATE, 'certification_required' => false, 'required' => false],
            ['name' => 'PostgreSQL', 'category' => SkillCategory::TECHNICAL, 'proficiency_level' => ProficiencyLevel::BEGINNER, 'certification_required' => false, 'required' => false],
            ['name' => 'MongoDB', 'category' => SkillCategory::TECHNICAL, 'proficiency_level' => ProficiencyLevel::BEGINNER, 'certification_required' => false, 'required' => false],
            ['name' => 'Redis', 'category' => SkillCategory::TECHNICAL, 'proficiency_level' => ProficiencyLevel::INTERMEDIATE, 'certification_required' => false, 'required' => false],
            ['name' => 'Docker', 'category' => SkillCategory::TECHNICAL, 'proficiency_level' => ProficiencyLevel::INTERMEDIATE, 'certification_required' => false, 'required' => false],
            ['name' => 'Kubernetes', 'category' => SkillCategory::TECHNICAL, 'proficiency_level' => ProficiencyLevel::BEGINNER, 'certification_required' => false, 'required' => false],
            ['name' => 'AWS', 'category' => SkillCategory::TECHNICAL, 'proficiency_level' => ProficiencyLevel::INTERMEDIATE, 'certification_required' => true, 'required' => false],
            ['name' => 'Azure', 'category' => SkillCategory::TECHNICAL, 'proficiency_level' => ProficiencyLevel::BEGINNER, 'certification_required' => false, 'required' => false],
            ['name' => 'Git', 'category' => SkillCategory::TECHNICAL, 'proficiency_level' => ProficiencyLevel::ADVANCED, 'certification_required' => false, 'required' => true],
            ['name' => 'Linux', 'category' => SkillCategory::TECHNICAL, 'proficiency_level' => ProficiencyLevel::INTERMEDIATE, 'certification_required' => false, 'required' => false],

            // Soft Skills
            ['name' => 'Leadership', 'category' => SkillCategory::SOFT_SKILLS, 'proficiency_level' => ProficiencyLevel::INTERMEDIATE, 'certification_required' => false, 'required' => false],
            ['name' => 'Communication', 'category' => SkillCategory::SOFT_SKILLS, 'proficiency_level' => ProficiencyLevel::ADVANCED, 'certification_required' => false, 'required' => true],
            ['name' => 'Problem Solving', 'category' => SkillCategory::SOFT_SKILLS, 'proficiency_level' => ProficiencyLevel::ADVANCED, 'certification_required' => false, 'required' => true],
            ['name' => 'Teamwork', 'category' => SkillCategory::SOFT_SKILLS, 'proficiency_level' => ProficiencyLevel::ADVANCED, 'certification_required' => false, 'required' => true],
            ['name' => 'Time Management', 'category' => SkillCategory::SOFT_SKILLS, 'proficiency_level' => ProficiencyLevel::INTERMEDIATE, 'certification_required' => false, 'required' => false],
            ['name' => 'Critical Thinking', 'category' => SkillCategory::SOFT_SKILLS, 'proficiency_level' => ProficiencyLevel::INTERMEDIATE, 'certification_required' => false, 'required' => false],
            ['name' => 'Adaptability', 'category' => SkillCategory::SOFT_SKILLS, 'proficiency_level' => ProficiencyLevel::INTERMEDIATE, 'certification_required' => false, 'required' => false],
            ['name' => 'Creativity', 'category' => SkillCategory::SOFT_SKILLS, 'proficiency_level' => ProficiencyLevel::INTERMEDIATE, 'certification_required' => false, 'required' => false],
            ['name' => 'Negotiation', 'category' => SkillCategory::SOFT_SKILLS, 'proficiency_level' => ProficiencyLevel::BEGINNER, 'certification_required' => false, 'required' => false],
            ['name' => 'Conflict Resolution', 'category' => SkillCategory::SOFT_SKILLS, 'proficiency_level' => ProficiencyLevel::BEGINNER, 'certification_required' => false, 'required' => false],

            // Languages
            ['name' => 'English', 'category' => SkillCategory::LANGUAGES, 'proficiency_level' => ProficiencyLevel::MASTER, 'certification_required' => false, 'required' => true],
            ['name' => 'Spanish', 'category' => SkillCategory::LANGUAGES, 'proficiency_level' => ProficiencyLevel::INTERMEDIATE, 'certification_required' => false, 'required' => false],
            ['name' => 'French', 'category' => SkillCategory::LANGUAGES, 'proficiency_level' => ProficiencyLevel::BEGINNER, 'certification_required' => false, 'required' => false],
            ['name' => 'German', 'category' => SkillCategory::LANGUAGES, 'proficiency_level' => ProficiencyLevel::BEGINNER, 'certification_required' => false, 'required' => false],
            ['name' => 'Chinese', 'category' => SkillCategory::LANGUAGES, 'proficiency_level' => ProficiencyLevel::BEGINNER, 'certification_required' => false, 'required' => false],
            ['name' => 'Japanese', 'category' => SkillCategory::LANGUAGES, 'proficiency_level' => ProficiencyLevel::BEGINNER, 'certification_required' => false, 'required' => false],

            // Tools
            ['name' => 'Photoshop', 'category' => SkillCategory::TOOLS, 'proficiency_level' => ProficiencyLevel::INTERMEDIATE, 'certification_required' => false, 'required' => false],
            ['name' => 'Illustrator', 'category' => SkillCategory::TOOLS, 'proficiency_level' => ProficiencyLevel::BEGINNER, 'certification_required' => false, 'required' => false],
            ['name' => 'Figma', 'category' => SkillCategory::TOOLS, 'proficiency_level' => ProficiencyLevel::INTERMEDIATE, 'certification_required' => false, 'required' => false],
            ['name' => 'Sketch', 'category' => SkillCategory::TOOLS, 'proficiency_level' => ProficiencyLevel::BEGINNER, 'certification_required' => false, 'required' => false],
            ['name' => 'Excel', 'category' => SkillCategory::TOOLS, 'proficiency_level' => ProficiencyLevel::ADVANCED, 'certification_required' => false, 'required' => false],
            ['name' => 'PowerPoint', 'category' => SkillCategory::TOOLS, 'proficiency_level' => ProficiencyLevel::INTERMEDIATE, 'certification_required' => false, 'required' => false],
            ['name' => 'Power BI', 'category' => SkillCategory::TOOLS, 'proficiency_level' => ProficiencyLevel::BEGINNER, 'certification_required' => false, 'required' => false],
            ['name' => 'Tableau', 'category' => SkillCategory::TOOLS, 'proficiency_level' => ProficiencyLevel::BEGINNER, 'certification_required' => false, 'required' => false],

            // Methodologies
            ['name' => 'Agile', 'category' => SkillCategory::METHODOLOGIES, 'proficiency_level' => ProficiencyLevel::ADVANCED, 'certification_required' => true, 'required' => false],
            ['name' => 'Scrum', 'category' => SkillCategory::METHODOLOGIES, 'proficiency_level' => ProficiencyLevel::INTERMEDIATE, 'certification_required' => true, 'required' => false],
            ['name' => 'Kanban', 'category' => SkillCategory::METHODOLOGIES, 'proficiency_level' => ProficiencyLevel::INTERMEDIATE, 'certification_required' => false, 'required' => false],
            ['name' => 'DevOps', 'category' => SkillCategory::METHODOLOGIES, 'proficiency_level' => ProficiencyLevel::INTERMEDIATE, 'certification_required' => false, 'required' => false],
            ['name' => 'CI/CD', 'category' => SkillCategory::METHODOLOGIES, 'proficiency_level' => ProficiencyLevel::INTERMEDIATE, 'certification_required' => false, 'required' => false],
            ['name' => 'Waterfall', 'category' => SkillCategory::METHODOLOGIES, 'proficiency_level' => ProficiencyLevel::BEGINNER, 'certification_required' => false, 'required' => false],
            ['name' => 'Lean', 'category' => SkillCategory::METHODOLOGIES, 'proficiency_level' => ProficiencyLevel::BEGINNER, 'certification_required' => false, 'required' => false],
            ['name' => 'Six Sigma', 'category' => SkillCategory::METHODOLOGIES, 'proficiency_level' => ProficiencyLevel::BEGINNER, 'certification_required' => true, 'required' => false],

            // Certifications
            ['name' => 'PMP', 'category' => SkillCategory::CERTIFICATIONS, 'proficiency_level' => ProficiencyLevel::EXPERT, 'certification_required' => true, 'required' => false],
            ['name' => 'PRINCE2', 'category' => SkillCategory::CERTIFICATIONS, 'proficiency_level' => ProficiencyLevel::INTERMEDIATE, 'certification_required' => true, 'required' => false],
            ['name' => 'ITIL', 'category' => SkillCategory::CERTIFICATIONS, 'proficiency_level' => ProficiencyLevel::INTERMEDIATE, 'certification_required' => true, 'required' => false],
            ['name' => 'CISSP', 'category' => SkillCategory::CERTIFICATIONS, 'proficiency_level' => ProficiencyLevel::EXPERT, 'certification_required' => true, 'required' => false],
            ['name' => 'CompTIA A+', 'category' => SkillCategory::CERTIFICATIONS, 'proficiency_level' => ProficiencyLevel::INTERMEDIATE, 'certification_required' => true, 'required' => false],

            // Other
            ['name' => 'Project Management', 'category' => SkillCategory::OTHER, 'proficiency_level' => ProficiencyLevel::INTERMEDIATE, 'certification_required' => false, 'required' => false],
            ['name' => 'Business Analysis', 'category' => SkillCategory::OTHER, 'proficiency_level' => ProficiencyLevel::BEGINNER, 'certification_required' => false, 'required' => false],
            ['name' => 'Data Analysis', 'category' => SkillCategory::OTHER, 'proficiency_level' => ProficiencyLevel::INTERMEDIATE, 'certification_required' => false, 'required' => false],
            ['name' => 'UX/UI Design', 'category' => SkillCategory::OTHER, 'proficiency_level' => ProficiencyLevel::BEGINNER, 'certification_required' => false, 'required' => false],
            ['name' => 'Content Writing', 'category' => SkillCategory::OTHER, 'proficiency_level' => ProficiencyLevel::INTERMEDIATE, 'certification_required' => false, 'required' => false],
        ];
    }

    /**
     * Adjust proficiency level based on employee experience
     */
    private function adjustProficiencyForEmployee(Employee $employee, ProficiencyLevel $baseLevel): ProficiencyLevel
    {
        // Get employee's years of experience
        $employeeExperience = $employee->years_of_experience ?? 0;

        // Adjust proficiency based on experience
        if ($employeeExperience < 2) {
            return ProficiencyLevel::BEGINNER;
        } elseif ($employeeExperience < 5) {
            return in_array($baseLevel, [ProficiencyLevel::MASTER, ProficiencyLevel::EXPERT])
                ? ProficiencyLevel::ADVANCED
                : $baseLevel;
        } elseif ($employeeExperience < 8) {
            return in_array($baseLevel, [ProficiencyLevel::MASTER])
                ? ProficiencyLevel::EXPERT
                : $baseLevel;
        } else {
            return $baseLevel;
        }
    }

    /**
     * Calculate years of experience based on proficiency level
     */
    private function calculateYearsExperience(ProficiencyLevel $level): int
    {
        return match ($level) {
            ProficiencyLevel::BEGINNER => rand(0, 1),
            ProficiencyLevel::INTERMEDIATE => rand(2, 4),
            ProficiencyLevel::ADVANCED => rand(5, 8),
            ProficiencyLevel::EXPERT => rand(9, 12),
            ProficiencyLevel::MASTER => rand(13, 20),
        };
    }

    /**
     * Generate certification name
     */
    private function generateCertificationName(string $skillName): ?string
    {
        $certificationTypes = [
            'PHP' => ['Zend Certified Engineer', 'Laravel Certified Developer'],
            'JavaScript' => ['JavaScript Developer Certification', 'Node.js Certified Developer'],
            'Python' => ['Python Institute Certification', 'Django Certified Developer'],
            'Java' => ['Oracle Certified Professional', 'Spring Certified Developer'],
            'React' => ['React Developer Certification', 'Frontend Developer Certification'],
            'AWS' => ['AWS Certified Developer', 'AWS Solutions Architect'],
            'Agile' => ['Certified Scrum Master', 'PMI Agile Certified Practitioner'],
            'Scrum' => ['Certified Scrum Master', 'Certified Scrum Product Owner'],
            'PMP' => ['Project Management Professional'],
            'PRINCE2' => ['PRINCE2 Foundation', 'PRINCE2 Practitioner'],
        ];

        if (isset($certificationTypes[$skillName])) {
            return $this->faker->randomElement($certificationTypes[$skillName]);
        }

        // Generic certification names
        $genericCertifications = [
            'Professional Certification',
            'Advanced Certification',
            'Expert Certification',
            'Specialist Certification',
            'Master Certification',
        ];

        return $skillName.' '.$this->faker->randomElement($genericCertifications);
    }

    /**
     * Generate certification date
     */
    private function generateCertificationDate(int $yearsExperience): ?string
    {
        if ($yearsExperience === 0) {
            return null;
        }

        $maxYearsBack = min($yearsExperience, 3);

        return $this->faker->dateTimeBetween("-{$maxYearsBack} years", 'now')->format('Y-m-d');
    }

    /**
     * Generate certification expiry date
     */
    private function generateCertificationExpiry(): ?string
    {
        return $this->faker->dateTimeBetween('now', '+2 years')->format('Y-m-d');
    }

    /**
     * Generate skill description
     */
    private function generateSkillDescription(string $skillName, SkillCategory $category): string
    {
        $descriptions = [
            'PHP' => 'Proficient in PHP development with experience in modern frameworks and best practices.',
            'JavaScript' => 'Strong JavaScript skills including ES6+, modern frameworks, and frontend development.',
            'Python' => 'Experienced in Python development for web applications, data analysis, and automation.',
            'Java' => 'Skilled in Java development with enterprise frameworks and object-oriented programming.',
            'React' => 'Proficient in React development with modern hooks, state management, and component architecture.',
            'Leadership' => 'Demonstrated leadership abilities in team management and project coordination.',
            'Communication' => 'Excellent communication skills in both written and verbal forms.',
            'Problem Solving' => 'Strong analytical and problem-solving skills with creative solutions.',
            'English' => 'Fluent in English with professional business communication skills.',
            'Agile' => 'Experienced in Agile methodologies with practical application in software development.',
        ];

        return $descriptions[$skillName] ?? "Proficient in {$skillName} with practical experience in {$category->value}.";
    }

    /**
     * Generate keywords
     */
    private function generateKeywords(string $skillName, SkillCategory $category): array
    {
        $baseKeywords = [$skillName, $category->value];

        $additionalKeywords = [
            'technical' => ['development', 'programming', 'coding', 'software', 'web', 'mobile'],
            'soft_skills' => ['communication', 'leadership', 'teamwork', 'problem-solving', 'time-management'],
            'languages' => ['fluent', 'conversational', 'business', 'technical', 'native'],
            'tools' => ['software', 'application', 'platform', 'system', 'tool'],
            'methodologies' => ['agile', 'scrum', 'waterfall', 'lean', 'six-sigma'],
            'certifications' => ['certified', 'accredited', 'licensed', 'qualified', 'professional'],
            'other' => ['specialized', 'domain', 'industry', 'field', 'area'],
        ];

        if (isset($additionalKeywords[$category->value])) {
            $baseKeywords = array_merge($baseKeywords, $additionalKeywords[$category->value]);
        }

        return array_unique($baseKeywords);
    }

    /**
     * Generate tags
     */
    private function generateTags(SkillCategory $category): array
    {
        $tagMap = [
            'technical' => ['tech', 'development', 'programming', 'engineering'],
            'soft_skills' => ['interpersonal', 'communication', 'leadership'],
            'languages' => ['language', 'communication', 'international'],
            'tools' => ['software', 'application', 'platform'],
            'methodologies' => ['process', 'framework', 'methodology'],
            'certifications' => ['certified', 'accredited', 'professional'],
            'other' => ['specialized', 'domain', 'industry'],
        ];

        $tags = $tagMap[$category->value] ?? ['general'];

        // Add some random tags
        $randomTags = ['essential', 'advanced', 'core', 'specialized', 'emerging'];
        $tags = array_merge($tags, $this->faker->randomElements($randomTags, 2));

        return array_unique($tags);
    }

    /**
     * Ensure each employee has at least one primary skill
     */
    private function ensurePrimarySkills($employees): void
    {
        foreach ($employees as $employee) {
            $primarySkills = EmployeeSkill::where('employee_id', $employee->id)
                ->where('is_primary', true)
                ->count();

            if ($primarySkills === 0) {
                // Find a skill for this employee and make it primary
                $skill = EmployeeSkill::where('employee_id', $employee->id)
                    ->where('is_verified', true)
                    ->first();

                if ($skill) {
                    $skill->update(['is_primary' => true]);
                }
            }
        }
    }
}
