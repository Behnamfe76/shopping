<?php

namespace Fereydooni\Shopping\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Fereydooni\Shopping\App\Models\ProviderCertification;
use Fereydooni\Shopping\App\Models\Provider;
use Fereydooni\Shopping\App\Models\User;
use Fereydooni\Shopping\App\Enums\CertificationCategory;
use Fereydooni\Shopping\App\Enums\CertificationStatus;
use Fereydooni\Shopping\App\Enums\VerificationStatus;
use Carbon\Carbon;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\Fereydooni\Shopping\App\Models\ProviderCertification>
 */
class ProviderCertificationFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ProviderCertification::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $issueDate = $this->faker->dateTimeBetween('-5 years', 'now');
        $isRecurring = $this->faker->boolean(70); // 70% chance of being recurring
        $expiryDate = $isRecurring ? Carbon::parse($issueDate)->addMonths($this->faker->numberBetween(12, 60)) : null;

        $category = $this->faker->randomElement(CertificationCategory::cases());
        $status = $this->faker->randomElement(CertificationStatus::cases());
        $verificationStatus = $this->faker->randomElement(VerificationStatus::cases());

        // Ensure status and verification status make sense together
        if ($status === CertificationStatus::EXPIRED && $expiryDate && $expiryDate->isPast()) {
            $status = CertificationStatus::EXPIRED;
        } elseif ($status === CertificationStatus::ACTIVE && $expiryDate && $expiryDate->isPast()) {
            $status = CertificationStatus::EXPIRED;
        }

        if ($verificationStatus === VerificationStatus::VERIFIED) {
            $verifiedAt = $this->faker->dateTimeBetween($issueDate, 'now');
            $verifiedBy = User::inRandomOrder()->first()?->id;
        } else {
            $verifiedAt = null;
            $verifiedBy = null;
        }

        return [
            'provider_id' => Provider::factory(),
            'certification_name' => $this->generateCertificationName($category),
            'certification_number' => $this->generateCertificationNumber($category),
            'issuing_organization' => $this->generateIssuingOrganization($category),
            'category' => $category,
            'description' => $this->faker->optional(0.8)->paragraph(3),
            'issue_date' => $issueDate,
            'expiry_date' => $expiryDate,
            'renewal_date' => $this->faker->optional(0.3)->dateTimeBetween($issueDate, $expiryDate),
            'status' => $status,
            'verification_status' => $verificationStatus,
            'verification_url' => $this->faker->optional(0.6)->url(),
            'attachment_path' => $this->faker->optional(0.4)->filePath(),
            'credits_earned' => $this->faker->optional(0.7)->numberBetween(1, 50),
            'is_recurring' => $isRecurring,
            'renewal_period' => $isRecurring ? $this->faker->randomElement([12, 18, 24, 36, 48, 60]) : null,
            'renewal_requirements' => $isRecurring ? $this->generateRenewalRequirements($category) : null,
            'verified_at' => $verifiedAt,
            'verified_by' => $verifiedBy,
            'notes' => $this->faker->optional(0.3)->sentences(2, true),
        ];
    }

    /**
     * Generate a realistic certification name based on category.
     */
    protected function generateCertificationName(CertificationCategory $category): string
    {
        $names = [
            CertificationCategory::PROFESSIONAL => [
                'Professional Engineer (PE)',
                'Certified Public Accountant (CPA)',
                'Project Management Professional (PMP)',
                'Certified Information Systems Auditor (CISA)',
                'Certified Financial Planner (CFP)',
                'Certified Management Accountant (CMA)',
                'Certified Internal Auditor (CIA)',
                'Certified Fraud Examiner (CFE)',
                'Certified Business Analysis Professional (CBAP)',
                'Certified Scrum Master (CSM)',
            ],
            CertificationCategory::TECHNICAL => [
                'AWS Certified Solutions Architect',
                'Microsoft Certified: Azure Solutions Architect Expert',
                'Google Cloud Professional Cloud Architect',
                'Cisco Certified Network Associate (CCNA)',
                'CompTIA A+ Certification',
                'Certified Ethical Hacker (CEH)',
                'Oracle Certified Professional (OCP)',
                'Red Hat Certified Engineer (RHCE)',
                'VMware Certified Professional (VCP)',
                'Certified Kubernetes Administrator (CKA)',
            ],
            CertificationCategory::SAFETY => [
                'OSHA 30-Hour Construction Safety',
                'OSHA 10-Hour General Industry Safety',
                'First Aid and CPR Certification',
                'Hazardous Materials Handling (HAZMAT)',
                'Confined Space Entry Certification',
                'Fall Protection Certification',
                'Scaffold Safety Certification',
                'Electrical Safety Certification',
                'Fire Safety Certification',
                'Emergency Response Certification',
            ],
            CertificationCategory::COMPLIANCE => [
                'ISO 9001:2015 Quality Management',
                'ISO 14001:2015 Environmental Management',
                'ISO 27001:2013 Information Security',
                'SOC 2 Type II Compliance',
                'GDPR Compliance Certification',
                'HIPAA Compliance Certification',
                'PCI DSS Compliance',
                'FDA Compliance Certification',
                'EPA Environmental Compliance',
                'DOT Transportation Compliance',
            ],
            CertificationCategory::EDUCATIONAL => [
                'Bachelor of Science in Engineering',
                'Master of Business Administration (MBA)',
                'Doctor of Philosophy (PhD)',
                'Associate Degree in Technology',
                'Professional Development Certificate',
                'Continuing Education Units (CEU)',
                'Industry Training Certification',
                'Skills Development Program',
                'Advanced Technical Training',
                'Leadership Development Program',
            ],
            CertificationCategory::INDUSTRY_SPECIFIC => [
                'API 510 Pressure Vessel Inspector',
                'ASME Boiler and Pressure Vessel Code',
                'NACE Coating Inspector Certification',
                'AWS Certified Welding Inspector (CWI)',
                'API 570 Piping Inspector',
                'ASNT NDT Level II Certification',
                'API 653 Aboveground Storage Tank Inspector',
                'AWS Certified Welder',
                'API 1169 Pipeline Construction Inspector',
                'ASME B31.3 Process Piping Code',
            ],
            CertificationCategory::OTHER => [
                'Customer Service Excellence',
                'Sales and Marketing Certification',
                'Supply Chain Management',
                'Quality Assurance Certification',
                'Risk Management Professional',
                'Change Management Certification',
                'Innovation and Creativity',
                'Strategic Planning Professional',
                'Team Leadership Certification',
                'Communication Skills Mastery',
            ],
        ];

        return $this->faker->randomElement($names[$category] ?? $names[CertificationCategory::OTHER]);
    }

    /**
     * Generate a realistic certification number based on category.
     */
    protected function generateCertificationNumber(CertificationCategory $category): string
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

        $prefix = $this->faker->randomElement($prefixes[$category] ?? ['CERT']);
        $year = $this->faker->numberBetween(2015, 2024);
        $number = $this->faker->numberBetween(1000, 99999);

        return "{$prefix}-{$year}-{$number}";
    }

    /**
     * Generate a realistic issuing organization based on category.
     */
    protected function generateIssuingOrganization(CertificationCategory $category): string
    {
        $organizations = [
            CertificationCategory::PROFESSIONAL => [
                'National Council of Examiners for Engineering and Surveying',
                'American Institute of Certified Public Accountants',
                'Project Management Institute',
                'Information Systems Audit and Control Association',
                'Certified Financial Planner Board of Standards',
                'Institute of Management Accountants',
                'Institute of Internal Auditors',
                'Association of Certified Fraud Examiners',
                'International Institute of Business Analysis',
                'Scrum Alliance',
            ],
            CertificationCategory::TECHNICAL => [
                'Amazon Web Services',
                'Microsoft Corporation',
                'Google Cloud Platform',
                'Cisco Systems',
                'CompTIA',
                'EC-Council',
                'Oracle Corporation',
                'Red Hat',
                'VMware',
                'Cloud Native Computing Foundation',
            ],
            CertificationCategory::SAFETY => [
                'Occupational Safety and Health Administration',
                'American Red Cross',
                'National Safety Council',
                'American Heart Association',
                'Safety Council of Greater St. Louis',
                'National Fire Protection Association',
                'American Society of Safety Professionals',
                'Board of Certified Safety Professionals',
                'International Safety Equipment Association',
                'Safety Equipment Institute',
            ],
            CertificationCategory::COMPLIANCE => [
                'International Organization for Standardization',
                'American Institute of Certified Public Accountants',
                'Information Systems Audit and Control Association',
                'European Union Data Protection Authorities',
                'U.S. Department of Health and Human Services',
                'Payment Card Industry Security Standards Council',
                'U.S. Food and Drug Administration',
                'U.S. Environmental Protection Agency',
                'U.S. Department of Transportation',
                'Federal Communications Commission',
            ],
            CertificationCategory::EDUCATIONAL => [
                'Accredited University or College',
                'Professional Development Institute',
                'Continuing Education Provider',
                'Industry Training Organization',
                'Skills Development Center',
                'Leadership Development Institute',
                'Technical Training Academy',
                'Professional Association',
                'Government Training Program',
                'Corporate University',
            ],
            CertificationCategory::INDUSTRY_SPECIFIC => [
                'American Petroleum Institute',
                'American Society of Mechanical Engineers',
                'National Association of Corrosion Engineers',
                'American Welding Society',
                'American Society for Nondestructive Testing',
                'American Society of Civil Engineers',
                'American Institute of Chemical Engineers',
                'Society of Petroleum Engineers',
                'American Society of Heating, Refrigerating and Air-Conditioning Engineers',
                'Institute of Electrical and Electronics Engineers',
            ],
            CertificationCategory::OTHER => [
                'Customer Service Institute',
                'Sales and Marketing Association',
                'Supply Chain Management Institute',
                'Quality Assurance Institute',
                'Risk Management Society',
                'Change Management Institute',
                'Innovation and Creativity Center',
                'Strategic Planning Association',
                'Leadership Development Institute',
                'Communication Skills Academy',
            ],
        ];

        return $this->faker->randomElement($organizations[$category] ?? $organizations[CertificationCategory::OTHER]);
    }

    /**
     * Generate realistic renewal requirements based on category.
     */
    protected function generateRenewalRequirements(CertificationCategory $category): array
    {
        $requirements = [
            CertificationCategory::PROFESSIONAL => [
                'Complete continuing education hours',
                'Maintain professional development units',
                'Pass renewal examination',
                'Submit renewal application',
                'Pay renewal fees',
                'Provide proof of work experience',
                'Complete ethics training',
                'Submit professional references',
            ],
            CertificationCategory::TECHNICAL => [
                'Complete continuing education credits',
                'Pass recertification exam',
                'Maintain active status',
                'Submit renewal documentation',
                'Pay annual fees',
                'Complete hands-on projects',
                'Attend technical conferences',
                'Participate in community forums',
            ],
            CertificationCategory::SAFETY => [
                'Complete refresher training',
                'Pass safety assessment',
                'Maintain first aid certification',
                'Complete hazard recognition training',
                'Submit safety incident reports',
                'Attend safety meetings',
                'Complete emergency response drills',
                'Maintain equipment certifications',
            ],
            CertificationCategory::COMPLIANCE => [
                'Complete annual compliance training',
                'Pass compliance assessment',
                'Submit compliance reports',
                'Complete audit requirements',
                'Maintain documentation',
                'Attend compliance updates',
                'Complete risk assessments',
                'Submit corrective action plans',
            ],
            CertificationCategory::EDUCATIONAL => [
                'Complete continuing education units',
                'Maintain academic standing',
                'Submit progress reports',
                'Complete required coursework',
                'Attend academic conferences',
                'Submit research papers',
                'Complete internships',
                'Maintain professional memberships',
            ],
            CertificationCategory::INDUSTRY_SPECIFIC => [
                'Complete industry-specific training',
                'Pass technical assessments',
                'Maintain industry standards',
                'Complete practical examinations',
                'Submit technical reports',
                'Attend industry conferences',
                'Complete field inspections',
                'Maintain equipment certifications',
            ],
            CertificationCategory::OTHER => [
                'Complete skill assessments',
                'Maintain performance standards',
                'Submit progress reports',
                'Complete required training',
                'Attend professional development',
                'Submit work samples',
                'Complete evaluations',
                'Maintain active participation',
            ],
        ];

        $categoryRequirements = $requirements[$category] ?? $requirements[CertificationCategory::OTHER];
        $count = $this->faker->numberBetween(3, 6);

        return $this->faker->randomElements($categoryRequirements, $count);
    }

    /**
     * Indicate that the certification is active.
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => CertificationStatus::ACTIVE,
            'expiry_date' => $this->faker->dateTimeBetween('now', '+5 years'),
        ]);
    }

    /**
     * Indicate that the certification is expired.
     */
    public function expired(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => CertificationStatus::EXPIRED,
            'expiry_date' => $this->faker->dateTimeBetween('-2 years', '-1 month'),
        ]);
    }

    /**
     * Indicate that the certification is verified.
     */
    public function verified(): static
    {
        return $this->state(fn (array $attributes) => [
            'verification_status' => VerificationStatus::VERIFIED,
            'verified_at' => $this->faker->dateTimeBetween($attributes['issue_date'] ?? '-1 year', 'now'),
            'verified_by' => User::factory(),
        ]);
    }

    /**
     * Indicate that the certification is unverified.
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
     * Indicate that the certification is recurring.
     */
    public function recurring(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_recurring' => true,
            'renewal_period' => $this->faker->randomElement([12, 18, 24, 36, 48, 60]),
            'renewal_requirements' => $this->generateRenewalRequirements($attributes['category'] ?? CertificationCategory::PROFESSIONAL),
        ]);
    }

    /**
     * Indicate that the certification is expiring soon.
     */
    public function expiringSoon(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => CertificationStatus::ACTIVE,
            'expiry_date' => $this->faker->dateTimeBetween('now', '+30 days'),
        ]);
    }

    /**
     * Indicate that the certification is pending renewal.
     */
    public function pendingRenewal(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => CertificationStatus::PENDING_RENEWAL,
            'expiry_date' => $this->faker->dateTimeBetween('-6 months', '-1 month'),
        ]);
    }

    /**
     * Indicate that the certification is suspended.
     */
    public function suspended(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => CertificationStatus::SUSPENDED,
            'notes' => ['Suspended due to compliance issues'],
        ]);
    }

    /**
     * Indicate that the certification is revoked.
     */
    public function revoked(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => CertificationStatus::REVOKED,
            'notes' => ['Revoked due to policy violations'],
        ]);
    }

    /**
     * Indicate that the certification is pending verification.
     */
    public function pendingVerification(): static
    {
        return $this->state(fn (array $attributes) => [
            'verification_status' => VerificationStatus::PENDING,
            'verified_at' => null,
            'verified_by' => null,
        ]);
    }

    /**
     * Indicate that the certification requires update.
     */
    public function requiresUpdate(): static
    {
        return $this->state(fn (array $attributes) => [
            'verification_status' => VerificationStatus::REQUIRES_UPDATE,
            'notes' => ['Additional documentation required for verification'],
        ]);
    }
};
