<?php

namespace Fereydooni\Shopping\database\seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Fereydooni\Shopping\App\Models\ProviderSpecialization;
use Fereydooni\Shopping\App\Models\Provider;
use Fereydooni\Shopping\App\Models\User;
use Fereydooni\Shopping\App\Enums\SpecializationCategory;
use Fereydooni\Shopping\App\Enums\ProficiencyLevel;
use Fereydooni\Shopping\App\Enums\VerificationStatus;

class ProviderSpecializationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Check if we have providers to work with
        $providers = Provider::all();
        if ($providers->isEmpty()) {
            $this->command->warn('No providers found. Skipping specialization seeding.');
            return;
        }

        // Check if we have users for verification
        $users = User::all();
        if ($users->isEmpty()) {
            $this->command->warn('No users found. Skipping specialization seeding.');
            return;
        }

        // Create sample specializations for each provider
        foreach ($providers as $provider) {
            $this->createProviderSpecializations($provider, $users);
        }

        $this->command->info('Provider specializations seeded successfully!');
    }

    /**
     * Create specializations for a specific provider.
     */
    protected function createProviderSpecializations(Provider $provider, $users): void
    {
        // Determine how many specializations to create (1-4 per provider)
        $specializationCount = rand(1, 4);

        // Ensure at least one specialization is primary
        $hasPrimary = false;

        for ($i = 0; $i < $specializationCount; $i++) {
            $isPrimary = !$hasPrimary && ($i === 0 || rand(0, 1));
            if ($isPrimary) {
                $hasPrimary = true;
            }

            $specialization = $this->createSpecialization($provider, $users, $isPrimary);

            // Create additional specializations with different characteristics
            if ($i > 0) {
                $this->createAdditionalSpecializations($provider, $users);
                break; // Only create additional ones for the first few providers
            }
        }
    }

    /**
     * Create a single specialization.
     */
    protected function createSpecialization(Provider $provider, $users, bool $isPrimary = false): ProviderSpecialization
    {
        $categories = SpecializationCategory::cases();
        $proficiencyLevels = ProficiencyLevel::cases();
        $verificationStatuses = VerificationStatus::cases();

        $specialization = ProviderSpecialization::create([
            'provider_id' => $provider->id,
            'specialization_name' => $this->getRandomSpecializationName(),
            'category' => $categories[array_rand($categories)],
            'description' => $this->getRandomDescription(),
            'years_experience' => rand(0, 25),
            'proficiency_level' => $proficiencyLevels[array_rand($proficiencyLevels)],
            'certifications' => $this->getRandomCertifications(),
            'is_primary' => $isPrimary,
            'is_active' => true,
            'verification_status' => $verificationStatuses[array_rand($verificationStatuses)],
            'verified_at' => $this->getRandomVerifiedAt(),
            'verified_by' => $this->getRandomVerifiedBy($users),
            'notes' => $this->getRandomNotes(),
        ]);

        return $specialization;
    }

    /**
     * Create additional specializations with varied characteristics.
     */
    protected function createAdditionalSpecializations(Provider $provider, $users): void
    {
        // Create a technical specialization
        ProviderSpecialization::create([
            'provider_id' => $provider->id,
            'specialization_name' => 'Software Development',
            'category' => SpecializationCategory::TECHNICAL,
            'description' => 'Full-stack software development with modern technologies.',
            'years_experience' => rand(3, 15),
            'proficiency_level' => ProficiencyLevel::EXPERT,
            'certifications' => ['AWS Certified Developer', 'Microsoft Certified: Azure Developer'],
            'is_primary' => false,
            'is_active' => true,
            'verification_status' => VerificationStatus::VERIFIED,
            'verified_at' => now()->subDays(rand(1, 30)),
            'verified_by' => $users->random()->id,
            'notes' => ['Strong problem-solving skills', 'Excellent communication'],
        ]);

        // Create a financial specialization
        ProviderSpecialization::create([
            'provider_id' => $provider->id,
            'specialization_name' => 'Financial Planning',
            'category' => SpecializationCategory::FINANCIAL,
            'description' => 'Comprehensive financial planning and investment advisory services.',
            'years_experience' => rand(5, 20),
            'proficiency_level' => ProficiencyLevel::MASTER,
            'certifications' => ['Certified Financial Planner (CFP)', 'Chartered Financial Analyst (CFA)'],
            'is_primary' => false,
            'is_active' => true,
            'verification_status' => VerificationStatus::VERIFIED,
            'verified_at' => now()->subDays(rand(1, 30)),
            'verified_by' => $users->random()->id,
            'notes' => ['Expert in retirement planning', 'Tax optimization specialist'],
        ]);
    }

    /**
     * Get random specialization name.
     */
    protected function getRandomSpecializationName(): string
    {
        $names = [
            'Web Development', 'Mobile App Development', 'Data Science', 'Machine Learning',
            'Cybersecurity', 'Cloud Computing', 'DevOps Engineering', 'UI/UX Design',
            'Project Management', 'Business Analysis', 'Digital Marketing', 'Content Creation',
            'Graphic Design', 'Video Production', 'Photography', 'Consulting',
            'Training & Education', 'Research & Development', 'Quality Assurance', 'Technical Writing'
        ];

        return $names[array_rand($names)];
    }

    /**
     * Get random description.
     */
    protected function getRandomDescription(): string
    {
        $descriptions = [
            'Comprehensive expertise in modern development practices and technologies.',
            'Specialized knowledge in cutting-edge industry solutions and best practices.',
            'Proven track record of delivering high-quality results in complex projects.',
            'Deep understanding of industry standards and emerging trends.',
            'Extensive experience in solving challenging technical and business problems.',
            'Skilled professional with strong analytical and problem-solving abilities.',
            'Expert in optimizing processes and improving efficiency.',
            'Dedicated professional committed to continuous learning and improvement.'
        ];

        return $descriptions[array_rand($descriptions)];
    }

    /**
     * Get random certifications.
     */
    protected function getRandomCertifications(): ?array
    {
        $certifications = [
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
            'Certified Insurance Agent'
        ];

        // 70% chance of having certifications
        if (rand(1, 10) <= 7) {
            $count = rand(1, 3);
            return array_rand(array_flip($certifications), $count);
        }

        return null;
    }

    /**
     * Get random verification date.
     */
    protected function getRandomVerifiedAt(): ?string
    {
        // 60% chance of being verified
        if (rand(1, 10) <= 6) {
            return now()->subDays(rand(1, 365))->toDateTimeString();
        }

        return null;
    }

    /**
     * Get random verifier.
     */
    protected function getRandomVerifiedBy($users): ?int
    {
        // Only return a user ID if there are users available
        if ($users->isNotEmpty()) {
            return $users->random()->id;
        }

        return null;
    }

    /**
     * Get random notes.
     */
    protected function getRandomNotes(): ?array
    {
        $notes = [
            'Excellent communication skills',
            'Strong problem-solving abilities',
            'Team player with leadership qualities',
            'Detail-oriented and organized',
            'Fast learner and adaptable',
            'Customer-focused approach',
            'Technical expertise in modern tools',
            'Proven track record of success',
            'Continuous learning mindset',
            'Quality-driven professional'
        ];

        // 30% chance of having notes
        if (rand(1, 10) <= 3) {
            $count = rand(1, 2);
            return array_rand(array_flip($notes), $count);
        }

        return null;
    }
}
