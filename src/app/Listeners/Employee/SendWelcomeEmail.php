<?php

namespace Fereydooni\Shopping\app\Listeners\Employee;

use Fereydooni\Shopping\app\Events\Employee\EmployeeCreated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendWelcomeEmail implements ShouldQueue
{
    use InteractsWithQueue;

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
    public function handle(EmployeeCreated $event): void
    {
        try {
            $employee = $event->employee;

            // Send welcome email to the new employee
            Mail::send('emails.employees.welcome', [
                'employee' => $employee,
                'company_name' => config('app.name'),
                'login_url' => route('login'),
            ], function ($message) use ($employee) {
                $message->to($employee->email)
                    ->subject('Welcome to '.config('app.name'));
            });

            Log::info('Welcome email sent to employee: '.$employee->email);
        } catch (\Exception $e) {
            Log::error('Failed to send welcome email to employee: '.$event->employee->email, [
                'error' => $e->getMessage(),
                'employee_id' => $event->employee->id,
            ]);
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(EmployeeCreated $event, \Throwable $exception): void
    {
        Log::error('Failed to send welcome email to employee', [
            'employee_id' => $event->employee->id,
            'employee_email' => $event->employee->email,
            'error' => $exception->getMessage(),
        ]);
    }
}
