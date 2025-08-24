<?php

namespace Fereydooni\Shopping\app\Listeners;

use Fereydooni\Shopping\app\Events\CommunicationSent;
use Fereydooni\Shopping\app\Events\ProviderCommunicationCreated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class SendCommunicationNotification implements ShouldQueue
{
    use InteractsWithQueue;

    public function __construct()
    {
        //
    }

    public function handle(CommunicationSent|ProviderCommunicationCreated $event): void
    {
        $communication = $event->providerCommunication;

        try {
            // Send email notification
            $this->sendEmailNotification($communication);

            // Send SMS notification if applicable
            if ($this->shouldSendSMS($communication)) {
                $this->sendSMSNotification($communication);
            }

            // Send in-app notification
            $this->sendInAppNotification($communication);

            Log::info('Communication notification sent successfully', [
                'communication_id' => $communication->id,
                'type' => $communication->communication_type,
                'recipient' => $communication->provider_id
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to send communication notification', [
                'communication_id' => $communication->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    private function sendEmailNotification($communication): void
    {
        // Implementation for email notification
        // This would typically use Laravel's notification system
        Log::info('Email notification sent for communication', [
            'communication_id' => $communication->id
        ]);
    }

    private function shouldSendSMS($communication): bool
    {
        // Logic to determine if SMS should be sent
        return in_array($communication->communication_type, ['sms', 'urgent']);
    }

    private function sendSMSNotification($communication): void
    {
        // Implementation for SMS notification
        Log::info('SMS notification sent for communication', [
            'communication_id' => $communication->id
        ]);
    }

    private function sendInAppNotification($communication): void
    {
        // Implementation for in-app notification
        Log::info('In-app notification sent for communication', [
            'communication_id' => $communication->id
        ]);
    }
}
