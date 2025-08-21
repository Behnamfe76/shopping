<?php

namespace Fereydooni\Shopping\database\seeders;

use Illuminate\Database\Seeder;
use Fereydooni\Shopping\app\Models\CustomerCommunication;
use Fereydooni\Shopping\app\Models\Customer;
use Fereydooni\Shopping\app\Models\User;
use Fereydooni\Shopping\app\Enums\CommunicationType;
use Fereydooni\Shopping\app\Enums\CommunicationStatus;
use Fereydooni\Shopping\app\Enums\CommunicationPriority;
use Fereydooni\Shopping\app\Enums\CommunicationChannel;

class CustomerCommunicationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $customers = Customer::all();
        $users = User::all();

        if ($customers->isEmpty() || $users->isEmpty()) {
            $this->command->warn('No customers or users found. Skipping CustomerCommunication seeding.');
            return;
        }

        $communicationTypes = CommunicationType::cases();
        $statuses = CommunicationStatus::cases();
        $priorities = CommunicationPriority::cases();
        $channels = CommunicationChannel::cases();

        foreach ($customers as $customer) {
            // Create 5-15 communications per customer
            $communicationCount = rand(5, 15);
            
            for ($i = 0; $i < $communicationCount; $i++) {
                $communicationType = $communicationTypes[array_rand($communicationTypes)];
                $status = $statuses[array_rand($statuses)];
                $priority = $priorities[array_rand($priorities)];
                $channel = $channels[array_rand($channels)];
                $user = $users->random();

                $communication = CustomerCommunication::create([
                    'customer_id' => $customer->id,
                    'user_id' => $user->id,
                    'communication_type' => $communicationType->value,
                    'subject' => $this->generateSubject($communicationType),
                    'content' => $this->generateContent($communicationType),
                    'status' => $status->value,
                    'priority' => $priority->value,
                    'channel' => $channel->value,
                    'scheduled_at' => $this->generateScheduledAt($status),
                    'sent_at' => $this->generateSentAt($status),
                    'delivered_at' => $this->generateDeliveredAt($status),
                    'opened_at' => $this->generateOpenedAt($status),
                    'clicked_at' => $this->generateClickedAt($status),
                    'bounced_at' => $this->generateBouncedAt($status),
                    'unsubscribed_at' => $this->generateUnsubscribedAt($status),
                    'metadata' => $this->generateMetadata($communicationType),
                    'tracking_data' => $this->generateTrackingData($status),
                    'created_at' => now()->subDays(rand(1, 365)),
                    'updated_at' => now()->subDays(rand(0, 30)),
                ]);
            }
        }

        $this->command->info('CustomerCommunication seeding completed successfully.');
    }

    protected function generateSubject(CommunicationType $type): string
    {
        $subjects = [
            CommunicationType::EMAIL => [
                'Welcome to our store!',
                'Your order has been shipped',
                'Special offer just for you',
                'Thank you for your purchase',
                'New products available',
                'Your account has been updated',
                'Exclusive member benefits',
                'Seasonal sale announcement',
                'Product review request',
                'Loyalty program update'
            ],
            CommunicationType::SMS => [
                'Order shipped',
                'Delivery update',
                'Special offer',
                'Account alert',
                'Payment reminder'
            ],
            CommunicationType::PUSH_NOTIFICATION => [
                'New order update',
                'Special offer available',
                'Product back in stock',
                'Account notification',
                'App update available'
            ],
            CommunicationType::IN_APP => [
                'Welcome message',
                'Feature announcement',
                'Account update',
                'New content available',
                'System notification'
            ],
            CommunicationType::LETTER => [
                'Welcome letter',
                'Account statement',
                'Legal notice',
                'Important update',
                'Thank you letter'
            ],
            CommunicationType::PHONE_CALL => [
                'Order confirmation call',
                'Customer service call',
                'Follow-up call',
                'Survey call',
                'Support call'
            ]
        ];

        $typeSubjects = $subjects[$type] ?? ['General communication'];
        return $typeSubjects[array_rand($typeSubjects)];
    }

    protected function generateContent(CommunicationType $type): string
    {
        $contents = [
            CommunicationType::EMAIL => [
                'Thank you for choosing our store. We appreciate your business and look forward to serving you again.',
                'Your order has been successfully shipped and is on its way to you. Track your package using the link below.',
                'We have a special offer just for you! Don\'t miss out on these amazing deals.',
                'Thank you for your recent purchase. We hope you enjoy your new items.',
                'Check out our latest products that we think you\'ll love.',
                'Your account has been successfully updated. Please review the changes.',
                'As a valued member, you have access to exclusive benefits and offers.',
                'Our seasonal sale is now live! Get up to 50% off on selected items.',
                'We\'d love to hear your feedback about your recent purchase.',
                'Your loyalty points have been updated. Check your account for details.'
            ],
            CommunicationType::SMS => [
                'Your order #12345 has been shipped. Track at example.com/track',
                'Your delivery will arrive today between 2-4 PM.',
                'Special offer: 20% off all items. Use code SAVE20. Valid until tomorrow.',
                'Your account has been updated. Check your email for details.',
                'Payment reminder: Your invoice is due in 3 days.'
            ],
            CommunicationType::PUSH_NOTIFICATION => [
                'Your order has been updated. Tap to view details.',
                'Special offer available now! Tap to shop.',
                'Product is back in stock. Tap to purchase.',
                'Your account has been updated.',
                'New app version available with improved features.'
            ],
            CommunicationType::IN_APP => [
                'Welcome to our app! We\'re excited to have you here.',
                'New features are now available. Check them out!',
                'Your account settings have been updated.',
                'New content is available for you to explore.',
                'System maintenance completed successfully.'
            ],
            CommunicationType::LETTER => [
                'Welcome to our community. We\'re excited to have you as a customer.',
                'Please find enclosed your account statement for this month.',
                'This letter serves as official notice regarding your account.',
                'We have important updates regarding your account.',
                'Thank you for your continued business and trust in our company.'
            ],
            CommunicationType::PHONE_CALL => [
                'This is a confirmation call regarding your recent order.',
                'We\'re calling to provide customer service assistance.',
                'This is a follow-up call regarding your recent inquiry.',
                'We\'re conducting a customer satisfaction survey.',
                'We\'re calling to provide technical support.'
            ]
        ];

        $typeContents = $contents[$type] ?? ['General communication content.'];
        return $typeContents[array_rand($typeContents)];
    }

    protected function generateScheduledAt(CommunicationStatus $status): ?string
    {
        if (in_array($status->value, ['scheduled', 'sending', 'sent', 'delivered', 'opened', 'clicked'])) {
            return now()->subDays(rand(1, 30))->toDateTimeString();
        }
        return null;
    }

    protected function generateSentAt(CommunicationStatus $status): ?string
    {
        if (in_array($status->value, ['sent', 'delivered', 'opened', 'clicked'])) {
            return now()->subDays(rand(1, 25))->toDateTimeString();
        }
        return null;
    }

    protected function generateDeliveredAt(CommunicationStatus $status): ?string
    {
        if (in_array($status->value, ['delivered', 'opened', 'clicked'])) {
            return now()->subDays(rand(1, 20))->toDateTimeString();
        }
        return null;
    }

    protected function generateOpenedAt(CommunicationStatus $status): ?string
    {
        if (in_array($status->value, ['opened', 'clicked'])) {
            return now()->subDays(rand(1, 15))->toDateTimeString();
        }
        return null;
    }

    protected function generateClickedAt(CommunicationStatus $status): ?string
    {
        if ($status->value === 'clicked') {
            return now()->subDays(rand(1, 10))->toDateTimeString();
        }
        return null;
    }

    protected function generateBouncedAt(CommunicationStatus $status): ?string
    {
        if ($status->value === 'bounced') {
            return now()->subDays(rand(1, 5))->toDateTimeString();
        }
        return null;
    }

    protected function generateUnsubscribedAt(CommunicationStatus $status): ?string
    {
        if ($status->value === 'unsubscribed') {
            return now()->subDays(rand(1, 3))->toDateTimeString();
        }
        return null;
    }

    protected function generateMetadata(CommunicationType $type): array
    {
        return [
            'communication_type' => $type->value,
            'template_used' => 'default_template',
            'campaign_name' => 'general_campaign',
            'segment_name' => 'all_customers',
            'sender_info' => [
                'name' => 'Customer Service',
                'email' => 'service@example.com',
                'phone' => '+1234567890'
            ],
            'delivery_preferences' => [
                'time_zone' => 'UTC',
                'preferred_time' => '09:00',
                'frequency' => 'weekly'
            ]
        ];
    }

    protected function generateTrackingData(CommunicationStatus $status): array
    {
        $trackingData = [
            'sent_count' => 1,
            'delivered_count' => 0,
            'opened_count' => 0,
            'clicked_count' => 0,
            'bounced_count' => 0,
            'unsubscribed_count' => 0,
            'delivery_rate' => 0.0,
            'open_rate' => 0.0,
            'click_rate' => 0.0,
            'bounce_rate' => 0.0,
            'unsubscribe_rate' => 0.0
        ];

        if (in_array($status->value, ['sent', 'delivered', 'opened', 'clicked'])) {
            $trackingData['delivered_count'] = 1;
            $trackingData['delivery_rate'] = 100.0;
        }

        if (in_array($status->value, ['opened', 'clicked'])) {
            $trackingData['opened_count'] = 1;
            $trackingData['open_rate'] = 100.0;
        }

        if ($status->value === 'clicked') {
            $trackingData['clicked_count'] = 1;
            $trackingData['click_rate'] = 100.0;
        }

        if ($status->value === 'bounced') {
            $trackingData['bounced_count'] = 1;
            $trackingData['bounce_rate'] = 100.0;
        }

        if ($status->value === 'unsubscribed') {
            $trackingData['unsubscribed_count'] = 1;
            $trackingData['unsubscribe_rate'] = 100.0;
        }

        return $trackingData;
    }
}
