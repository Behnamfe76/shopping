<?php

namespace App\Listeners\EmployeeBenefits;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class EmployeeBenefitsListeners implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Handle the event.
     */
    public function handle(object $event): void
    {
        try {
            // Dispatch to individual listeners
            $this->dispatchToListeners($event);

        } catch (\Exception $e) {
            Log::error('Error in EmployeeBenefitsListeners', [
                'event' => get_class($event),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        }
    }

    /**
     * Dispatch event to individual listeners.
     */
    protected function dispatchToListeners(object $event): void
    {
        try {
            // Send notifications
            app(SendBenefitsNotification::class)->handle($event);

            // Update employee records
            app(UpdateEmployeeBenefitsRecord::class)->handle($event);

            // Log activities
            app(LogBenefitsActivity::class)->handle($event);

            // Process documents
            app(ProcessBenefitsDocuments::class)->handle($event);

            // Update payroll deductions
            app(UpdatePayrollDeductions::class)->handle($event);

        } catch (\Exception $e) {
            Log::error('Error dispatching to individual listeners', [
                'event' => get_class($event),
                'error' => $e->getMessage(),
            ]);
        }
    }
}
