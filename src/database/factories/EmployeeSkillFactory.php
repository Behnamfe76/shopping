<?php

namespace Database\Factories;

use App\Enums\ProficiencyLevel;
use App\Enums\SkillCategory;
use App\Models\EmployeeSkill;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\EmployeeSkill>
 */
class EmployeeSkillFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = EmployeeSkill::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $skillCategories = SkillCategory::values();
        $proficiencyLevels = ProficiencyLevel::values();

        $skillName = $this->faker->randomElement([
            'PHP', 'JavaScript', 'Python', 'Java', 'C#', 'Ruby', 'Go', 'Rust',
            'React', 'Vue.js', 'Angular', 'Node.js', 'Laravel', 'Django', 'Spring',
            'MySQL', 'PostgreSQL', 'MongoDB', 'Redis', 'Elasticsearch',
            'Docker', 'Kubernetes', 'AWS', 'Azure', 'GCP',
            'Agile', 'Scrum', 'Kanban', 'DevOps', 'CI/CD',
            'Project Management', 'Leadership', 'Communication', 'Problem Solving',
            'English', 'Spanish', 'French', 'German', 'Chinese', 'Japanese',
            'Photoshop', 'Illustrator', 'Figma', 'Sketch', 'InDesign',
            'Excel', 'PowerPoint', 'Word', 'Power BI', 'Tableau',
        ]);

        $skillCategory = $this->faker->randomElement($skillCategories);
        $proficiencyLevel = $this->faker->randomElement($proficiencyLevels);
        $yearsExperience = $this->faker->numberBetween(0, 15);

        // Adjust proficiency level based on experience
        if ($yearsExperience < 2) {
            $proficiencyLevel = $this->faker->randomElement(['beginner', 'intermediate']);
        } elseif ($yearsExperience < 5) {
            $proficiencyLevel = $this->faker->randomElement(['intermediate', 'advanced']);
        } else {
            $proficiencyLevel = $this->faker->randomElement(['advanced', 'expert', 'master']);
        }

        $certificationRequired = $this->faker->boolean(20);
        $hasCertification = $certificationRequired && $this->faker->boolean(70);

        $data = [
            'skill_name' => $skillName,
            'skill_category' => $skillCategory,
            'proficiency_level' => $proficiencyLevel,
            'years_experience' => $yearsExperience,
            'certification_required' => $certificationRequired,
            'is_verified' => $this->faker->boolean(80),
            'is_active' => $this->faker->boolean(90),
            'is_primary' => $this->faker->boolean(15),
            'is_required' => $this->faker->boolean(30),
            'skill_description' => $this->faker->paragraph(2),
            'keywords' => $this->generateKeywords($skillName, $skillCategory),
            'tags' => $this->generateTags($skillCategory),
            'notes' => $this->faker->optional(0.3)->sentence(),
            'attachments' => $this->faker->optional(0.2)->randomElements([
                'certificate.pdf', 'portfolio.pdf', 'reference.pdf', 'assessment.pdf',
            ], $this->faker->numberBetween(1, 3)),
        ];

        // Add certification data if applicable
        if ($hasCertification) {
            $certificationDate = $this->faker->dateTimeBetween('-3 years', 'now');
            $data['certification_name'] = $this->generateCertificationName($skillName);
            $data['certification_date'] = $certificationDate;
            $data['certification_expiry'] = $this->faker->dateTimeBetween($certificationDate, '+2 years');
        }

        // Add verification data if verified
        if ($data['is_verified']) {
            $data['verified_at'] = $this->faker->dateTimeBetween('-1 year', 'now');
        }

        return $data;
    }

    /**
     * Generate keywords based on skill name and category
     */
    private function generateKeywords(string $skillName, string $category): array
    {
        $baseKeywords = [$skillName, $category];

        $additionalKeywords = [
            'technical' => ['development', 'programming', 'coding', 'software', 'web', 'mobile'],
            'soft_skills' => ['communication', 'leadership', 'teamwork', 'problem-solving', 'time-management'],
            'languages' => ['fluent', 'conversational', 'business', 'technical', 'native'],
            'tools' => ['software', 'application', 'platform', 'system', 'tool'],
            'methodologies' => ['agile', 'scrum', 'waterfall', 'lean', 'six-sigma'],
            'certifications' => ['certified', 'accredited', 'licensed', 'qualified', 'professional'],
            'other' => ['specialized', 'domain', 'industry', 'field', 'area'],
        ];

        if (isset($additionalKeywords[$category])) {
            $baseKeywords = array_merge($baseKeywords, $additionalKeywords[$category]);
        }

        // Add some random generic keywords
        $genericKeywords = ['expertise', 'proficiency', 'competency', 'capability', 'knowledge'];
        $baseKeywords = array_merge($baseKeywords, $this->faker->randomElements($genericKeywords, 2));

        return array_unique($baseKeywords);
    }

    /**
     * Generate tags based on skill category
     */
    private function generateTags(string $category): array
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

        $tags = $tagMap[$category] ?? ['general'];

        // Add some random tags
        $randomTags = ['essential', 'advanced', 'core', 'specialized', 'emerging'];
        $tags = array_merge($tags, $this->faker->randomElements($randomTags, 2));

        return array_unique($tags);
    }

    /**
     * Generate certification name based on skill
     */
    private function generateCertificationName(string $skillName): string
    {
        $certificationTypes = [
            'PHP' => ['Zend Certified Engineer', 'Laravel Certified Developer'],
            'JavaScript' => ['JavaScript Developer Certification', 'Node.js Certified Developer'],
            'Python' => ['Python Institute Certification', 'Django Certified Developer'],
            'Java' => ['Oracle Certified Professional', 'Spring Certified Developer'],
            'React' => ['React Developer Certification', 'Frontend Developer Certification'],
            'AWS' => ['AWS Certified Developer', 'AWS Solutions Architect'],
            'Agile' => ['Certified Scrum Master', 'PMI Agile Certified Practitioner'],
            'Project Management' => ['PMP Certification', 'PRINCE2 Certification'],
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
     * Indicate that the skill is technical
     */
    public function technical(): static
    {
        return $this->state(fn (array $attributes) => [
            'skill_category' => SkillCategory::TECHNICAL,
            'skill_name' => $this->faker->randomElement([
                'PHP', 'JavaScript', 'Python', 'Java', 'React', 'Vue.js', 'Node.js',
                'MySQL', 'PostgreSQL', 'Docker', 'AWS', 'Git', 'Linux',
            ]),
        ]);
    }

    /**
     * Indicate that the skill is a soft skill
     */
    public function softSkill(): static
    {
        return $this->state(fn (array $attributes) => [
            'skill_category' => SkillCategory::SOFT_SKILLS,
            'skill_name' => $this->faker->randomElement([
                'Leadership', 'Communication', 'Problem Solving', 'Teamwork',
                'Time Management', 'Critical Thinking', 'Adaptability', 'Creativity',
            ]),
        ]);
    }

    /**
     * Indicate that the skill is a language
     */
    public function language(): static
    {
        return $this->state(fn (array $attributes) => [
            'skill_category' => SkillCategory::LANGUAGES,
            'skill_name' => $this->faker->randomElement([
                'English', 'Spanish', 'French', 'German', 'Chinese', 'Japanese',
                'Portuguese', 'Italian', 'Russian', 'Arabic',
            ]),
        ]);
    }

    /**
     * Indicate that the skill is verified
     */
    public function verified(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_verified' => true,
            'verified_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
        ]);
    }

    /**
     * Indicate that the skill is unverified
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_verified' => false,
            'verified_at' => null,
        ]);
    }

    /**
     * Indicate that the skill is primary
     */
    public function primary(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_primary' => true,
        ]);
    }

    /**
     * Indicate that the skill is required
     */
    public function required(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_required' => true,
        ]);
    }

    /**
     * Indicate that the skill has certification
     */
    public function certified(): static
    {
        return $this->state(fn (array $attributes) => [
            'certification_required' => true,
            'certification_name' => $this->generateCertificationName($attributes['skill_name'] ?? 'Skill'),
            'certification_date' => $this->faker->dateTimeBetween('-2 years', '-6 months'),
            'certification_expiry' => $this->faker->dateTimeBetween('now', '+2 years'),
        ]);
    }

    /**
     * Indicate that the skill has expiring certification
     */
    public function expiringCertification(): static
    {
        return $this->state(fn (array $attributes) => [
            'certification_required' => true,
            'certification_name' => $this->generateCertificationName($attributes['skill_name'] ?? 'Skill'),
            'certification_date' => $this->faker->dateTimeBetween('-2 years', '-1 year'),
            'certification_expiry' => $this->faker->dateTimeBetween('now', '+30 days'),
        ]);
    }

    /**
     * Indicate that the skill has expired certification
     */
    public function expiredCertification(): static
    {
        return $this->state(fn (array $attributes) => [
            'certification_required' => true,
            'certification_name' => $this->generateCertificationName($attributes['skill_name'] ?? 'Skill'),
            'certification_date' => $this->faker->dateTimeBetween('-3 years', '-2 years'),
            'certification_expiry' => $this->faker->dateTimeBetween('-1 year', 'now'),
        ]);
    }

    /**
     * Indicate that the skill is for a beginner
     */
    public function beginner(): static
    {
        return $this->state(fn (array $attributes) => [
            'proficiency_level' => ProficiencyLevel::BEGINNER,
            'years_experience' => $this->faker->numberBetween(0, 1),
        ]);
    }

    /**
     * Indicate that the skill is for an intermediate user
     */
    public function intermediate(): static
    {
        return $this->state(fn (array $attributes) => [
            'proficiency_level' => ProficiencyLevel::INTERMEDIATE,
            'years_experience' => $this->faker->numberBetween(2, 4),
        ]);
    }

    /**
     * Indicate that the skill is for an advanced user
     */
    public function advanced(): static
    {
        return $this->state(fn (array $attributes) => [
            'proficiency_level' => ProficiencyLevel::ADVANCED,
            'years_experience' => $this->faker->numberBetween(5, 8),
        ]);
    }

    /**
     * Indicate that the skill is for an expert
     */
    public function expert(): static
    {
        return $this->state(fn (array $attributes) => [
            'proficiency_level' => ProficiencyLevel::EXPERT,
            'years_experience' => $this->faker->numberBetween(9, 12),
        ]);
    }

    /**
     * Indicate that the skill is for a master
     */
    public function master(): static
    {
        return $this->state(fn (array $attributes) => [
            'proficiency_level' => ProficiencyLevel::MASTER,
            'years_experience' => $this->faker->numberBetween(13, 20),
        ]);
    }

    /**
     * Indicate that the skill is active
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => true,
        ]);
    }

    /**
     * Indicate that the skill is inactive
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }
}
