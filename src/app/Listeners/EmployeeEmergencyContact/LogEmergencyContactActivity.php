<?php

namespace App\Listeners\EmployeeEmergencyContact;

use App\Events\EmployeeEmergencyContact\EmployeeEmergencyContactCreated;
use App\Events\EmployeeEmergencyContact\EmployeeEmergencyContactUpdated;
use App\Events\EmployeeEmergencyContact\EmployeeEmergencyContactSetPrimary;
use App\Events\EmployeeEmergencyContact\EmployeeEmergencyContactDeleted;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class LogEmergencyContactActivity implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Handle the event.
     */
    public function handle($event): void
    {
        try {
            $contact = $event->contact;
            $action = $this->getActionFromEvent($event);
            $details = $this->getEventDetails($event);

            // Log to database for audit trail
            $this->logToDatabase($contact, $action, $details);

            // Log to application log
            Log::info("Emergency contact {$action}", [
                'contact_id' => $contact->id,
                'employee_id' => $contact->employee_id,
                'contact_name' => $contact->contact_name,
                'relationship' => $contact->relationship,
                'action' => $action,
                'details' => $details,
                'user_id' => auth()->id(),
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to log emergency contact activity', [
                'event' => get_class($event),
                'contact_id' => $event->contact->id ?? null,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Log activity to database.
     */
    private function logToDatabase($contact, string $action, array $details): void
    {
        try {
            DB::table('activity_logs')->insert([
                'log_name' => 'emergency_contact',
                'description' => "Emergency contact {$action}",
                'subject_type' => get_class($contact),
                'subject_id' => $contact->id,
                'causer_type' => auth()->user() ? get_class(auth()->user()) : null,
                'causer_id' => auth()->id(),
                'properties' => json_encode($details),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } catch (\Exception $e) {
            Log::warning('Failed to log emergency contact activity to database', [
                'error' => $e->getMessage(),
                'contact_id' => $contact->id,
                'action' => $action,
            ]);
        }
    }

    /**
     * Get action description from event.
     */
    private function getActionFromEvent($event): string
    {
        return match (true) {
            $event instanceof EmployeeEmergencyContactCreated => 'created',
            $event instanceof EmployeeEmergencyContactUpdated => 'updated',
            $event instanceof EmployeeEmergencyContactSetPrimary => 'set as primary',
            $event instanceof EmployeeEmergencyContactDeleted => 'deleted',
            default => 'unknown',
        };
    }

    /**
     * Get event-specific details.
     */
    private function getEventDetails($event): array
    {
        $baseDetails = [
            'contact_id' => $event->contact->id,
            'employee_id' => $event->contact->employee_id,
            'contact_name' => $event->contact->contact_name,
            'relationship' => $event->contact->relationship,
            'is_primary' => $event->contact->is_primary,
        ];

        if ($event instanceof EmployeeEmergencyContactUpdated) {
            $baseDetails['changes'] = $event->changes;
        }

        if ($event instanceof EmployeeEmergencyContactSetPrimary) {
            $baseDetails['previous_primary_id'] = $event->previousPrimary?->id;
            $baseDetails['previous_primary_name'] = $event->previousPrimary?->contact_name;
        }

        return $baseDetails;
    }

    /**
     * Handle a job failure.
     */
    public function failed($event, \Throwable $exception): void
    {
        Log::error('Log emergency contact activity job failed', [
            'event' => get_class($event),
            'contact_id' => $event->contact->id ?? null,
            'error' => $exception->getMessage(),
        ]);
    }
}
