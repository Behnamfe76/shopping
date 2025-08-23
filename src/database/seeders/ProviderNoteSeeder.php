<?php

namespace Fereydooni\Shopping\database\seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ProviderNoteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Check if provider_notes table exists
        if (!Schema::hasTable('provider_notes')) {
            return;
        }

        // Check if providers table exists and has data
        if (!Schema::hasTable('providers') || DB::table('providers')->count() === 0) {
            return;
        }

        // Check if users table exists and has data
        if (!Schema::hasTable('users') || DB::table('users')->count() === 0) {
            return;
        }

        $noteTypes = ['general', 'quality', 'financial', 'contract', 'performance', 'communication'];

        // Get existing provider and user IDs
        $providerIds = DB::table('providers')->pluck('id')->toArray();
        $userIds = DB::table('users')->pluck('id')->toArray();

        if (empty($providerIds) || empty($userIds)) {
            return;
        }

        $notes = [];

        foreach ($providerIds as $providerId) {
            // Create 2-5 notes per provider
            $numNotes = rand(2, 5);

            for ($i = 0; $i < $numNotes; $i++) {
                $notes[] = [
                    'provider_id' => $providerId,
                    'user_id' => $userIds[array_rand($userIds)],
                    'note' => $this->generateNoteContent(),
                    'type' => $noteTypes[array_rand($noteTypes)],
                    'is_public' => rand(0, 1),
                    'is_archived' => rand(0, 1),
                    'created_at' => now()->subDays(rand(1, 365)),
                    'updated_at' => now()->subDays(rand(0, 30)),
                ];
            }
        }

        // Insert notes in batches
        foreach (array_chunk($notes, 100) as $chunk) {
            DB::table('provider_notes')->insert($chunk);
        }
    }

    /**
     * Generate sample note content.
     */
    private function generateNoteContent(): string
    {
        $contents = [
            'Provider has been consistently delivering high-quality products.',
            'Payment terms have been extended due to good payment history.',
            'Quality rating improved after recent product improvements.',
            'Contract renewal discussion scheduled for next month.',
            'Excellent communication and response time.',
            'Minor quality issues reported, monitoring required.',
            'Financial performance is above average.',
            'Delivery performance has been consistent.',
            'Provider requested price adjustment for next quarter.',
            'Annual review completed with positive feedback.',
        ];

        return $contents[array_rand($contents)];
    }
}
