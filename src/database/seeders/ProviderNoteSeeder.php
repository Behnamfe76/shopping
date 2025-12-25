<?php

namespace Fereydooni\Shopping\database\seeders;

use Fereydooni\Shopping\app\Models\Provider;
use Fereydooni\Shopping\app\Models\ProviderNote;
use Fereydooni\Shopping\app\Models\User;
use Illuminate\Database\Seeder;

class ProviderNoteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get existing providers and users
        $providers = Provider::all();
        $users = User::all();

        if ($providers->isEmpty() || $users->isEmpty()) {
            $this->command->warn('No providers or users found. Skipping provider note seeding.');

            return;
        }

        $this->command->info('Seeding provider notes...');

        // Create sample notes for each provider
        foreach ($providers as $provider) {
            $this->createSampleNotesForProvider($provider, $users);
        }

        $this->command->info('Provider notes seeded successfully!');
    }

    /**
     * Create sample notes for a specific provider
     */
    private function createSampleNotesForProvider(Provider $provider, $users): void
    {
        $noteTypes = [
            'general' => 0.3,      // 30% chance
            'contract' => 0.2,     // 20% chance
            'payment' => 0.15,     // 15% chance
            'quality' => 0.15,     // 15% chance
            'performance' => 0.1,  // 10% chance
            'communication' => 0.05, // 5% chance
            'other' => 0.05,        // 5% chance
        ];

        $priorities = [
            'low' => 0.4,      // 40% chance
            'medium' => 0.35,  // 35% chance
            'high' => 0.2,     // 20% chance
            'urgent' => 0.05,   // 5% chance
        ];

        // Create 5-15 notes per provider
        $numNotes = rand(5, 15);

        for ($i = 0; $i < $numNotes; $i++) {
            $noteType = $this->getRandomWeighted($noteTypes);
            $priority = $this->getRandomWeighted($priorities);
            $user = $users->random();
            $isPrivate = rand(1, 100) <= 20; // 20% chance of being private
            $isArchived = rand(1, 100) <= 10; // 10% chance of being archived

            $noteData = [
                'provider_id' => $provider->id,
                'user_id' => $user->id,
                'title' => $this->generateTitle($noteType, $priority),
                'content' => $this->generateContent($noteType, $priority),
                'note_type' => $noteType,
                'priority' => $priority,
                'is_private' => $isPrivate,
                'is_archived' => $isArchived,
                'tags' => $this->generateTags($noteType, $priority),
                'attachments' => $this->generateAttachments($noteType),
                'created_at' => $this->generateRandomDate(),
                'updated_at' => $this->generateRandomDate(),
            ];

            ProviderNote::create($noteData);
        }
    }

    /**
     * Generate a weighted random selection
     */
    private function getRandomWeighted(array $weights): string
    {
        $rand = rand(1, 100);
        $cumulative = 0;

        foreach ($weights as $item => $weight) {
            $cumulative += ($weight * 100);
            if ($rand <= $cumulative) {
                return $item;
            }
        }

        return array_key_first($weights);
    }

    /**
     * Generate a title based on note type and priority
     */
    private function generateTitle(string $noteType, string $priority): string
    {
        $titles = [
            'general' => [
                'low' => ['General Information', 'Basic Note', 'Standard Update'],
                'medium' => ['Important Update', 'Key Information', 'Notable Change'],
                'high' => ['Critical Information', 'Important Notice', 'Key Update'],
                'urgent' => ['URGENT: Immediate Action Required', 'CRITICAL: Important Notice'],
            ],
            'contract' => [
                'low' => ['Contract Review', 'Terms Discussion', 'Agreement Note'],
                'medium' => ['Contract Update', 'Terms Revision', 'Agreement Change'],
                'high' => ['Contract Issue', 'Terms Problem', 'Agreement Concern'],
                'urgent' => ['URGENT: Contract Problem', 'CRITICAL: Agreement Issue'],
            ],
            'payment' => [
                'low' => ['Payment Note', 'Billing Update', 'Invoice Note'],
                'medium' => ['Payment Issue', 'Billing Problem', 'Invoice Concern'],
                'high' => ['Payment Problem', 'Billing Issue', 'Invoice Problem'],
                'urgent' => ['URGENT: Payment Issue', 'CRITICAL: Billing Problem'],
            ],
            'quality' => [
                'low' => ['Quality Check', 'Standards Review', 'Quality Note'],
                'medium' => ['Quality Issue', 'Standards Problem', 'Quality Concern'],
                'high' => ['Quality Problem', 'Standards Issue', 'Quality Issue'],
                'urgent' => ['URGENT: Quality Problem', 'CRITICAL: Standards Issue'],
            ],
            'performance' => [
                'low' => ['Performance Note', 'Metrics Review', 'Performance Update'],
                'medium' => ['Performance Issue', 'Metrics Problem', 'Performance Concern'],
                'high' => ['Performance Problem', 'Metrics Issue', 'Performance Issue'],
                'urgent' => ['URGENT: Performance Problem', 'CRITICAL: Metrics Issue'],
            ],
            'communication' => [
                'low' => ['Communication Note', 'Message Update', 'Communication Update'],
                'medium' => ['Communication Issue', 'Message Problem', 'Communication Concern'],
                'high' => ['Communication Problem', 'Message Issue', 'Communication Issue'],
                'urgent' => ['URGENT: Communication Problem', 'CRITICAL: Message Issue'],
            ],
            'other' => [
                'low' => ['Miscellaneous Note', 'Other Update', 'General Note'],
                'medium' => ['Miscellaneous Issue', 'Other Problem', 'General Concern'],
                'high' => ['Miscellaneous Problem', 'Other Issue', 'General Issue'],
                'urgent' => ['URGENT: Miscellaneous Problem', 'CRITICAL: Other Issue'],
            ],
        ];

        $availableTitles = $titles[$noteType][$priority] ?? $titles['general']['medium'];

        return $availableTitles[array_rand($availableTitles)];
    }

    /**
     * Generate content based on note type and priority
     */
    private function generateContent(string $noteType, string $priority): string
    {
        $contentTemplates = [
            'general' => [
                'low' => 'This is a general note for routine information and updates.',
                'medium' => 'This note contains important information that requires attention.',
                'high' => 'This is a critical note that needs immediate attention and action.',
                'urgent' => 'URGENT: This note requires immediate action and cannot be delayed.',
            ],
            'contract' => [
                'low' => 'Contract review and terms discussion note.',
                'medium' => 'Contract update with important changes to terms.',
                'high' => 'Contract issue that needs immediate resolution.',
                'urgent' => 'URGENT: Critical contract problem requiring immediate legal review.',
            ],
            'payment' => [
                'low' => 'Payment and billing information update.',
                'medium' => 'Payment issue that needs to be addressed.',
                'high' => 'Payment problem requiring immediate attention.',
                'urgent' => 'URGENT: Critical payment issue affecting business operations.',
            ],
            'quality' => [
                'low' => 'Quality standards review and compliance note.',
                'medium' => 'Quality issue that needs to be investigated.',
                'high' => 'Quality problem requiring immediate corrective action.',
                'urgent' => 'URGENT: Critical quality issue affecting product safety.',
            ],
            'performance' => [
                'low' => 'Performance metrics and KPIs review.',
                'medium' => 'Performance issue that needs monitoring.',
                'high' => 'Performance problem requiring immediate intervention.',
                'urgent' => 'URGENT: Critical performance issue affecting business goals.',
            ],
            'communication' => [
                'low' => 'Communication and messaging update.',
                'medium' => 'Communication issue that needs resolution.',
                'high' => 'Communication problem requiring immediate attention.',
                'urgent' => 'URGENT: Critical communication breakdown affecting operations.',
            ],
            'other' => [
                'low' => 'Miscellaneous note for other updates and information.',
                'medium' => 'Other issue that needs to be addressed.',
                'high' => 'Other problem requiring attention.',
                'urgent' => 'URGENT: Critical issue requiring immediate action.',
            ],
        ];

        $baseContent = $contentTemplates[$noteType][$priority] ?? $contentTemplates['general']['medium'];

        // Add some variety to the content
        $additionalDetails = [
            'Please review and take appropriate action.',
            'This requires follow-up and documentation.',
            'Please update the relevant stakeholders.',
            'This needs to be escalated if not resolved.',
            'Please ensure compliance with company policies.',
        ];

        $additionalDetail = $additionalDetails[array_rand($additionalDetails)];

        return $baseContent.' '.$additionalDetail;
    }

    /**
     * Generate tags based on note type and priority
     */
    private function generateTags(string $noteType, string $priority): array
    {
        $baseTags = [$noteType, $priority];

        $typeSpecificTags = [
            'general' => ['information', 'update', 'routine'],
            'contract' => ['legal', 'terms', 'agreement', 'compliance'],
            'payment' => ['billing', 'finance', 'invoice', 'revenue'],
            'quality' => ['standards', 'compliance', 'inspection', 'testing'],
            'performance' => ['metrics', 'kpi', 'goals', 'targets'],
            'communication' => ['message', 'correspondence', 'notification'],
            'other' => ['miscellaneous', 'general', 'other'],
        ];

        $prioritySpecificTags = [
            'low' => ['routine', 'low-priority'],
            'medium' => ['important', 'attention'],
            'high' => ['critical', 'urgent', 'action-required'],
            'urgent' => ['urgent', 'critical', 'immediate-action', 'escalation'],
        ];

        $tags = array_merge(
            $baseTags,
            $typeSpecificTags[$noteType] ?? [],
            $prioritySpecificTags[$priority] ?? []
        );

        // Randomly select 2-4 tags
        $numTags = rand(2, 4);
        $selectedTags = array_rand(array_flip($tags), min($numTags, count($tags)));

        return is_array($selectedTags) ? $selectedTags : [$selectedTags];
    }

    /**
     * Generate attachments based on note type
     */
    private function generateAttachments(string $noteType): ?array
    {
        // 30% chance of having attachments
        if (rand(1, 100) > 30) {
            return null;
        }

        $attachmentTypes = [
            'general' => ['pdf', 'doc', 'txt'],
            'contract' => ['pdf', 'doc', 'docx'],
            'payment' => ['pdf', 'xls', 'xlsx'],
            'quality' => ['pdf', 'jpg', 'png'],
            'performance' => ['pdf', 'xls', 'xlsx'],
            'communication' => ['pdf', 'doc', 'txt'],
            'other' => ['pdf', 'doc', 'txt'],
        ];

        $types = $attachmentTypes[$noteType] ?? ['pdf', 'doc'];
        $numAttachments = rand(1, 3);
        $attachments = [];

        for ($i = 0; $i < $numAttachments; $i++) {
            $type = $types[array_rand($types)];
            $filename = "{$noteType}_note_".uniqid().".{$type}";
            $attachments[] = "attachments/provider-notes/{$filename}";
        }

        return $attachments;
    }

    /**
     * Generate a random date within the last year
     */
    private function generateRandomDate(): string
    {
        $startDate = strtotime('-1 year');
        $endDate = time();
        $randomTimestamp = rand($startDate, $endDate);

        return date('Y-m-d H:i:s', $randomTimestamp);
    }
}
