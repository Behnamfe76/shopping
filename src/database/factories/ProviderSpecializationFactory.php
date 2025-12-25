<?php

namespace Fereydooni\Shopping\database\factories;

use Fereydooni\Shopping\App\Enums\ProficiencyLevel;
use Fereydooni\Shopping\App\Enums\SpecializationCategory;
use Fereydooni\Shopping\App\Enums\VerificationStatus;
use Fereydooni\Shopping\App\Models\Provider;
use Fereydooni\Shopping\App\Models\ProviderSpecialization;
use Fereydooni\Shopping\App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\Fereydooni\Shopping\App\Models\ProviderSpecialization>
 */
class ProviderSpecializationFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ProviderSpecialization::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'provider_id' => Provider::factory(),
            'specialization_name' => $this->faker->unique()->jobTitle(),
            'category' => $this->faker->randomElement(SpecializationCategory::cases()),
            'description' => $this->faker->paragraph(),
            'years_experience' => $this->faker->numberBetween(0, 25),
            'proficiency_level' => $this->faker->randomElement(ProficiencyLevel::cases()),
            'certifications' => $this->faker->optional(0.7)->randomElements([
                'AWS Certified Solutions Architect',
                'Microsoft Certified: Azure Developer Associate',
                'Google Cloud Professional Developer',
                'Certified Scrum Master (CSM)',
                'Project Management Professional (PMP)',
                'Certified Information Systems Security Professional (CISSP)',
                'Certified Public Accountant (CPA)',
                'Certified Financial Planner (CFP)',
                'Certified Legal Assistant (CLA)',
                'Registered Nurse (RN)',
                'Board Certified Physician',
                'Certified Teacher',
                'Certified Personal Trainer',
                'Certified Real Estate Agent',
                'Certified Insurance Agent',
            ], $this->faker->numberBetween(0, 3)),
            'is_primary' => false,
            'is_active' => true,
            'verification_status' => $this->faker->randomElement(VerificationStatus::cases()),
            'verified_at' => $this->faker->optional(0.6)->dateTimeBetween('-1 year', 'now'),
            'verified_by' => $this->faker->optional(0.6)->randomElement(User::pluck('id')->toArray()),
            'notes' => $this->faker->optional(0.3)->randomElements([
                'Excellent communication skills',
                'Strong problem-solving abilities',
                'Team player with leadership qualities',
                'Detail-oriented and organized',
                'Fast learner and adaptable',
                'Customer-focused approach',
                'Technical expertise in modern tools',
                'Proven track record of success',
                'Continuous learning mindset',
                'Quality-driven professional',
            ], $this->faker->numberBetween(0, 2)),
        ];
    }

    /**
     * Indicate that the specialization is primary.
     */
    public function primary(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_primary' => true,
        ]);
    }

    /**
     * Indicate that the specialization is active.
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => true,
        ]);
    }

    /**
     * Indicate that the specialization is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }

    /**
     * Indicate that the specialization is verified.
     */
    public function verified(): static
    {
        return $this->state(fn (array $attributes) => [
            'verification_status' => VerificationStatus::VERIFIED,
            'verified_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'verified_by' => User::factory(),
        ]);
    }

    /**
     * Indicate that the specialization is pending verification.
     */
    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'verification_status' => VerificationStatus::PENDING,
            'verified_at' => null,
            'verified_by' => null,
        ]);
    }

    /**
     * Indicate that the specialization is rejected.
     */
    public function rejected(): static
    {
        return $this->state(fn (array $attributes) => [
            'verification_status' => VerificationStatus::REJECTED,
            'verified_at' => null,
            'verified_by' => null,
            'notes' => array_merge($attributes['notes'] ?? [], [
                'Rejection reason: '.$this->faker->sentence(),
            ]),
        ]);
    }

    /**
     * Indicate that the specialization is unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'verification_status' => VerificationStatus::UNVERIFIED,
            'verified_at' => null,
            'verified_by' => null,
        ]);
    }

    /**
     * Indicate that the specialization has beginner proficiency.
     */
    public function beginner(): static
    {
        return $this->state(fn (array $attributes) => [
            'proficiency_level' => ProficiencyLevel::BEGINNER,
            'years_experience' => $this->faker->numberBetween(0, 2),
        ]);
    }

    /**
     * Indicate that the specialization has intermediate proficiency.
     */
    public function intermediate(): static
    {
        return $this->state(fn (array $attributes) => [
            'proficiency_level' => ProficiencyLevel::INTERMEDIATE,
            'years_experience' => $this->faker->numberBetween(2, 5),
        ]);
    }

    /**
     * Indicate that the specialization has advanced proficiency.
     */
    public function advanced(): static
    {
        return $this->state(fn (array $attributes) => [
            'proficiency_level' => ProficiencyLevel::ADVANCED,
            'years_experience' => $this->faker->numberBetween(5, 10),
        ]);
    }

    /**
     * Indicate that the specialization has expert proficiency.
     */
    public function expert(): static
    {
        return $this->state(fn (array $attributes) => [
            'proficiency_level' => ProficiencyLevel::EXPERT,
            'years_experience' => $this->faker->numberBetween(10, 20),
        ]);
    }

    /**
     * Indicate that the specialization has master proficiency.
     */
    public function master(): static
    {
        return $this->state(fn (array $attributes) => [
            'proficiency_level' => ProficiencyLevel::MASTER,
            'years_experience' => $this->faker->numberBetween(20, 30),
        ]);
    }

    /**
     * Indicate that the specialization is in the medical category.
     */
    public function medical(): static
    {
        return $this->state(fn (array $attributes) => [
            'category' => SpecializationCategory::MEDICAL,
            'specialization_name' => $this->faker->randomElement([
                'Cardiology', 'Neurology', 'Oncology', 'Pediatrics', 'Surgery',
                'Emergency Medicine', 'Family Medicine', 'Internal Medicine',
                'Psychiatry', 'Radiology', 'Anesthesiology', 'Dermatology',
            ]),
        ]);
    }

    /**
     * Indicate that the specialization is in the legal category.
     */
    public function legal(): static
    {
        return $this->state(fn (array $attributes) => [
            'category' => SpecializationCategory::LEGAL,
            'specialization_name' => $this->faker->randomElement([
                'Corporate Law', 'Criminal Law', 'Family Law', 'Real Estate Law',
                'Tax Law', 'Intellectual Property Law', 'Employment Law',
                'Environmental Law', 'Immigration Law', 'Bankruptcy Law',
            ]),
        ]);
    }

    /**
     * Indicate that the specialization is in the technical category.
     */
    public function technical(): static
    {
        return $this->state(fn (array $attributes) => [
            'category' => SpecializationCategory::TECHNICAL,
            'specialization_name' => $this->faker->randomElement([
                'Software Development', 'Data Science', 'DevOps', 'Cybersecurity',
                'Cloud Computing', 'Machine Learning', 'Web Development',
                'Mobile Development', 'Database Administration', 'Network Engineering',
            ]),
        ]);
    }

    /**
     * Indicate that the specialization is in the financial category.
     */
    public function financial(): static
    {
        return $this->state(fn (array $attributes) => [
            'category' => SpecializationCategory::FINANCIAL,
            'specialization_name' => $this->faker->randomElement([
                'Investment Banking', 'Financial Planning', 'Accounting',
                'Risk Management', 'Corporate Finance', 'Tax Consulting',
                'Auditing', 'Insurance', 'Real Estate Investment', 'Wealth Management',
            ]),
        ]);
    }

    /**
     * Indicate that the specialization is in the educational category.
     */
    public function educational(): static
    {
        return $this->state(fn (array $attributes) => [
            'category' => SpecializationCategory::EDUCATIONAL,
            'specialization_name' => $this->faker->randomElement([
                'Mathematics', 'Science', 'Language Arts', 'History',
                'Physical Education', 'Music', 'Art', 'Special Education',
                'Curriculum Development', 'Educational Technology',
            ]),
        ]);
    }

    /**
     * Indicate that the specialization is in the consulting category.
     */
    public function consulting(): static
    {
        return $this->state(fn (array $attributes) => [
            'category' => SpecializationCategory::CONSULTING,
            'specialization_name' => $this->faker->randomElement([
                'Business Strategy', 'Change Management', 'Process Improvement',
                'Digital Transformation', 'Organizational Development',
                'Project Management', 'Human Resources', 'Marketing Strategy',
            ]),
        ]);
    }

    /**
     * Indicate that the specialization has high experience.
     */
    public function experienced(): static
    {
        return $this->state(fn (array $attributes) => [
            'years_experience' => $this->faker->numberBetween(10, 25),
            'proficiency_level' => $this->faker->randomElement([
                ProficiencyLevel::ADVANCED,
                ProficiencyLevel::EXPERT,
                ProficiencyLevel::MASTER,
            ]),
        ]);
    }

    /**
     * Indicate that the specialization has low experience.
     */
    public function inexperienced(): static
    {
        return $this->state(fn (array $attributes) => [
            'years_experience' => $this->faker->numberBetween(0, 3),
            'proficiency_level' => $this->faker->randomElement([
                ProficiencyLevel::BEGINNER,
                ProficiencyLevel::INTERMEDIATE,
            ]),
        ]);
    }

    /**
     * Indicate that the specialization has certifications.
     */
    public function certified(): static
    {
        return $this->state(fn (array $attributes) => [
            'certifications' => $this->faker->randomElements([
                'AWS Certified Solutions Architect',
                'Microsoft Certified: Azure Developer Associate',
                'Google Cloud Professional Developer',
                'Certified Scrum Master (CSM)',
                'Project Management Professional (PMP)',
                'Certified Information Systems Security Professional (CISSP)',
                'Certified Public Accountant (CPA)',
                'Certified Financial Planner (CFP)',
                'Certified Legal Assistant (CLA)',
                'Registered Nurse (RN)',
                'Board Certified Physician',
                'Certified Teacher',
                'Certified Personal Trainer',
                'Certified Real Estate Agent',
                'Certified Insurance Agent',
            ], $this->faker->numberBetween(1, 3)),
        ]);
    }

    /**
     * Indicate that the specialization has no certifications.
     */
    public function uncertified(): static
    {
        return $this->state(fn (array $attributes) => [
            'certifications' => null,
        ]);
    }
}
