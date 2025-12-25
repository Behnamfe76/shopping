<?php

namespace App\Listeners\EmployeeEmergencyContact;

use App\Events\EmployeeEmergencyContact\EmployeeEmergencyContactCreated;
use App\Events\EmployeeEmergencyContact\EmployeeEmergencyContactDeleted;
use App\Events\EmployeeEmergencyContact\EmployeeEmergencyContactSetPrimary;
use App\Events\EmployeeEmergencyContact\EmployeeEmergencyContactUpdated;
use App\Notifications\EmployeeEmergencyContact\EmergencyContactAdded;
use App\Notifications\EmployeeEmergencyContact\EmergencyContactRemoved;
use App\Notifications\EmployeeEmergencyContact\EmergencyContactSetPrimary;
use App\Notifications\EmployeeEmergencyContact\EmergencyContactUpdated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class SendEmergencyContactNotification implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Handle the event.
     */
    public function handle($event): void
    {
        try {
            $employee = $event->contact->employee;

            if (! $employee) {
                Log::warning('Employee not found for emergency contact notification', [
                    'contact_id' => $event->contact->id,
                    'employee_id' => $event->contact->employee_id,
                ]);

                return;
            }

            switch (true) {
                case $event instanceof EmployeeEmergencyContactCreated:
                    $employee->notify(new EmergencyContactAdded($event->contact));
                    break;

                case $event instanceof EmployeeEmergencyContactUpdated:
                    $employee->notify(new EmergencyContactUpdated($event->contact, $event->changes));
                    break;

                case $event instanceof EmployeeEmergencyContactSetPrimary:
                    $employee->notify(new EmergencyContactSetPrimary($event->contact, $event->previousPrimary));
                    break;

                case $event instanceof EmployeeEmergencyContactDeleted:
                    $employee->notify(new EmergencyContactRemoved($event->contact));
                    break;
            }
        } catch (\Exception $e) {
            Log::error('Failed to send emergency contact notification', [
                'event' => get_class($event),
                'contact_id' => $event->contact->id ?? null,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed($event, \Throwable $exception): void
    {
        Log::error('Emergency contact notification job failed', [
            'event' => get_class($event),
            'contact_id' => $event->contact->id ?? null,
            'error' => $exception->getMessage(),
        ]);
    }
}
