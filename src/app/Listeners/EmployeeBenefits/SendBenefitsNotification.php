<?php

namespace App\Listeners\EmployeeBenefits;

use App\Events\EmployeeBenefits\EmployeeBenefitsCreated;
use App\Events\EmployeeBenefits\EmployeeBenefitsUpdated;
use App\Events\EmployeeBenefits\EmployeeBenefitsEnrolled;
use App\Events\EmployeeBenefits\EmployeeBenefitsTerminated;
use App\Events\EmployeeBenefits\EmployeeBenefitsCancelled;
use App\Events\EmployeeBenefits\EmployeeBenefitsExpiring;
use App\Notifications\EmployeeBenefits\BenefitsEnrollmentCreated;
use App\Notifications\EmployeeBenefits\BenefitsEnrollmentApproved;
use App\Notifications\EmployeeBenefits\BenefitsEnrollmentTerminated;
use App\Notifications\EmployeeBenefits\BenefitsExpiringReminder;
use App\Notifications\EmployeeBenefits\BenefitsRenewalNotice;
use App\Notifications\EmployeeBenefits\BenefitsCostChange;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class SendBenefitsNotification implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Handle the event.
     */
    public function handle(object $event): void
    {
        try {
            match (true) {
                $event instanceof EmployeeBenefitsCreated => $this->handleBenefitsCreated($event),
                $event instanceof EmployeeBenefitsUpdated => $this->handleBenefitsUpdated($event),
                $event instanceof EmployeeBenefitsEnrolled => $this->handleBenefitsEnrolled($event),
                $event instanceof EmployeeBenefitsTerminated => $this->handleBenefitsTerminated($event),
                $event instanceof EmployeeBenefitsCancelled => $this->handleBenefitsCancelled($event),
                $event instanceof EmployeeBenefitsExpiring => $this->handleBenefitsExpiring($event),
                default => Log::info('Unknown EmployeeBenefits event type', ['event' => get_class($event)])
            };
        } catch (\Exception $e) {
            Log::error('Error sending benefits notification', [
                'event' => get_class($event),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    /**
     * Handle benefits created event.
     */
    protected function handleBenefitsCreated(EmployeeBenefitsCreated $event): void
    {
        $benefit = $event->employeeBenefits;
        $employee = $benefit->employee;

        // Notify HR of new benefit enrollment
        if ($employee && $employee->user) {
            $employee->user->notify(new BenefitsEnrollmentCreated($benefit));
        }

        Log::info('Benefits enrollment created notification sent', [
            'benefit_id' => $benefit->id,
            'employee_id' => $benefit->employee_id
        ]);
    }

    /**
     * Handle benefits updated event.
     */
    protected function handleBenefitsUpdated(EmployeeBenefitsUpdated $event): void
    {
        $benefit = $event->employeeBenefits;
        $employee = $benefit->employee;
        $changes = $event->changes;

        // Check if costs changed
        if (isset($changes['premium_amount']) || isset($changes['employee_contribution']) || isset($changes['employer_contribution'])) {
            if ($employee && $employee->user) {
                $employee->user->notify(new BenefitsCostChange($benefit, $changes));
            }
        }

        Log::info('Benefits updated notification sent', [
            'benefit_id' => $benefit->id,
            'employee_id' => $benefit->employee_id,
            'changes' => $changes
        ]);
    }

    /**
     * Handle benefits enrolled event.
     */
    protected function handleBenefitsEnrolled(EmployeeBenefitsEnrolled $event): void
    {
        $benefit = $event->employeeBenefits;
        $employee = $benefit->employee;

        // Notify employee of approved enrollment
        if ($employee && $employee->user) {
            $employee->user->notify(new BenefitsEnrollmentApproved($benefit));
        }

        Log::info('Benefits enrollment approved notification sent', [
            'benefit_id' => $benefit->id,
            'employee_id' => $benefit->employee_id
        ]);
    }

    /**
     * Handle benefits terminated event.
     */
    protected function handleBenefitsTerminated(EmployeeBenefitsTerminated $event): void
    {
        $benefit = $event->employeeBenefits;
        $employee = $benefit->employee;

        // Notify employee of terminated benefits
        if ($employee && $employee->user) {
            $employee->user->notify(new BenefitsEnrollmentTerminated($benefit, $event->reason));
        }

        Log::info('Benefits termination notification sent', [
            'benefit_id' => $benefit->id,
            'employee_id' => $benefit->employee_id,
            'reason' => $event->reason
        ]);
    }

    /**
     * Handle benefits cancelled event.
     */
    protected function handleBenefitsCancelled(EmployeeBenefitsCancelled $event): void
    {
        $benefit = $event->employeeBenefits;
        $employee = $benefit->employee;

        // Notify employee of cancelled benefits
        if ($employee && $employee->user) {
            $employee->user->notify(new BenefitsEnrollmentTerminated($benefit, $event->reason));
        }

        Log::info('Benefits cancellation notification sent', [
            'benefit_id' => $benefit->id,
            'employee_id' => $benefit->employee_id,
            'reason' => $event->reason
        ]);
    }

    /**
     * Handle benefits expiring event.
     */
    protected function handleBenefitsExpiring(EmployeeBenefitsExpiring $event): void
    {
        $benefit = $event->employeeBenefits;
        $employee = $benefit->employee;

        // Notify employee of expiring benefits
        if ($employee && $employee->user) {
            $employee->user->notify(new BenefitsExpiringReminder($benefit));
        }

        Log::info('Benefits expiring reminder sent', [
            'benefit_id' => $benefit->id,
            'employee_id' => $benefit->employee_id
        ]);
    }
}
