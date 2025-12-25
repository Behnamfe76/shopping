<?php

namespace Fereydooni\Shopping\Database\Seeders;

use Fereydooni\Shopping\app\Enums\CustomerNotePriority;
use Fereydooni\Shopping\app\Enums\CustomerNoteType;
use Fereydooni\Shopping\app\Models\Customer;
use Fereydooni\Shopping\app\Models\CustomerNote;
use Fereydooni\Shopping\app\Models\User;
use Illuminate\Database\Seeder;

class CustomerNoteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $customers = Customer::take(10)->get();
        $users = User::take(5)->get();

        if ($customers->isEmpty() || $users->isEmpty()) {
            $this->command->warn('No customers or users found. Skipping customer note seeding.');

            return;
        }

        $noteTypes = CustomerNoteType::cases();
        $priorities = CustomerNotePriority::cases();

        $sampleNotes = [
            [
                'title' => 'Initial Contact',
                'content' => 'Customer contacted us for the first time. Showed interest in our premium products.',
                'type' => CustomerNoteType::SALES,
                'priority' => CustomerNotePriority::MEDIUM,
                'tags' => ['first-contact', 'sales-lead'],
            ],
            [
                'title' => 'Technical Support Request',
                'content' => 'Customer experiencing issues with product setup. Need to provide detailed instructions.',
                'type' => CustomerNoteType::TECHNICAL,
                'priority' => CustomerNotePriority::HIGH,
                'tags' => ['technical', 'support', 'urgent'],
            ],
            [
                'title' => 'Payment Issue',
                'content' => 'Customer reported payment processing error. Investigating with payment provider.',
                'type' => CustomerNoteType::BILLING,
                'priority' => CustomerNotePriority::URGENT,
                'tags' => ['billing', 'payment', 'urgent'],
            ],
            [
                'title' => 'Product Feedback',
                'content' => 'Customer provided positive feedback about product quality and delivery speed.',
                'type' => CustomerNoteType::FEEDBACK,
                'priority' => CustomerNotePriority::LOW,
                'tags' => ['feedback', 'positive'],
            ],
            [
                'title' => 'Complaint Resolution',
                'content' => 'Customer complaint about delayed delivery. Offered compensation and expedited shipping.',
                'type' => CustomerNoteType::COMPLAINT,
                'priority' => CustomerNotePriority::HIGH,
                'tags' => ['complaint', 'delivery', 'resolved'],
            ],
            [
                'title' => 'Follow-up Required',
                'content' => 'Customer requested quote for bulk order. Need to follow up within 48 hours.',
                'type' => CustomerNoteType::FOLLOW_UP,
                'priority' => CustomerNotePriority::MEDIUM,
                'tags' => ['follow-up', 'quote', 'bulk-order'],
            ],
            [
                'title' => 'General Inquiry',
                'content' => 'Customer asked about product specifications and availability.',
                'type' => CustomerNoteType::GENERAL,
                'priority' => CustomerNotePriority::LOW,
                'tags' => ['inquiry', 'specifications'],
            ],
            [
                'title' => 'Support Ticket',
                'content' => 'Customer opened support ticket for account access issues.',
                'type' => CustomerNoteType::SUPPORT,
                'priority' => CustomerNotePriority::MEDIUM,
                'tags' => ['support', 'account', 'access'],
            ],
        ];

        foreach ($customers as $customer) {
            // Create 2-5 notes per customer
            $noteCount = rand(2, 5);

            for ($i = 0; $i < $noteCount; $i++) {
                $noteData = $sampleNotes[array_rand($sampleNotes)];

                CustomerNote::create([
                    'customer_id' => $customer->id,
                    'user_id' => $users->random()->id,
                    'title' => $noteData['title'],
                    'content' => $noteData['content'],
                    'note_type' => $noteData['type'],
                    'priority' => $noteData['priority'],
                    'is_private' => rand(0, 1),
                    'is_pinned' => rand(0, 1),
                    'tags' => $noteData['tags'],
                    'created_at' => now()->subDays(rand(1, 30)),
                    'updated_at' => now()->subDays(rand(0, 29)),
                ]);
            }
        }

        $this->command->info('Customer notes seeded successfully!');
    }
}
