<?php

namespace Fereydooni\Shopping\app\Listeners;

use Fereydooni\Shopping\app\Events\Provider\PrimaryLocationChanged;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Exception;

class SendPrimaryLocationChangedNotification implements ShouldQueue
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
    public function handle(PrimaryLocationChanged $event): void
    {
        try {
            $newPrimaryLocation = $event->newPrimaryLocation;
            $previousPrimaryLocation = $event->previousPrimaryLocation;
            $user = $event->user;
            $changeData = $event->changeData;

            // Send email notification to provider
            $this->sendEmailNotification($newPrimaryLocation, $previousPrimaryLocation, $user, $changeData);

            // Send SMS notification if phone is available
            if ($newPrimaryLocation->phone) {
                $this->sendSMSNotification($newPrimaryLocation, $previousPrimaryLocation, $user, $changeData);
            }

            // Send in-app notification
            $this->sendInAppNotification($newPrimaryLocation, $previousPrimaryLocation, $user, $changeData);

            Log::info('Primary location changed notifications sent successfully', [
                'new_location_id' => $newPrimaryLocation->id,
                'provider_id' => $newPrimaryLocation->provider_id,
                'change_type' => $event->determineChangeType(),
                'user_id' => $user?->id
            ]);

        } catch (Exception $e) {
            Log::error('Failed to send primary location changed notifications', [
                'new_location_id' => $event->newPrimaryLocation->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            // Re-throw the exception to mark the job as failed
            throw $e;
        }
    }

    /**
     * Send email notification
     */
    protected function sendEmailNotification($newLocation, $previousLocation, $user, array $changeData): void
    {
        try {
            // Get provider email
            $providerEmail = $newLocation->provider->email ?? null;

            if (!$providerEmail) {
                Log::warning('No provider email found for primary location change notification', [
                    'location_id' => $newLocation->id,
                    'provider_id' => $newLocation->provider_id
                ]);
                return;
            }

            // Send email notification
            // Note: You would implement your actual email notification logic here
            // For example: Mail::to($providerEmail)->send(new PrimaryLocationChangedMail($newLocation, $previousLocation, $user));

            Log::info('Email notification sent for primary location change', [
                'new_location_id' => $newLocation->id,
                'provider_email' => $providerEmail,
                'change_type' => $changeData['change_type'] ?? 'unknown'
            ]);

        } catch (Exception $e) {
            Log::error('Failed to send email notification for primary location change', [
                'new_location_id' => $newLocation->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Send SMS notification
     */
    protected function sendSMSNotification($newLocation, $previousLocation, $user, array $changeData): void
    {
        try {
            $phone = $newLocation->phone;

            if (!$phone) {
                return;
            }

            // Send SMS notification
            // Note: You would implement your actual SMS notification logic here
            // For example: SMS::send($phone, new PrimaryLocationChangedSMS($newLocation, $previousLocation, $user));

            Log::info('SMS notification sent for primary location change', [
                'new_location_id' => $newLocation->id,
                'phone' => $phone,
                'change_type' => $changeData['change_type'] ?? 'unknown'
            ]);

        } catch (Exception $e) {
            Log::error('Failed to send SMS notification for primary location change', [
                'new_location_id' => $newLocation->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Send in-app notification
     */
    protected function sendInAppNotification($newLocation, $previousLocation, $user, array $changeData): void
    {
        try {
            // Get users to notify (provider admins, location managers, etc.)
            $usersToNotify = $this->getUsersToNotify($newLocation, $previousLocation);

            foreach ($usersToNotify as $notifyUser) {
                // Send in-app notification
                // Note: You would implement your actual in-app notification logic here
                // For example: $notifyUser->notify(new PrimaryLocationChangedNotification($newLocation, $previousLocation, $user));

                Log::info('In-app notification sent for primary location change', [
                    'new_location_id' => $newLocation->id,
                    'user_id' => $notifyUser->id,
                    'change_type' => $changeData['change_type'] ?? 'unknown'
                ]);
            }

        } catch (Exception $e) {
            Log::error('Failed to send in-app notification for primary location change', [
                'new_location_id' => $newLocation->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Get users to notify about primary location change
     */
    protected function getUsersToNotify($newLocation, $previousLocation): array
    {
        $users = [];

        try {
            // Add provider owner/admin users
            if ($newLocation->provider && $newLocation->provider->users) {
                $users = array_merge($users, $newLocation->provider->users->toArray());
            }

            // Add new primary location managers if any
            if ($newLocation->contact_email) {
                // Find user by contact email
                $contactUser = \App\Models\User::where('email', $newLocation->contact_email)->first();
                if ($contactUser) {
                    $users[] = $contactUser;
                }
            }

            // Add previous primary location managers if any
            if ($previousLocation && $previousLocation->contact_email) {
                $previousContactUser = \App\Models\User::where('email', $previousLocation->contact_email)->first();
                if ($previousContactUser) {
                    $users[] = $previousContactUser;
                }
            }

            // Remove duplicates
            $users = array_unique($users, SORT_REGULAR);

        } catch (Exception $e) {
            Log::error('Failed to get users to notify for primary location change', [
                'new_location_id' => $newLocation->id,
                'error' => $e->getMessage()
            ]);
        }

        return $users;
    }

    /**
     * Handle a job failure.
     */
    public function failed(PrimaryLocationChanged $event, Exception $exception): void
    {
        Log::error('Primary location changed notification job failed', [
            'new_location_id' => $event->newPrimaryLocation->id,
            'error' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString()
        ]);
    }
}
