<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class ProviderCommunicationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Sample communication types
        $communicationTypes = [
            'email', 'phone', 'chat', 'sms', 'video_call', 'in_person',
            'support_ticket', 'complaint', 'inquiry', 'order_update',
            'payment_notification', 'quality_issue', 'delivery_update',
            'contract_discussion', 'general'
        ];

        // Sample statuses
        $statuses = ['draft', 'sent', 'delivered', 'read', 'replied', 'closed', 'archived', 'failed'];

        // Sample directions
        $directions = ['inbound', 'outbound'];

        // Sample priorities
        $priorities = ['low', 'normal', 'high', 'urgent'];

        // Sample subjects for different communication types
        $subjectTemplates = [
            'email' => 'Email Communication #{id}',
            'phone' => 'Phone Call Follow-up #{id}',
            'chat' => 'Chat Session #{id}',
            'sms' => 'SMS Notification #{id}',
            'support_ticket' => 'Support Ticket #{id} - {issue}',
            'complaint' => 'Complaint #{id} - {issue}',
            'inquiry' => 'Inquiry #{id} - {topic}',
            'order_update' => 'Order Update #{id} - {status}',
            'payment_notification' => 'Payment Notification #{id}',
            'quality_issue' => 'Quality Issue #{id} - {description}',
            'delivery_update' => 'Delivery Update #{id} - {status}',
            'contract_discussion' => 'Contract Discussion #{id}',
            'general' => 'General Communication #{id}'
        ];

        // Sample messages
        $messageTemplates = [
            'email' => 'This is an email communication regarding our business relationship.',
            'phone' => 'Follow-up from our recent phone conversation.',
            'chat' => 'Chat session summary and action items.',
            'sms' => 'Quick SMS notification for urgent matters.',
            'support_ticket' => 'Support ticket details and resolution steps.',
            'complaint' => 'Complaint details and resolution process.',
            'inquiry' => 'Inquiry details and response information.',
            'order_update' => 'Order status update and next steps.',
            'payment_notification' => 'Payment received and confirmation details.',
            'quality_issue' => 'Quality issue details and corrective actions.',
            'delivery_update' => 'Delivery status and tracking information.',
            'contract_discussion' => 'Contract terms discussion and negotiation points.',
            'general' => 'General business communication and updates.'
        ];

        // Sample tags
        $sampleTags = [
            'urgent', 'follow-up', 'resolved', 'pending', 'high-priority',
            'customer-service', 'billing', 'technical', 'logistics', 'quality'
        ];

        // Sample attachments
        $sampleAttachments = [
            ['name' => 'document.pdf', 'size' => '2.5MB', 'type' => 'pdf'],
            ['name' => 'image.jpg', 'size' => '1.2MB', 'type' => 'image'],
            ['name' => 'spreadsheet.xlsx', 'size' => '500KB', 'type' => 'excel']
        ];

        // Create sample communications
        for ($i = 1; $i <= 50; $i++) {
            $communicationType = $communicationTypes[array_rand($communicationTypes)];
            $status = $statuses[array_rand($statuses)];
            $direction = $directions[array_rand($directions)];
            $priority = $priorities[array_rand($priorities)];

            // Generate subject and message based on type
            $subject = str_replace(
                ['{id}', '{issue}', '{topic}', '{status}', '{description}'],
                [$i, 'Sample Issue', 'Sample Topic', 'In Progress', 'Sample Description'],
                $subjectTemplates[$communicationType]
            );

            $message = $messageTemplates[$communicationType];

            // Random provider and user IDs (assuming they exist)
            $providerId = rand(1, 10);
            $userId = rand(1, 20);

            // Generate thread ID for some communications
            $threadId = null;
            $parentId = null;
            if (rand(1, 3) === 1) {
                $threadId = 'thread_' . Str::random(10);
                // Create a parent communication for this thread
                if (rand(1, 2) === 1) {
                    $parentId = $i - 1;
                }
            }

            // Generate timestamps
            $createdAt = Carbon::now()->subDays(rand(0, 30))->subHours(rand(0, 23));
            $sentAt = $status !== 'draft' ? $createdAt->copy()->addMinutes(rand(1, 60)) : null;
            $readAt = in_array($status, ['read', 'replied']) ? $sentAt?->copy()->addMinutes(rand(5, 120)) : null;
            $repliedAt = $status === 'replied' ? $readAt?->copy()->addMinutes(rand(10, 240)) : null;

            // Calculate response time if both sent and replied
            $responseTime = null;
            if ($sentAt && $repliedAt) {
                $responseTime = $sentAt->diffInMinutes($repliedAt);
            }

            // Generate satisfaction rating for some communications
            $satisfactionRating = null;
            if (in_array($status, ['replied', 'closed']) && rand(1, 2) === 1) {
                $satisfactionRating = round(rand(30, 50) / 10, 1); // 3.0 to 5.0
            }

            // Generate random tags
            $tags = [];
            $numTags = rand(0, 3);
            for ($j = 0; $j < $numTags; $j++) {
                $tags[] = $sampleTags[array_rand($sampleTags)];
            }

            // Generate random attachments
            $attachments = [];
            $numAttachments = rand(0, 2);
            for ($j = 0; $j < $numAttachments; $j++) {
                $attachments[] = $sampleAttachments[array_rand($sampleAttachments)];
            }

            // Determine if urgent based on priority
            $isUrgent = $priority === 'urgent' || rand(1, 10) === 1;

            // Determine if archived
            $isArchived = $status === 'archived' || rand(1, 20) === 1;

            // Generate notes for some communications
            $notes = null;
            if (rand(1, 3) === 1) {
                $notes = 'Additional notes for communication #' . $i . '. This is a sample note for demonstration purposes.';
            }

            DB::table('provider_communications')->insert([
                'provider_id' => $providerId,
                'user_id' => $userId,
                'communication_type' => $communicationType,
                'subject' => $subject,
                'message' => $message,
                'direction' => $direction,
                'status' => $status,
                'sent_at' => $sentAt,
                'read_at' => $readAt,
                'replied_at' => $repliedAt,
                'priority' => $priority,
                'is_urgent' => $isUrgent,
                'is_archived' => $isArchived,
                'thread_id' => $threadId,
                'parent_id' => $parentId,
                'response_time' => $responseTime,
                'satisfaction_rating' => $satisfactionRating,
                'attachments' => !empty($attachments) ? json_encode($attachments) : null,
                'tags' => !empty($tags) ? json_encode($tags) : null,
                'notes' => $notes,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ]);
        }

        $this->command->info('Provider Communication seeder completed successfully!');
        $this->command->info('Created 50 sample communications with various types, statuses, and priorities.');
    }
}
