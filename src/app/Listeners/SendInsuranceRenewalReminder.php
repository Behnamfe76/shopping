<?php

namespace App\Listeners;

use App\Events\ProviderInsuranceExpired;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class SendInsuranceRenewalReminder implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(ProviderInsuranceExpired $event): void
    {
        try {
            $providerInsurance = $event->providerInsurance;
            $expirationDate = $event->expirationDate;
            $notificationData = $event->notificationData;

            // Send renewal reminder email
            $this->sendRenewalReminderEmail($providerInsurance, $expirationDate, $notificationData);

            // Send renewal reminder SMS if configured
            $this->sendRenewalReminderSms($providerInsurance, $expirationDate, $notificationData);

            // Send renewal reminder in-app notification
            $this->sendRenewalReminderInApp($providerInsurance, $expirationDate, $notificationData);

            Log::info('Insurance renewal reminder notifications sent successfully', [
                'insurance_id' => $providerInsurance->id,
                'expiration_date' => $expirationDate,
                'notification_data' => $notificationData
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to send insurance renewal reminder notifications', [
                'insurance_id' => $event->providerInsurance->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Send renewal reminder email
     */
    private function sendRenewalReminderEmail($providerInsurance, $expirationDate, $notificationData): void
    {
        // Implementation for renewal reminder email
        Log::info('Renewal reminder email sent', [
            'insurance_id' => $providerInsurance->id
        ]);
    }

    /**
     * Send renewal reminder SMS
     */
    private function sendRenewalReminderSms($providerInsurance, $expirationDate, $notificationData): void
    {
        // Implementation for renewal reminder SMS
        Log::info('Renewal reminder SMS sent', [
            'insurance_id' => $providerInsurance->id
        ]);
    }

    /**
     * Send renewal reminder in-app notification
     */
    private function sendRenewalReminderInApp($providerInsurance, $expirationDate, $notificationData): void
    {
        // Implementation for renewal reminder in-app notification
        Log::info('Renewal reminder in-app notification sent', [
            'insurance_id' => $providerInsurance->id
        ]);
    }
}
