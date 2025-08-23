<?php

namespace App\Notifications\EmployeeBenefits;

use App\Models\EmployeeBenefits;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BenefitsRenewalNotice extends Notification implements ShouldQueue
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
        $renewalDeadline = now()->addDays(30)->toDateString();

        return (new MailMessage)
            ->subject('Benefits Renewal Notice')
            ->greeting("Hello {$notifiable->name},")
            ->line("Your benefits enrollment is eligible for renewal.")
            ->line("**Renewal Details:**")
            ->line("- **Benefit Type:** {$this->benefit->benefit_type}")
            ->line("- **Benefit Name:** {$this->benefit->benefit_name}")
            ->line("- **Provider:** {$this->benefit->provider}")
            ->line("- **Current End Date:** {$this->benefit->end_date}")
            ->line("- **Renewal Deadline:** {$renewalDeadline}")
            ->line('**Renewal Options:**')
            ->line('- Continue with current coverage (if available)')
            ->line('- Switch to alternative plans')
            ->line('- Adjust coverage levels')
            ->line('- Update dependent information')
            ->action('Renew Benefits', url('/employee/benefits/' . $this->benefit->id . '/renew'))
            ->line('Please complete your renewal by the deadline to avoid any coverage gaps.')
            ->line('Contact HR if you need assistance with the renewal process.')
            ->salutation('Best regards, HR Team');
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        $employee = $this->benefit->employee;
        $renewalDeadline = now()->addDays(30)->toDateString();

        return [
            'type' => 'benefits_renewal_notice',
            'title' => 'Benefits Renewal Notice',
            'message' => "Your {$this->benefit->benefit_type} benefits are eligible for renewal",
            'benefit_id' => $this->benefit->id,
            'employee_id' => $this->benefit->employee_id,
            'employee_name' => $employee ? $employee->full_name : 'Unknown Employee',
            'benefit_type' => $this->benefit->benefit_type,
            'benefit_name' => $this->benefit->benefit_name,
            'provider' => $this->benefit->provider,
            'end_date' => $this->benefit->end_date,
            'renewal_deadline' => $renewalDeadline,
            'created_at' => now()->toISOString(),
            'action_url' => '/employee/benefits/' . $this->benefit->id . '/renew'
        ];
    }

    /**
     * Get the notification's database type.
     */
    public function getDatabaseType(): string
    {
        return 'benefits_renewal_notice';
    }
}
