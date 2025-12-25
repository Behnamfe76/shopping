<?php

namespace App\Notifications\EmployeeBenefits;

use App\Models\EmployeeBenefits;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BenefitsEnrollmentCreated extends Notification implements ShouldQueue
{
    use Queueable;

    protected EmployeeBenefits $benefit;

    /**
     * Create a new notification instance.
     */
    public function __construct(EmployeeBenefits $benefit)
    {
        $this->benefit = $benefit;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $employee = $this->benefit->employee;
        $employeeName = $employee ? $employee->full_name : 'Unknown Employee';

        return (new MailMessage)
            ->subject('New Employee Benefits Enrollment Created')
            ->greeting("Hello {$notifiable->name},")
            ->line("A new employee benefits enrollment has been created for {$employeeName}.")
            ->line('**Benefit Details:**')
            ->line("- **Type:** {$this->benefit->benefit_type}")
            ->line("- **Name:** {$this->benefit->benefit_name}")
            ->line("- **Provider:** {$this->benefit->provider}")
            ->line("- **Coverage Level:** {$this->benefit->coverage_level}")
            ->line("- **Status:** {$this->benefit->status}")
            ->line("- **Effective Date:** {$this->benefit->effective_date}")
            ->line('- **Monthly Premium:** $'.number_format($this->benefit->premium_amount, 2))
            ->line('- **Employee Contribution:** $'.number_format($this->benefit->employee_contribution, 2))
            ->line('- **Employer Contribution:** $'.number_format($this->benefit->employer_contribution, 2))
            ->action('Review Enrollment', url('/admin/employee-benefits/'.$this->benefit->id))
            ->line('Please review and approve this enrollment as needed.')
            ->salutation('Best regards, HR System');
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        $employee = $this->benefit->employee;

        return [
            'type' => 'benefits_enrollment_created',
            'title' => 'New Benefits Enrollment Created',
            'message' => 'New benefits enrollment created for '.($employee ? $employee->full_name : 'Unknown Employee'),
            'benefit_id' => $this->benefit->id,
            'employee_id' => $this->benefit->employee_id,
            'employee_name' => $employee?->full_name ?? 'Unknown Employee',
            'benefit_type' => $this->benefit->benefit_type,
            'benefit_name' => $this->benefit->benefit_name,
            'provider' => $this->benefit->provider,
            'status' => $this->benefit->status,
            'effective_date' => $this->benefit->effective_date,
            'premium_amount' => $this->benefit->premium_amount,
            'employee_contribution' => $this->benefit->employee_contribution,
            'employer_contribution' => $this->benefit->employer_contribution,
            'created_at' => now()->toISOString(),
            'action_url' => '/admin/employee-benefits/'.$this->benefit->id,
        ];
    }

    /**
     * Get the notification's database type.
     */
    public function getDatabaseType(): string
    {
        return 'benefits_enrollment_created';
    }

    /**
     * Determine if the notification should be sent.
     */
    public function shouldSend(object $notifiable): bool
    {
        // Only send to HR users or users with benefits management permissions
        return $notifiable->hasPermission('employee-benefits.manage-all') ||
               $notifiable->hasPermission('employee-benefits.view-all');
    }
}
