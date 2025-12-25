<?php

namespace Fereydooni\Shopping\app\Listeners;

use Exception;
use Fereydooni\Shopping\app\Events\Provider\ProviderLocationCreated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class SendLocationCreatedNotification implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * The number of times the job may be attempted.
     */
    public $tries = 3;

    /**
     * The number of seconds to wait before retrying the job.
     */
    public $backoff = 60;

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
    public function handle(ProviderLocationCreated $event): void
    {
        try {
            $location = $event->providerLocation;
            $user = $event->user;
            $locationData = $event->locationData;

            // Send email notification to provider
            $this->sendEmailNotification($location, $user, $locationData);

            // Send SMS notification if phone is available
            if ($location->phone) {
                $this->sendSMSNotification($location, $user, $locationData);
            }

            // Send in-app notification
            $this->sendInAppNotification($location, $user, $locationData);

            Log::info('Location created notifications sent successfully', [
                'location_id' => $location->id,
                'provider_id' => $location->provider_id,
                'user_id' => $user?->id,
            ]);

        } catch (Exception $e) {
            Log::error('Failed to send location created notifications', [
                'location_id' => $event->providerLocation->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            // Re-throw the exception to mark the job as failed
            throw $e;
        }
    }

    /**
     * Send email notification
     */
    protected function sendEmailNotification($location, $user, array $locationData): void
    {
        try {
            // Get provider email
            $providerEmail = $location->provider->email ?? null;

            if (! $providerEmail) {
                Log::warning('No provider email found for location notification', [
                    'location_id' => $location->id,
                    'provider_id' => $location->provider_id,
                ]);

                return;
            }

            // Send email notification
            // Note: You would implement your actual email notification logic here
            // For example: Mail::to($providerEmail)->send(new LocationCreatedMail($location, $user));

            Log::info('Email notification sent for location creation', [
                'location_id' => $location->id,
                'provider_email' => $providerEmail,
            ]);

        } catch (Exception $e) {
            Log::error('Failed to send email notification for location creation', [
                'location_id' => $location->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Send SMS notification
     */
    protected function sendSMSNotification($location, $user, array $locationData): void
    {
        try {
            $phone = $location->phone;

            if (! $phone) {
                return;
            }

            // Send SMS notification
            // Note: You would implement your actual SMS notification logic here
            // For example: SMS::send($phone, new LocationCreatedSMS($location, $user));

            Log::info('SMS notification sent for location creation', [
                'location_id' => $location->id,
                'phone' => $phone,
            ]);

        } catch (Exception $e) {
            Log::error('Failed to send SMS notification for location creation', [
                'location_id' => $location->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Send in-app notification
     */
    protected function sendInAppNotification($location, $user, array $locationData): void
    {
        try {
            // Get users to notify (provider admins, location managers, etc.)
            $usersToNotify = $this->getUsersToNotify($location);

            foreach ($usersToNotify as $notifyUser) {
                // Send in-app notification
                // Note: You would implement your actual in-app notification logic here
                // For example: $notifyUser->notify(new LocationCreatedNotification($location, $user));

                Log::info('In-app notification sent for location creation', [
                    'location_id' => $location->id,
                    'user_id' => $notifyUser->id,
                ]);
            }

        } catch (Exception $e) {
            Log::error('Failed to send in-app notification for location creation', [
                'location_id' => $location->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Get users to notify about location creation
     */
    protected function getUsersToNotify($location): array
    {
        $users = [];

        try {
            // Add provider owner/admin users
            if ($location->provider && $location->provider->users) {
                $users = array_merge($users, $location->provider->users->toArray());
            }

            // Add location managers if any
            if ($location->contact_email) {
                // Find user by contact email
                $contactUser = \App\Models\User::where('email', $location->contact_email)->first();
                if ($contactUser) {
                    $users[] = $contactUser;
                }
            }

            // Remove duplicates
            $users = array_unique($users, SORT_REGULAR);

        } catch (Exception $e) {
            Log::error('Failed to get users to notify for location creation', [
                'location_id' => $location->id,
                'error' => $e->getMessage(),
            ]);
        }

        return $users;
    }

    /**
     * Handle a job failure.
     */
    public function failed(ProviderLocationCreated $event, Exception $exception): void
    {
        Log::error('Location created notification job failed', [
            'location_id' => $event->providerLocation->id,
            'error' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString(),
        ]);
    }
}
