<?php

namespace App\Listeners;

use App\Events\ProviderPerformanceCreated;
use App\Events\ProviderPerformanceUpdated;
use App\Events\PerformanceAlertGenerated;
use App\Notifications\ProviderPerformanceAlert;
use App\Notifications\ProviderPerformanceCreated as PerformanceCreatedNotification;
use App\Notifications\ProviderPerformanceUpdated as PerformanceUpdatedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;

class SendPerformanceAlertNotification implements ShouldQueue
{
    use InteractsWithQueue;

    public $queue = 'notifications';
    public $connection = 'redis';
    public $tries = 3;
    public $timeout = 60;

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
    public function handle($event): void
    {
        try {
            $this->processEvent($event);
        } catch (\Exception $e) {
            Log::error('Failed to process performance alert notification', [
                'event' => get_class($event),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            // Re-throw the exception to trigger job failure handling
            throw $e;
        }
    }

    /**
     * Process different types of events.
     */
    protected function processEvent($event): void
    {
        switch (get_class($event)) {
            case ProviderPerformanceCreated::class:
                $this->handlePerformanceCreated($event);
                break;
            case ProviderPerformanceUpdated::class:
                $this->handlePerformanceUpdated($event);
                break;
            case PerformanceAlertGenerated::class:
                $this->handlePerformanceAlert($event);
                break;
            default:
                Log::warning('Unknown event type for performance alert notification', [
                    'event_class' => get_class($event),
                ]);
        }
    }

    /**
     * Handle performance created event.
     */
    protected function handlePerformanceCreated(ProviderPerformanceCreated $event): void
    {
        $performance = $event->providerPerformance;

        // Send notification to relevant stakeholders
        $this->sendPerformanceCreatedNotifications($performance);

        // Check for immediate alerts
        if ($performance->needsAttention()) {
            $this->sendImmediateAlertNotifications($performance, 'created');
        }

        // Log the notification
        Log::info('Performance created notification sent', [
            'performance_id' => $performance->id,
            'provider_id' => $performance->provider_id,
            'alerts_generated' => $performance->needsAttention(),
        ]);
    }

    /**
     * Handle performance updated event.
     */
    protected function handlePerformanceUpdated(ProviderPerformanceUpdated $event): void
    {
        $performance = $event->providerPerformance;
        $changes = $event->changes ?? [];

        // Send notification to relevant stakeholders
        $this->sendPerformanceUpdatedNotifications($performance, $changes);

        // Check for immediate alerts
        if ($performance->needsAttention()) {
            $this->sendImmediateAlertNotifications($performance, 'updated');
        }

        // Check for grade changes
        if (isset($changes['performance_grade'])) {
            $this->sendGradeChangeNotifications($performance, $changes['performance_grade']);
        }

        // Log the notification
        Log::info('Performance updated notification sent', [
            'performance_id' => $performance->id,
            'provider_id' => $performance->provider_id,
            'alerts_generated' => $performance->needsAttention(),
            'grade_changed' => isset($changes['performance_grade']),
        ]);
    }

    /**
     * Handle performance alert event.
     */
    protected function handlePerformanceAlert(PerformanceAlertGenerated $event): void
    {
        $performance = $event->performance;
        $alerts = $event->alerts;

        // Send alert notifications
        $this->sendAlertNotifications($performance, $alerts);

        // Log the alert notification
        Log::info('Performance alert notification sent', [
            'performance_id' => $performance->id,
            'provider_id' => $performance->provider_id,
            'alerts_count' => count($alerts),
            'alerts' => $alerts,
        ]);
    }

    /**
     * Send performance created notifications.
     */
    protected function sendPerformanceCreatedNotifications($performance): void
    {
        // Get relevant users to notify
        $usersToNotify = $this->getUsersToNotify($performance);

        // Send notifications
        foreach ($usersToNotify as $user) {
            try {
                $user->notify(new PerformanceCreatedNotification($performance));
            } catch (\Exception $e) {
                Log::error('Failed to send performance created notification', [
                    'user_id' => $user->id,
                    'performance_id' => $performance->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }
    }

    /**
     * Send performance updated notifications.
     */
    protected function sendPerformanceUpdatedNotifications($performance, array $changes): void
    {
        // Get relevant users to notify
        $usersToNotify = $this->getUsersToNotify($performance);

        // Send notifications
        foreach ($usersToNotify as $user) {
            try {
                $user->notify(new PerformanceUpdatedNotification($performance, $changes));
            } catch (\Exception $e) {
                Log::error('Failed to send performance updated notification', [
                    'user_id' => $user->id,
                    'performance_id' => $performance->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }
    }

    /**
     * Send immediate alert notifications.
     */
    protected function sendImmediateAlertNotifications($performance, string $action): void
    {
        // Get users who should receive immediate alerts
        $immediateAlertUsers = $this->getImmediateAlertUsers($performance);

        // Send immediate alert notifications
        foreach ($immediateAlertUsers as $user) {
            try {
                $user->notify(new ProviderPerformanceAlert($performance, $action));
            } catch (\Exception $e) {
                Log::error('Failed to send immediate alert notification', [
                    'user_id' => $user->id,
                    'performance_id' => $performance->id,
                    'action' => $action,
                    'error' => $e->getMessage(),
                ]);
            }
        }
    }

    /**
     * Send grade change notifications.
     */
    protected function sendGradeChangeNotifications($performance, $oldGrade): void
    {
        // Get users who should be notified of grade changes
        $gradeChangeUsers = $this->getGradeChangeUsers($performance);

        // Send grade change notifications
        foreach ($gradeChangeUsers as $user) {
            try {
                $user->notify(new ProviderPerformanceAlert($performance, 'grade_changed', [
                    'old_grade' => $oldGrade,
                    'new_grade' => $performance->performance_grade?->value,
                ]));
            } catch (\Exception $e) {
                Log::error('Failed to send grade change notification', [
                    'user_id' => $user->id,
                    'performance_id' => $performance->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }
    }

    /**
     * Send alert notifications.
     */
    protected function sendAlertNotifications($performance, array $alerts): void
    {
        // Get users who should receive alert notifications
        $alertUsers = $this->getAlertUsers($performance);

        // Send alert notifications
        foreach ($alertUsers as $user) {
            try {
                $user->notify(new ProviderPerformanceAlert($performance, 'alerts', [
                    'alerts' => $alerts,
                ]));
            } catch (\Exception $e) {
                Log::error('Failed to send alert notification', [
                    'user_id' => $user->id,
                    'performance_id' => $performance->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }
    }

    /**
     * Get users to notify for performance events.
     */
    protected function getUsersToNotify($performance): array
    {
        $users = [];

        try {
            // Get admin users
            $adminUsers = DB::table('users')
                ->join('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
                ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
                ->where('roles.name', 'admin')
                ->select('users.*')
                ->get();

            $users = array_merge($users, $adminUsers->toArray());

            // Get manager users
            $managerUsers = DB::table('users')
                ->join('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
                ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
                ->where('roles.name', 'manager')
                ->select('users.*')
                ->get();

            $users = array_merge($users, $managerUsers->toArray());

            // Get analyst users
            $analystUsers = DB::table('users')
                ->join('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
                ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
                ->where('roles.name', 'analyst')
                ->select('users.*')
                ->get();

            $users = array_merge($users, $analystUsers->toArray());

        } catch (\Exception $e) {
            Log::error('Failed to get users to notify', [
                'error' => $e->getMessage(),
            ]);
        }

        return array_unique($users, SORT_REGULAR);
    }

    /**
     * Get users who should receive immediate alerts.
     */
    protected function getImmediateAlertUsers($performance): array
    {
        $users = [];

        try {
            // Get supervisor users for immediate alerts
            $supervisorUsers = DB::table('users')
                ->join('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
                ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
                ->whereIn('roles.name', ['supervisor', 'manager', 'admin'])
                ->select('users.*')
                ->get();

            $users = array_merge($users, $supervisorUsers->toArray());

        } catch (\Exception $e) {
            Log::error('Failed to get immediate alert users', [
                'error' => $e->getMessage(),
            ]);
        }

        return array_unique($users, SORT_REGULAR);
    }

    /**
     * Get users who should be notified of grade changes.
     */
    protected function getGradeChangeUsers($performance): array
    {
        $users = [];

        try {
            // Get quality assurance users
            $qaUsers = DB::table('users')
                ->join('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
                ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
                ->whereIn('roles.name', ['qa_specialist', 'quality_analyst', 'manager', 'admin'])
                ->select('users.*')
                ->get();

            $users = array_merge($users, $qaUsers->toArray());

        } catch (\Exception $e) {
            Log::error('Failed to get grade change users', [
                'error' => $e->getMessage(),
            ]);
        }

        return array_unique($users, SORT_REGULAR);
    }

    /**
     * Get users who should receive alert notifications.
     */
    protected function getAlertUsers($performance): array
    {
        $users = [];

        try {
            // Get all users with alert permissions
            $alertUsers = DB::table('users')
                ->join('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
                ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
                ->whereIn('roles.name', ['admin', 'manager', 'analyst', 'supervisor'])
                ->select('users.*')
                ->get();

            $users = array_merge($users, $alertUsers->toArray());

        } catch (\Exception $e) {
            Log::error('Failed to get alert users', [
                'error' => $e->getMessage(),
            ]);
        }

        return array_unique($users, SORT_REGULAR);
    }

    /**
     * Handle a job failure.
     */
    public function failed($event, \Throwable $exception): void
    {
        Log::error('Performance alert notification job failed', [
            'event' => get_class($event),
            'error' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString(),
        ]);

        // You could implement fallback notification methods here
        // such as sending SMS, Slack messages, etc.
    }

    /**
     * Get the tags that should be assigned to the job.
     */
    public function tags(): array
    {
        return [
            'provider_performance',
            'notifications',
            'alerts',
        ];
    }

    /**
     * Get the retry delay for the job.
     */
    public function retryAfter(): int
    {
        return 60; // 1 minute
    }

    /**
     * Get the maximum number of attempts for the job.
     */
    public function maxAttempts(): int
    {
        return 3;
    }

    /**
     * Get the job's unique identifier.
     */
    public function uniqueId(): string
    {
        return 'performance_alert_notification_' . uniqid();
    }

    /**
     * Get the job's timeout.
     */
    public function timeout(): int
    {
        return 60; // 1 minute
    }

    /**
     * Get the job's connection.
     */
    public function viaConnection(): string
    {
        return 'redis';
    }

    /**
     * Get the job's queue.
     */
    public function viaQueue(): string
    {
        return 'notifications';
    }
}
