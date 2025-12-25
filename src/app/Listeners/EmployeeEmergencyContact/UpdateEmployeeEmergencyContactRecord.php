<?php

namespace App\Listeners\EmployeeEmergencyContact;

use App\Events\EmployeeEmergencyContact\EmployeeEmergencyContactCreated;
use App\Events\EmployeeEmergencyContact\EmployeeEmergencyContactDeleted;
use App\Events\EmployeeEmergencyContact\EmployeeEmergencyContactSetPrimary;
use App\Events\EmployeeEmergencyContact\EmployeeEmergencyContactUpdated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class UpdateEmployeeEmergencyContactRecord implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Handle the event.
     */
    public function handle($event): void
    {
        try {
            $contact = $event->contact;
            $employeeId = $contact->employee_id;

            // Clear employee emergency contact cache
            $this->clearEmployeeContactCache($employeeId);

            // Update employee emergency contact count
            $this->updateEmployeeContactCount($employeeId);

            // Log the action
            Log::info('Employee emergency contact record updated', [
                'event' => get_class($event),
                'contact_id' => $contact->id,
                'employee_id' => $employeeId,
                'action' => $this->getActionFromEvent($event),
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to update employee emergency contact record', [
                'event' => get_class($event),
                'contact_id' => $event->contact->id ?? null,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Clear employee emergency contact cache.
     */
    private function clearEmployeeContactCache(int $employeeId): void
    {
        $cacheKeys = [
            "employee_emergency_contacts_{$employeeId}",
            "employee_primary_contact_{$employeeId}",
            "employee_active_contacts_{$employeeId}",
        ];

        foreach ($cacheKeys as $key) {
            Cache::forget($key);
        }
    }

    /**
     * Update employee emergency contact count.
     */
    private function updateEmployeeContactCount(int $employeeId): void
    {
        // This could be updated to use a counter table or cache
        // For now, we'll just clear the cache to force a fresh count
        Cache::forget("employee_contact_count_{$employeeId}");
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
     * Handle a job failure.
     */
    public function failed($event, \Throwable $exception): void
    {
        Log::error('Update employee emergency contact record job failed', [
            'event' => get_class($event),
            'contact_id' => $event->contact->id ?? null,
            'error' => $exception->getMessage(),
        ]);
    }
}
