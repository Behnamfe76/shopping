<?php

namespace Fereydooni\Shopping\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Fereydooni\Shopping\App\Models\ProviderCertification;
use Fereydooni\Shopping\App\Models\Provider;
use Fereydooni\Shopping\App\Models\User;
use Fereydooni\Shopping\App\Enums\CertificationCategory;
use Fereydooni\Shopping\App\Enums\CertificationStatus;
use Fereydooni\Shopping\App\Enums\VerificationStatus;
use Carbon\Carbon;

class ProviderCertificationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Seeding ProviderCertification data...');

        // Get existing providers and users
        $providers = Provider::all();
        $users = User::all();

        if ($providers->isEmpty()) {
            $this->command->warn('No providers found. Creating sample providers first...');
            $providers = Provider::factory(10)->create();
        }

        if ($users->isEmpty()) {
            $this->command->warn('No users found. Creating sample users first...');
            $users = User::factory(5)->create();
        }

        // Sample certification data
        $certifications = [
            // Professional Certifications
            [
                'category' => CertificationCategory::PROFESSIONAL,
                'certification_name' => 'Professional Engineer (PE)',
                'issuing_organization' => 'National Council of Examiners for Engineering and Surveying',
                'description' => 'Professional engineering license for civil, mechanical, electrical, and other engineering disciplines.',
                'is_recurring' => true,
                'renewal_period' => 24,
                'renewal_requirements' => ['Complete continuing education hours', 'Maintain professional development units', 'Submit renewal application'],
            ],
            [
                'category' => CertificationCategory::PROFESSIONAL,
                'certification_name' => 'Project Management Professional (PMP)',
                'issuing_organization' => 'Project Management Institute',
                'description' => 'Internationally recognized project management certification.',
                'is_recurring' => true,
                'renewal_period' => 36,
                'renewal_requirements' => ['Earn 60 professional development units', 'Complete continuing education', 'Submit renewal application'],
            ],
            [
                'category' => CertificationCategory::PROFESSIONAL,
                'certification_name' => 'Certified Public Accountant (CPA)',
                'issuing_organization' => 'American Institute of Certified Public Accountants',
                'description' => 'Professional accounting certification for public accountants.',
                'is_recurring' => true,
                'renewal_period' => 24,
                'renewal_requirements' => ['Complete continuing professional education', 'Maintain ethics compliance', 'Submit renewal application'],
            ],

            // Technical Certifications
            [
                'category' => CertificationCategory::TECHNICAL,
                'certification_name' => 'AWS Certified Solutions Architect',
                'issuing_organization' => 'Amazon Web Services',
                'description' => 'Cloud computing certification for AWS architecture and design.',
                'is_recurring' => true,
                'renewal_period' => 36,
                'renewal_requirements' => ['Pass recertification exam', 'Complete hands-on projects', 'Maintain active status'],
            ],
            [
                'category' => CertificationCategory::TECHNICAL,
                'certification_name' => 'Cisco Certified Network Associate (CCNA)',
                'issuing_organization' => 'Cisco Systems',
                'description' => 'Networking certification for Cisco network technologies.',
                'is_recurring' => true,
                'renewal_period' => 36,
                'renewal_period' => 36,
                'renewal_requirements' => ['Pass recertification exam', 'Complete continuing education', 'Maintain active status'],
            ],

            // Safety Certifications
            [
                'category' => CertificationCategory::SAFETY,
                'certification_name' => 'OSHA 30-Hour Construction Safety',
                'issuing_organization' => 'Occupational Safety and Health Administration',
                'description' => 'Construction safety training certification for supervisors and workers.',
                'is_recurring' => true,
                'renewal_period' => 60,
                'renewal_requirements' => ['Complete refresher training', 'Pass safety assessment', 'Submit renewal documentation'],
            ],
            [
                'category' => CertificationCategory::SAFETY,
                'certification_name' => 'First Aid and CPR Certification',
                'issuing_organization' => 'American Red Cross',
                'description' => 'Emergency response and first aid certification.',
                'is_recurring' => true,
                'renewal_period' => 24,
                'renewal_requirements' => ['Complete refresher course', 'Pass practical examination', 'Maintain certification'],
            ],

            // Compliance Certifications
            [
                'category' => CertificationCategory::COMPLIANCE,
                'certification_name' => 'ISO 9001:2015 Quality Management',
                'issuing_organization' => 'International Organization for Standardization',
                'description' => 'Quality management system certification.',
                'is_recurring' => true,
                'renewal_period' => 36,
                'renewal_requirements' => ['Complete annual compliance training', 'Pass compliance assessment', 'Submit compliance reports'],
            ],
            [
                'category' => CertificationCategory::COMPLIANCE,
                'certification_name' => 'SOC 2 Type II Compliance',
                'issuing_organization' => 'American Institute of Certified Public Accountants',
                'description' => 'Service Organization Control 2 compliance certification.',
                'is_recurring' => true,
                'renewal_period' => 12,
                'renewal_requirements' => ['Complete annual compliance training', 'Pass compliance assessment', 'Submit compliance reports'],
            ],

            // Educational Certifications
            [
                'category' => CertificationCategory::EDUCATIONAL,
                'certification_name' => 'Master of Business Administration (MBA)',
                'issuing_organization' => 'Accredited University',
                'description' => 'Graduate degree in business administration.',
                'is_recurring' => false,
                'renewal_period' => null,
                'renewal_requirements' => null,
            ],
            [
                'category' => CertificationCategory::EDUCATIONAL,
                'certification_name' => 'Professional Development Certificate',
                'issuing_organization' => 'Professional Development Institute',
                'description' => 'Professional development and continuing education certification.',
                'is_recurring' => true,
                'renewal_period' => 24,
                'renewal_requirements' => ['Complete continuing education units', 'Submit progress reports', 'Maintain active participation'],
            ],

            // Industry-Specific Certifications
            [
                'category' => CertificationCategory::INDUSTRY_SPECIFIC,
                'certification_name' => 'API 510 Pressure Vessel Inspector',
                'issuing_organization' => 'American Petroleum Institute',
                'description' => 'Pressure vessel inspection certification for the petroleum industry.',
                'is_recurring' => true,
                'renewal_period' => 60,
                'renewal_requirements' => ['Complete industry-specific training', 'Pass technical assessments', 'Complete field inspections'],
            ],
            [
                'category' => CertificationCategory::INDUSTRY_SPECIFIC,
                'certification_name' => 'AWS Certified Welding Inspector (CWI)',
                'issuing_organization' => 'American Welding Society',
                'description' => 'Welding inspection certification for the manufacturing industry.',
                'is_recurring' => true,
                'renewal_period' => 60,
                'renewal_requirements' => ['Complete industry-specific training', 'Pass technical assessments', 'Complete practical examinations'],
            ],
        ];

        $createdCount = 0;
        $providersPerCertification = max(1, floor($providers->count() / count($certifications)));

        foreach ($certifications as $certData) {
            // Create multiple instances of each certification for different providers
            for ($i = 0; $i < $providersPerCertification; $i++) {
                $provider = $providers->random();

                // Generate realistic dates
                $issueDate = Carbon::now()->subMonths(rand(1, 60));
                $isRecurring = $certData['is_recurring'];
                $expiryDate = $isRecurring ? $issueDate->copy()->addMonths($certData['renewal_period']) : null;

                // Determine status based on dates
                $status = CertificationStatus::ACTIVE;
                if ($expiryDate && $expiryDate->isPast()) {
                    $status = CertificationStatus::EXPIRED;
                }

                // Determine verification status
                $verificationStatus = $this->getRandomVerificationStatus();
                $verifiedAt = null;
                $verifiedBy = null;

                if ($verificationStatus === VerificationStatus::VERIFIED) {
                    $verifiedAt = $issueDate->copy()->addDays(rand(1, 30));
                    $verifiedBy = $users->random()->id;
                }

                // Generate certification number
                $certificationNumber = $this->generateCertificationNumber($certData['category'], $issueDate->year);

                try {
                    ProviderCertification::create([
                        'provider_id' => $provider->id,
                        'certification_name' => $certData['certification_name'],
                        'certification_number' => $certificationNumber,
                        'issuing_organization' => $certData['issuing_organization'],
                        'category' => $certData['category'],
                        'description' => $certData['description'],
                        'issue_date' => $issueDate,
                        'expiry_date' => $expiryDate,
                        'renewal_date' => $this->getRandomRenewalDate($issueDate, $expiryDate),
                        'status' => $status,
                        'verification_status' => $verificationStatus,
                        'verification_url' => $this->getRandomVerificationUrl(),
                        'attachment_path' => $this->getRandomAttachmentPath(),
                        'credits_earned' => rand(1, 50),
                        'is_recurring' => $isRecurring,
                        'renewal_period' => $certData['renewal_period'],
                        'renewal_requirements' => $certData['renewal_requirements'],
                        'verified_at' => $verifiedAt,
                        'verified_by' => $verifiedBy,
                        'notes' => $this->getRandomNotes($status, $verificationStatus),
                    ]);

                    $createdCount++;

                } catch (\Exception $e) {
                    $this->command->error("Failed to create certification: {$e->getMessage()}");
                }
            }
        }

        // Create additional random certifications for variety
        $additionalCount = rand(20, 50);
        for ($i = 0; $i < $additionalCount; $i++) {
            try {
                $provider = $providers->random();
                $category = CertificationCategory::cases()[array_rand(CertificationCategory::cases())];

                $issueDate = Carbon::now()->subMonths(rand(1, 60));
                $isRecurring = rand(0, 1);
                $expiryDate = $isRecurring ? $issueDate->copy()->addMonths(rand(12, 60)) : null;

                $status = $expiryDate && $expiryDate->isPast() ? CertificationStatus::EXPIRED : CertificationStatus::ACTIVE;
                $verificationStatus = $this->getRandomVerificationStatus();

                $verifiedAt = null;
                $verifiedBy = null;

                if ($verificationStatus === VerificationStatus::VERIFIED) {
                    $verifiedAt = $issueDate->copy()->addDays(rand(1, 30));
                    $verifiedBy = $users->random()->id;
                }

                ProviderCertification::create([
                    'provider_id' => $provider->id,
                    'certification_name' => $this->getRandomCertificationName($category),
                    'certification_number' => $this->generateCertificationNumber($category, $issueDate->year),
                    'issuing_organization' => $this->getRandomIssuingOrganization($category),
                    'category' => $category,
                    'description' => $this->getRandomDescription(),
                    'issue_date' => $issueDate,
                    'expiry_date' => $expiryDate,
                    'renewal_date' => $this->getRandomRenewalDate($issueDate, $expiryDate),
                    'status' => $status,
                    'verification_status' => $verificationStatus,
                    'verification_url' => $this->getRandomVerificationUrl(),
                    'attachment_path' => $this->getRandomAttachmentPath(),
                    'credits_earned' => rand(1, 50),
                    'is_recurring' => $isRecurring,
                    'renewal_period' => $isRecurring ? rand(12, 60) : null,
                    'renewal_requirements' => $isRecurring ? $this->getRandomRenewalRequirements($category) : null,
                    'verified_at' => $verifiedAt,
                    'verified_by' => $verifiedBy,
                    'notes' => $this->getRandomNotes($status, $verificationStatus),
                ]);

                $createdCount++;

            } catch (\Exception $e) {
                $this->command->error("Failed to create additional certification: {$e->getMessage()}");
            }
        }

        $this->command->info("Successfully created {$createdCount} provider certifications.");

        // Display statistics
        $this->displayStatistics();
    }

    /**
     * Get random verification status with weighted distribution.
     */
    protected function getRandomVerificationStatus(): VerificationStatus
    {
        $weights = [
            VerificationStatus::VERIFIED => 60,      // 60% verified
            VerificationStatus::UNVERIFIED => 25,    // 25% unverified
            VerificationStatus::PENDING => 10,       // 10% pending
            VerificationStatus::REJECTED => 3,       // 3% rejected
            VerificationStatus::REQUIRES_UPDATE => 2, // 2% requires update
        ];

        $random = rand(1, 100);
        $cumulative = 0;

        foreach ($weights as $status => $weight) {
            $cumulative += $weight;
            if ($random <= $cumulative) {
                return $status;
            }
        }

        return VerificationStatus::UNVERIFIED;
    }

    /**
     * Generate certification number based on category and year.
     */
    protected function generateCertificationNumber(CertificationCategory $category, int $year): string
    {
        $prefixes = [
            CertificationCategory::PROFESSIONAL => ['PE', 'CPA', 'PMP', 'CISA', 'CFP'],
            CertificationCategory::TECHNICAL => ['AWS', 'MS', 'GCP', 'CCNA', 'COMPTIA'],
            CertificationCategory::SAFETY => ['OSHA', 'FA', 'HAZ', 'CSE', 'FP'],
            CertificationCategory::COMPLIANCE => ['ISO', 'SOC', 'GDPR', 'HIPAA', 'PCI'],
            CertificationCategory::EDUCATIONAL => ['BS', 'MBA', 'PHD', 'AD', 'PD'],
            CertificationCategory::INDUSTRY_SPECIFIC => ['API', 'ASME', 'NACE', 'AWS', 'ASNT'],
            CertificationCategory::OTHER => ['CS', 'SM', 'SCM', 'QA', 'RM'],
        ];

        $prefix = $prefixes[$category][array_rand($prefixes[$category])];
        $number = rand(1000, 99999);

        return "{$prefix}-{$year}-{$number}";
    }

    /**
     * Get random renewal date.
     */
    protected function getRandomRenewalDate(Carbon $issueDate, ?Carbon $expiryDate): ?Carbon
    {
        if (!$expiryDate) {
            return null;
        }

        if (rand(0, 1)) {
            return $issueDate->copy()->addMonths(rand(1, $issueDate->diffInMonths($expiryDate)));
        }

        return null;
    }

    /**
     * Get random verification URL.
     */
    protected function getRandomVerificationUrl(): ?string
    {
        if (rand(0, 1)) {
            $domains = ['verify.example.com', 'cert.example.org', 'check.example.net'];
            $domain = $domains[array_rand($domains)];
            $id = rand(100000, 999999);
            return "https://{$domain}/verify/{$id}";
        }

        return null;
    }

    /**
     * Get random attachment path.
     */
    protected function getRandomAttachmentPath(): ?string
    {
        if (rand(0, 1)) {
            $extensions = ['pdf', 'jpg', 'png', 'docx'];
            $extension = $extensions[array_rand($extensions)];
            $filename = 'cert_' . rand(1000, 9999) . '.' . $extension;
            return "certifications/{$filename}";
        }

        return null;
    }

    /**
     * Get random notes based on status and verification status.
     */
    protected function getRandomNotes(CertificationStatus $status, VerificationStatus $verificationStatus): ?array
    {
        $notes = [];

        if ($status === CertificationStatus::SUSPENDED) {
            $notes[] = 'Suspended due to compliance issues';
        }

        if ($status === CertificationStatus::REVOKED) {
            $notes[] = 'Revoked due to policy violations';
        }

        if ($verificationStatus === VerificationStatus::REJECTED) {
            $notes[] = 'Verification rejected - insufficient documentation';
        }

        if ($verificationStatus === VerificationStatus::REQUIRES_UPDATE) {
            $notes[] = 'Additional documentation required for verification';
        }

        if (rand(0, 1) && empty($notes)) {
            $generalNotes = [
                'Certification in good standing',
                'All requirements met',
                'Regular monitoring required',
                'Annual review scheduled',
            ];
            $notes[] = $generalNotes[array_rand($generalNotes)];
        }

        return empty($notes) ? null : $notes;
    }

    /**
     * Get random certification name.
     */
    protected function getRandomCertificationName(CertificationCategory $category): string
    {
        $names = [
            CertificationCategory::PROFESSIONAL => 'Professional Certification',
            CertificationCategory::TECHNICAL => 'Technical Certification',
            CertificationCategory::SAFETY => 'Safety Certification',
            CertificationCategory::COMPLIANCE => 'Compliance Certification',
            CertificationCategory::EDUCATIONAL => 'Educational Certification',
            CertificationCategory::INDUSTRY_SPECIFIC => 'Industry Certification',
            CertificationCategory::OTHER => 'General Certification',
        ];

        return $names[$category] . ' ' . rand(100, 999);
    }

    /**
     * Get random issuing organization.
     */
    protected function getRandomIssuingOrganization(CertificationCategory $category): string
    {
        $organizations = [
            CertificationCategory::PROFESSIONAL => 'Professional Institute',
            CertificationCategory::TECHNICAL => 'Technical Institute',
            CertificationCategory::SAFETY => 'Safety Council',
            CertificationCategory::COMPLIANCE => 'Compliance Institute',
            CertificationCategory::EDUCATIONAL => 'Educational Institute',
            CertificationCategory::INDUSTRY_SPECIFIC => 'Industry Association',
            CertificationCategory::OTHER => 'Certification Board',
        ];

        return $organizations[$category];
    }

    /**
     * Get random description.
     */
    protected function getRandomDescription(): string
    {
        $descriptions = [
            'Professional certification demonstrating expertise in the field.',
            'Technical certification for specialized skills and knowledge.',
            'Safety certification ensuring workplace safety compliance.',
            'Compliance certification meeting industry standards.',
            'Educational certification for academic achievements.',
            'Industry-specific certification for specialized sectors.',
            'General certification for professional development.',
        ];

        return $descriptions[array_rand($descriptions)];
    }

    /**
     * Get random renewal requirements.
     */
    protected function getRandomRenewalRequirements(CertificationCategory $category): array
    {
        $requirements = [
            'Complete continuing education',
            'Pass renewal examination',
            'Submit renewal application',
            'Pay renewal fees',
            'Maintain active status',
            'Complete required training',
            'Submit progress reports',
        ];

        $count = rand(3, 5);
        return array_rand(array_flip($requirements), $count);
    }

    /**
     * Display seeding statistics.
     */
    protected function displayStatistics(): void
    {
        $total = ProviderCertification::count();
        $byCategory = ProviderCertification::selectRaw('category, count(*) as count')
            ->groupBy('category')
            ->pluck('count', 'category')
            ->toArray();

        $byStatus = ProviderCertification::selectRaw('status, count(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        $byVerificationStatus = ProviderCertification::selectRaw('verification_status, count(*) as count')
            ->groupBy('verification_status')
            ->pluck('count', 'verification_status')
            ->toArray();

        $this->command->info("\nProviderCertification Seeding Statistics:");
        $this->command->info("Total certifications: {$total}");

        $this->command->info("\nBy Category:");
        foreach ($byCategory as $category => $count) {
            $this->command->info("  {$category}: {$count}");
        }

        $this->command->info("\nBy Status:");
        foreach ($byStatus as $status => $count) {
            $this->command->info("  {$status}: {$count}");
        }

        $this->command->info("\nBy Verification Status:");
        foreach ($byVerificationStatus as $verificationStatus => $count) {
            $this->command->info("  {$verificationStatus}: {$count}");
        }
    }
};
