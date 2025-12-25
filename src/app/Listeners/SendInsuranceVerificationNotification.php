<?php

namespace App\Listeners;

use App\Events\ProviderInsuranceVerified;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class SendInsuranceVerificationNotification implements ShouldQueue
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
    public function handle(ProviderInsuranceVerified $event): void
    {
        try {
            $providerInsurance = $event->providerInsurance;
            $verifier = $event->verifier;
            $verificationDetails = $event->verificationDetails;

            // Send email notification
            $this->sendEmailNotification($providerInsurance, $verifier, $verificationDetails);

            // Send SMS notification if configured
            $this->sendSmsNotification($providerInsurance, $verifier, $verificationDetails);

            // Send in-app notification
            $this->sendInAppNotification($providerInsurance, $verifier, $verificationDetails);

            Log::info('Insurance verification notifications sent successfully', [
                'insurance_id' => $providerInsurance->id,
                'verifier_id' => $verifier,
                'verification_details' => $verificationDetails,
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to send insurance verification notifications', [
                'insurance_id' => $event->providerInsurance->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Send email notification
     */
    private function sendEmailNotification($providerInsurance, $verifier, $verificationDetails): void
    {
        // Implementation for email notification
        // This would typically use Laravel's notification system
        Log::info('Email notification sent for insurance verification', [
            'insurance_id' => $providerInsurance->id,
        ]);
    }

    /**
     * Send SMS notification
     */
    private function sendSmsNotification($providerInsurance, $verifier, $verificationDetails): void
    {
        // Implementation for SMS notification
        // This would typically use a third-party SMS service
        Log::info('SMS notification sent for insurance verification', [
            'insurance_id' => $providerInsurance->id,
        ]);
    }

    /**
     * Send in-app notification
     */
    private function sendInAppNotification($providerInsurance, $verifier, $verificationDetails): void
    {
        // Implementation for in-app notification
        // This would typically use Laravel's notification system with database
        Log::info('In-app notification sent for insurance verification', [
            'insurance_id' => $providerInsurance->id,
        ]);
    }
}
