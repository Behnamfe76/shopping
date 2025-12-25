<?php

namespace App\Notifications\EmployeeBenefits;

use App\Models\EmployeeBenefits;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BenefitsEnrollmentApproved extends Notification implements ShouldQueue
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
            ->subject('Your Benefits Enrollment Has Been Approved')
            ->greeting("Hello {$notifiable->name},")
            ->line('Great news! Your benefits enrollment has been approved and is now active.')
            ->line('**Enrollment Details:**')
            ->line("- **Benefit Type:** {$this->benefit->benefit_type}")
            ->line("- **Benefit Name:** {$this->benefit->benefit_name}")
            ->line("- **Provider:** {$this->benefit->provider}")
            ->line("- **Coverage Level:** {$this->benefit->coverage_level}")
            ->line("- **Effective Date:** {$this->benefit->effective_date}")
            ->line('- **Monthly Premium:** $'.number_format($this->benefit->premium_amount, 2))
            ->line('- **Your Monthly Contribution:** $'.number_format($this->benefit->employee_contribution, 2))
            ->line('- **Employer Contribution:** $'.number_format($this->benefit->employer_contribution, 2))
            ->action('View Benefits Details', url('/employee/benefits/'.$this->benefit->id))
            ->line('Your benefits will be effective from the effective date listed above.')
            ->line('If you have any questions about your benefits, please contact HR.')
            ->salutation('Best regards, HR Team');
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        $employee = $this->benefit->employee;

        return [
            'type' => 'benefits_enrollment_approved',
            'title' => 'Benefits Enrollment Approved',
            'message' => "Your {$this->benefit->benefit_type} benefits enrollment has been approved",
            'benefit_id' => $this->benefit->id,
            'employee_id' => $this->benefit->employee_id,
            'employee_name' => $employee ? $employee->full_name : 'Unknown Employee',
            'benefit_type' => $this->benefit->benefit_type,
            'benefit_name' => $this->benefit->benefit_name,
            'provider' => $this->benefit->provider,
            'status' => $this->benefit->status,
            'effective_date' => $this->benefit->effective_date,
            'premium_amount' => $this->benefit->premium_amount,
            'employee_contribution' => $this->benefit->employee_contribution,
            'employer_contribution' => $this->benefit->employer_contribution,
            'created_at' => now()->toISOString(),
            'action_url' => '/employee/benefits/'.$this->benefit->id,
        ];
    }

    /**
     * Get the notification's database type.
     */
    public function getDatabaseType(): string
    {
        return 'benefits_enrollment_approved';
    }
}
