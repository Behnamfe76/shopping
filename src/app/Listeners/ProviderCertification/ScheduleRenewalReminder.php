<?php

namespace App\Listeners\ProviderCertification;

use App\Events\ProviderCertification\ProviderCertificationCreated;
use App\Events\ProviderCertification\ProviderCertificationRenewed;
use App\Events\ProviderCertification\ProviderCertificationUpdated;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Queue;

class ScheduleRenewalReminder implements ShouldQueue
{
    use InteractsWithQueue;

    public $queue = 'reminders';

    /**
     * Handle the event.
     */
    public function handle($event): void
    {
        try {
            $certification = $event->certification;

            switch (get_class($event)) {
                case ProviderCertificationCreated::class:
                    $this->scheduleRemindersForNewCertification($certification);
                    break;

                case ProviderCertificationUpdated::class:
                    $this->rescheduleRemindersForUpdatedCertification($certification, $event->changes ?? []);
                    break;

                case ProviderCertificationRenewed::class:
                    $this->scheduleRemindersForRenewedCertification($certification);
                    break;
            }

            Log::info('Renewal reminders scheduled successfully', [
                'event' => get_class($event),
                'certification_id' => $certification->id,
                'provider_id' => $certification->provider_id,
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to schedule renewal reminders', [
                'event' => get_class($event),
                'certification_id' => $event->certification->id ?? null,
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    /**
     * Schedule reminders for a new certification.
     */
    private function scheduleRemindersForNewCertification($certification): void
    {
        if (! $this->shouldScheduleReminders($certification)) {
            return;
        }

        $expiryDate = Carbon::parse($certification->expiry_date);
        $now = Carbon::now();

        // Schedule reminders at different intervals
        $this->scheduleReminder($certification, 90, '90_days_before_expiry');
        $this->scheduleReminder($certification, 60, '60_days_before_expiry');
        $this->scheduleReminder($certification, 30, '30_days_before_expiry');
        $this->scheduleReminder($certification, 14, '14_days_before_expiry');
        $this->scheduleReminder($certification, 7, '7_days_before_expiry');
        $this->scheduleReminder($certification, 1, '1_day_before_expiry');
        $this->scheduleReminder($certification, 0, 'expiry_day');

        // Schedule post-expiry reminders
        $this->scheduleReminder($certification, -1, '1_day_after_expiry');
        $this->scheduleReminder($certification, -7, '7_days_after_expiry');
        $this->scheduleReminder($certification, -30, '30_days_after_expiry');
    }

    /**
     * Reschedule reminders for an updated certification.
     */
    private function rescheduleRemindersForUpdatedCertification($certification, array $changes): void
    {
        // Only reschedule if expiry date changed
        if (! isset($changes['expiry_date'])) {
            return;
        }

        // Cancel existing reminders
        $this->cancelExistingReminders($certification);

        // Schedule new reminders
        $this->scheduleRemindersForNewCertification($certification);
    }

    /**
     * Schedule reminders for a renewed certification.
     */
    private function scheduleRemindersForRenewedCertification($certification): void
    {
        // Cancel existing reminders
        $this->cancelExistingReminders($certification);

        // Schedule new reminders with new expiry date
        $this->scheduleRemindersForNewCertification($certification);
    }

    /**
     * Schedule a specific reminder.
     */
    private function scheduleReminder($certification, int $daysOffset, string $reminderType): void
    {
        $expiryDate = Carbon::parse($certification->expiry_date);
        $reminderDate = $expiryDate->copy()->addDays($daysOffset);

        // Don't schedule past reminders
        if ($reminderDate->isPast()) {
            return;
        }

        $delay = $reminderDate->diffInSeconds(now());

        Queue::later($delay, function () use ($certification, $reminderType) {
            $this->sendRenewalReminder($certification, $reminderType);
        });

        // Store reminder schedule in database for tracking
        $this->storeReminderSchedule($certification, $reminderType, $reminderDate);
    }

    /**
     * Send the actual renewal reminder.
     */
    private function sendRenewalReminder($certification, string $reminderType): void
    {
        try {
            $provider = $certification->provider;

            if (! $provider) {
                Log::warning('Provider not found for renewal reminder', [
                    'certification_id' => $certification->id,
                    'reminder_type' => $reminderType,
                ]);

                return;
            }

            // Send notification based on reminder type
            switch ($reminderType) {
                case '90_days_before_expiry':
                    $provider->notify(new \App\Notifications\ProviderCertification\CertificationExpiring($certification, 90));
                    break;

                case '60_days_before_expiry':
                    $provider->notify(new \App\Notifications\ProviderCertification\CertificationExpiring($certification, 60));
                    break;

                case '30_days_before_expiry':
                    $provider->notify(new \App\Notifications\ProviderCertification\CertificationExpiring($certification, 30));
                    break;

                case '14_days_before_expiry':
                    $provider->notify(new \App\Notifications\ProviderCertification\CertificationExpiring($certification, 14));
                    break;

                case '7_days_before_expiry':
                    $provider->notify(new \App\Notifications\ProviderCertification\CertificationExpiring($certification, 7));
                    break;

                case '1_day_before_expiry':
                    $provider->notify(new \App\Notifications\ProviderCertification\CertificationExpiring($certification, 1));
                    break;

                case 'expiry_day':
                    $provider->notify(new \App\Notifications\ProviderCertification\CertificationExpired($certification));
                    break;

                case '1_day_after_expiry':
                case '7_days_after_expiry':
                case '30_days_after_expiry':
                    $provider->notify(new \App\Notifications\ProviderCertification\CertificationExpired($certification));
                    break;
            }

            // Mark reminder as sent
            $this->markReminderAsSent($certification, $reminderType);

            Log::info('Renewal reminder sent successfully', [
                'certification_id' => $certification->id,
                'reminder_type' => $reminderType,
                'provider_id' => $provider->id,
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to send renewal reminder', [
                'certification_id' => $certification->id,
                'reminder_type' => $reminderType,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Check if reminders should be scheduled for this certification.
     */
    private function shouldScheduleReminders($certification): bool
    {
        // Don't schedule for expired certifications
        if ($certification->status === 'expired') {
            return false;
        }

        // Don't schedule for revoked certifications
        if ($certification->status === 'revoked') {
            return false;
        }

        // Don't schedule for non-recurring certifications if they're expired
        if (! $certification->is_recurring && Carbon::parse($certification->expiry_date)->isPast()) {
            return false;
        }

        return true;
    }

    /**
     * Cancel existing reminders for a certification.
     */
    private function cancelExistingReminders($certification): void
    {
        // This would typically involve canceling queued jobs
        // For now, we'll just log the action
        Log::info('Canceling existing reminders for certification', [
            'certification_id' => $certification->id,
        ]);
    }

    /**
     * Store reminder schedule in database.
     */
    private function storeReminderSchedule($certification, string $reminderType, Carbon $reminderDate): void
    {
        // This would typically store the reminder schedule in a database table
        // For now, we'll just log the action
        Log::info('Storing reminder schedule', [
            'certification_id' => $certification->id,
            'reminder_type' => $reminderType,
            'scheduled_for' => $reminderDate->toISOString(),
        ]);
    }

    /**
     * Mark reminder as sent.
     */
    private function markReminderAsSent($certification, string $reminderType): void
    {
        // This would typically update the reminder schedule in the database
        // For now, we'll just log the action
        Log::info('Marking reminder as sent', [
            'certification_id' => $certification->id,
            'reminder_type' => $reminderType,
            'sent_at' => now()->toISOString(),
        ]);
    }

    /**
     * Handle a job failure.
     */
    public function failed($event, \Throwable $exception): void
    {
        Log::error('Renewal reminder scheduling job failed', [
            'event' => get_class($event),
            'certification_id' => $event->certification->id ?? null,
            'error' => $exception->getMessage(),
        ]);
    }
}
