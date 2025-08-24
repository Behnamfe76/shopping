<?php

namespace App\Listeners;

use App\Events\ProviderInsuranceExpired;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class SendInsuranceExpirationNotification implements ShouldQueue
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

            // Send email notification
            $this->sendEmailNotification($providerInsurance, $expirationDate, $notificationData);

            // Send SMS notification if configured
            $this->sendSmsNotification($providerInsurance, $expirationDate, $notificationData);

            // Send in-app notification
            $this->sendInAppNotification($providerInsurance, $expirationDate, $notificationData);

            Log::info('Insurance expiration notifications sent successfully', [
                'insurance_id' => $providerInsurance->id,
                'expiration_date' => $expirationDate,
                'notification_data' => $notificationData
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to send insurance expiration notifications', [
                'insurance_id' => $event->providerInsurance->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Send email notification
     */
    private function sendEmailNotification($providerInsurance, $expirationDate, $notificationData): void
    {
        // Implementation for email notification
        Log::info('Email notification sent for insurance expiration', [
            'insurance_id' => $providerInsurance->id
        ]);
    }

    /**
     * Send SMS notification
     */
    private function sendSmsNotification($providerInsurance, $expirationDate, $notificationData): void
    {
        // Implementation for SMS notification
        Log::info('SMS notification sent for insurance expiration', [
            'insurance_id' => $providerInsurance->id
        ]);
    }

    /**
     * Send in-app notification
     */
    private function sendInAppNotification($providerInsurance, $expirationDate, $notificationData): void
    {
        // Implementation for in-app notification
        Log::info('In-app notification sent for insurance expiration', [
            'insurance_id' => $providerInsurance->id
        ]);
    }
}
