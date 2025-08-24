<?php

namespace App\Listeners;

use App\Events\PerformanceGradeChanged;
use App\Notifications\PerformanceGradeChangedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class SendGradeChangeNotification implements ShouldQueue
{
    use InteractsWithQueue;

    public $queue = 'notifications';

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
    public function handle(PerformanceGradeChanged $event): void
    {
        try {
            Log::info('Sending performance grade change notification', [
                'provider_id' => $event->providerPerformance->provider_id,
                'old_grade' => $event->oldGrade,
                'new_grade' => $event->newGrade,
                'reason' => $event->reason
            ]);

            // Get users who should receive this notification
            $recipients = $this->getNotificationRecipients($event);

            // Send notifications
            foreach ($recipients as $recipient) {
                $recipient->notify(new PerformanceGradeChangedNotification(
                    $event->providerPerformance,
                    $event->oldGrade,
                    $event->newGrade,
                    $event->reason
                ));
            }

            Log::info('Performance grade change notifications sent successfully', [
                'recipients_count' => count($recipients),
                'provider_id' => $event->providerPerformance->provider_id
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to send performance grade change notifications', [
                'error' => $e->getMessage(),
                'provider_id' => $event->providerPerformance->provider_id,
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    /**
     * Get users who should receive the notification
     */
    private function getNotificationRecipients(PerformanceGradeChanged $event): array
    {
        // This would typically query the database for users with appropriate permissions
        // For now, return an empty array - implement based on your user management system

        $recipients = [];

        // Example: Get users who manage this provider
        // $recipients = User::whereHas('managedProviders', function($query) use ($event) {
        //     $query->where('id', $event->providerPerformance->provider_id);
        // })->get();

        // Example: Get users with grade change notification permissions
        // $recipients = User::permission('receive-grade-change-notifications')->get();

        // Example: Get specific roles
        // $recipients = User::role(['manager', 'supervisor'])->get();

        return $recipients;
    }

    /**
     * Handle a job failure.
     */
    public function failed(PerformanceGradeChanged $event, \Throwable $exception): void
    {
        Log::error('Failed to send performance grade change notification', [
            'event' => $event,
            'error' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString()
        ]);
    }
}
