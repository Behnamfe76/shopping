<?php

namespace Fereydooni\Shopping\App\Listeners\ProviderRating;

use Fereydooni\Shopping\App\Events\ProviderRating\ProviderRatingCreated;
use Fereydooni\Shopping\App\Events\ProviderRating\ProviderRatingUpdated;
use Fereydooni\Shopping\App\Events\ProviderRating\ProviderRatingApproved;
use Fereydooni\Shopping\App\Events\ProviderRating\ProviderRatingRejected;
use Fereydooni\Shopping\App\Events\ProviderRating\ProviderRatingFlagged;
use Fereydooni\Shopping\App\Events\ProviderRating\ProviderRatingVerified;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class LogProviderRatingActivity implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Handle the event.
     */
    public function handle($event): void
    {
        try {
            $rating = $event->rating;
            $user = Auth::user();
            $userId = $user ? $user->id : null;
            $userName = $user ? $user->name : 'System';

            switch (true) {
                case $event instanceof ProviderRatingCreated:
                    $this->logRatingCreated($rating, $userId, $userName);
                    break;
                case $event instanceof ProviderRatingUpdated:
                    $this->logRatingUpdated($rating, $userId, $userName);
                    break;
                case $event instanceof ProviderRatingApproved:
                    $this->logRatingApproved($rating, $userId, $userName);
                    break;
                case $event instanceof ProviderRatingRejected:
                    $this->logRatingRejected($rating, $userId, $userName);
                    break;
                case $event instanceof ProviderRatingFlagged:
                    $this->logRatingFlagged($rating, $userId, $userName);
                    break;
                case $event instanceof ProviderRatingVerified:
                    $this->logRatingVerified($rating, $userId, $userName);
                    break;
            }

        } catch (\Exception $e) {
            Log::error('Failed to log provider rating activity', [
                'event' => get_class($event),
                'rating_id' => $event->rating->id ?? null,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    /**
     * Log rating created activity.
     */
    protected function logRatingCreated($rating, $userId, $userName): void
    {
        $logData = [
            'action' => 'rating_created',
            'rating_id' => $rating->id,
            'provider_id' => $rating->provider_id,
            'user_id' => $rating->user_id,
            'rating_value' => $rating->rating_value,
            'category' => $rating->category,
            'status' => $rating->status,
            'performed_by' => $userId,
            'performed_by_name' => $userName,
            'timestamp' => now(),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent()
        ];

        Log::info('Provider rating created', $logData);
        $this->storeActivityLog($logData);
    }

    /**
     * Log rating updated activity.
     */
    protected function logRatingUpdated($rating, $userId, $userName): void
    {
        $logData = [
            'action' => 'rating_updated',
            'rating_id' => $rating->id,
            'provider_id' => $rating->provider_id,
            'user_id' => $rating->user_id,
            'rating_value' => $rating->rating_value,
            'category' => $rating->category,
            'status' => $rating->status,
            'performed_by' => $userId,
            'performed_by_name' => $userName,
            'timestamp' => now(),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent()
        ];

        Log::info('Provider rating updated', $logData);
        $this->storeActivityLog($logData);
    }

    /**
     * Log rating approved activity.
     */
    protected function logRatingApproved($rating, $userId, $userName): void
    {
        $logData = [
            'action' => 'rating_approved',
            'rating_id' => $rating->id,
            'provider_id' => $rating->provider_id,
            'user_id' => $rating->user_id,
            'rating_value' => $rating->rating_value,
            'category' => $rating->category,
            'status' => $rating->status,
            'performed_by' => $userId,
            'performed_by_name' => $userName,
            'timestamp' => now(),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent()
        ];

        Log::info('Provider rating approved', $logData);
        $this->storeActivityLog($logData);
    }

    /**
     * Log rating rejected activity.
     */
    protected function logRatingRejected($rating, $userId, $userName): void
    {
        $logData = [
            'action' => 'rating_rejected',
            'rating_id' => $rating->id,
            'provider_id' => $rating->provider_id,
            'user_id' => $rating->user_id,
            'rating_value' => $rating->rating_value,
            'category' => $rating->category,
            'status' => $rating->status,
            'performed_by' => $userId,
            'performed_by_name' => $userName,
            'timestamp' => now(),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent()
        ];

        Log::info('Provider rating rejected', $logData);
        $this->storeActivityLog($logData);
    }

    /**
     * Log rating flagged activity.
     */
    protected function logRatingFlagged($rating, $userId, $userName): void
    {
        $logData = [
            'action' => 'rating_flagged',
            'rating_id' => $rating->id,
            'provider_id' => $rating->provider_id,
            'user_id' => $rating->user_id,
            'rating_value' => $rating->rating_value,
            'category' => $rating->category,
            'status' => $rating->status,
            'performed_by' => $userId,
            'performed_by_name' => $userName,
            'timestamp' => now(),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent()
        ];

        Log::info('Provider rating flagged', $logData);
        $this->storeActivityLog($logData);
    }

    /**
     * Log rating verified activity.
     */
    protected function logRatingVerified($rating, $userId, $userName): void
    {
        $logData = [
            'action' => 'rating_verified',
            'rating_id' => $rating->id,
            'provider_id' => $rating->provider_id,
            'user_id' => $rating->user_id,
            'rating_value' => $rating->rating_value,
            'category' => $rating->category,
            'status' => $rating->status,
            'performed_by' => $userId,
            'performed_by_name' => $userName,
            'timestamp' => now(),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent()
        ];

        Log::info('Provider rating verified', $logData);
        $this->storeActivityLog($logData);
    }

    /**
     * Store activity log in database if activity logging is enabled.
     */
    protected function storeActivityLog(array $logData): void
    {
        try {
            // Check if activity logging is enabled
            if (!config('logging.enable_activity_logs', false)) {
                return;
            }

            // Store in activity logs table if it exists
            // This would depend on your specific implementation
            // ActivityLog::create($logData);

            Log::info('Activity log stored', [
                'action' => $logData['action'],
                'rating_id' => $logData['rating_id']
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to store activity log', [
                'error' => $e->getMessage(),
                'log_data' => $logData
            ]);
        }
    }
}
