<?php

namespace App\Notifications\EmployeeBenefits;

use App\Models\EmployeeBenefits;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class EmployeeBenefitsNotifications extends Notification implements ShouldQueue
{
    use Queueable;

    protected EmployeeBenefits $benefit;
    protected string $type;
    protected array $data;

    /**
     * Create a new notification instance.
     */
    public function __construct(EmployeeBenefits $benefit, string $type, array $data = [])
    {
        $this->benefit = $benefit;
        $this->type = $type;
        $this->data = $data;
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
        return match($this->type) {
            'enrollment_created' => $this->getEnrollmentCreatedMail($notifiable),
            'enrollment_approved' => $this->getEnrollmentApprovedMail($notifiable),
            'enrollment_terminated' => $this->getEnrollmentTerminatedMail($notifiable),
            'expiring_reminder' => $this->getExpiringReminderMail($notifiable),
            'renewal_notice' => $this->getRenewalNoticeMail($notifiable),
            'cost_change' => $this->getCostChangeMail($notifiable),
            default => $this->getDefaultMail($notifiable)
        };
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        $baseData = [
            'benefit_id' => $this->benefit->id,
            'employee_id' => $this->benefit->employee_id,
            'benefit_type' => $this->benefit->benefit_type,
            'benefit_name' => $this->benefit->benefit_name,
            'provider' => $this->benefit->provider,
            'created_at' => now()->toISOString(),
        ];

        return array_merge($baseData, $this->data);
    }

    /**
     * Get enrollment created mail.
     */
    protected function getEnrollmentCreatedMail(object $notifiable): MailMessage
    {
        $employee = $this->benefit->employee;
        $employeeName = $employee ? $employee->full_name : 'Unknown Employee';

        return (new MailMessage)
            ->subject('New Employee Benefits Enrollment Created')
            ->greeting("Hello {$notifiable->name},")
            ->line("A new employee benefits enrollment has been created for {$employeeName}.")
            ->line("**Benefit Details:**")
            ->line("- **Type:** {$this->benefit->benefit_type}")
            ->line("- **Name:** {$this->benefit->benefit_name}")
            ->line("- **Provider:** {$this->benefit->provider}")
            ->line("- **Status:** {$this->benefit->status}")
            ->line("- **Effective Date:** {$this->benefit->effective_date}")
            ->action('Review Enrollment', url('/admin/employee-benefits/' . $this->benefit->id))
            ->line('Please review and approve this enrollment as needed.')
            ->salutation('Best regards, HR System');
    }

    /**
     * Get enrollment approved mail.
     */
    protected function getEnrollmentApprovedMail(object $notifiable): MailMessage
    {
        $employee = $this->benefit->employee;
        $employeeName = $employee ? $employee->full_name : 'Unknown Employee';

        return (new MailMessage)
            ->subject('Your Benefits Enrollment Has Been Approved')
            ->greeting("Hello {$notifiable->name},")
            ->line("Great news! Your benefits enrollment has been approved and is now active.")
            ->line("**Enrollment Details:**")
            ->line("- **Benefit Type:** {$this->benefit->benefit_type}")
            ->line("- **Benefit Name:** {$this->benefit->benefit_name}")
            ->line("- **Provider:** {$this->benefit->provider}")
            ->line("- **Effective Date:** {$this->benefit->effective_date}")
            ->action('View Benefits Details', url('/employee/benefits/' . $this->benefit->id))
            ->line('Your benefits will be effective from the effective date listed above.')
            ->salutation('Best regards, HR Team');
    }

    /**
     * Get enrollment terminated mail.
     */
    protected function getEnrollmentTerminatedMail(object $notifiable): MailMessage
    {
        $employee = $this->benefit->employee;
        $employeeName = $employee ? $employee->full_name : 'Unknown Employee';
        $reason = $this->data['reason'] ?? 'No reason provided';

        return (new MailMessage)
            ->subject('Your Benefits Enrollment Has Been Terminated')
            ->greeting("Hello {$notifiable->name},")
            ->line("Your benefits enrollment has been terminated.")
            ->line("**Termination Details:**")
            ->line("- **Benefit Type:** {$this->benefit->benefit_type}")
            ->line("- **Benefit Name:** {$this->benefit->benefit_name}")
            ->line("- **Provider:** {$this->benefit->provider}")
            ->line("- **Reason:** {$reason}")
            ->line('**Important Information:**')
            ->line('- Your benefits coverage will end on the end date listed above.')
            ->line('- You may be eligible for COBRA continuation coverage.')
            ->action('Contact HR', url('/contact/hr'))
            ->line('If you have any questions, please contact HR immediately.')
            ->salutation('Best regards, HR Team');
    }

    /**
     * Get expiring reminder mail.
     */
    protected function getExpiringReminderMail(object $notifiable): MailMessage
    {
        $employee = $this->benefit->employee;
        $employeeName = $employee ? $employee->full_name : 'Unknown Employee';
        $daysUntilExpiry = $this->data['days_until_expiry'] ?? 30;

        return (new MailMessage)
            ->subject('Your Benefits Are Expiring Soon')
            ->greeting("Hello {$notifiable->name},")
            ->line("This is a reminder that your benefits enrollment will expire soon.")
            ->line("**Expiring Benefits Details:**")
            ->line("- **Benefit Type:** {$this->benefit->benefit_type}")
            ->line("- **Benefit Name:** {$this->benefit->benefit_name}")
            ->line("- **Provider:** {$this->benefit->provider}")
            ->line("- **Days Until Expiry:** {$daysUntilExpiry} days")
            ->line('**Action Required:**')
            ->line('- Please review your renewal options before the expiration date.')
            ->action('Review Benefits', url('/employee/benefits/' . $this->benefit->id))
            ->line('Don\'t let your coverage lapse - take action today!')
            ->salutation('Best regards, HR Team');
    }

    /**
     * Get renewal notice mail.
     */
    protected function getRenewalNoticeMail(object $notifiable): MailMessage
    {
        $employee = $this->benefit->employee;
        $employeeName = $employee ? $employee->full_name : 'Unknown Employee';
        $renewalDeadline = $this->data['renewal_deadline'] ?? now()->addDays(30)->toDateString();

        return (new MailMessage)
            ->subject('Benefits Renewal Notice')
            ->greeting("Hello {$notifiable->name},")
            ->line("Your benefits enrollment is eligible for renewal.")
            ->line("**Renewal Details:**")
            ->line("- **Benefit Type:** {$this->benefit->benefit_type}")
            ->line("- **Benefit Name:** {$this->benefit->benefit_name}")
            ->line("- **Provider:** {$this->benefit->provider}")
            ->line("- **Renewal Deadline:** {$renewalDeadline}")
            ->action('Renew Benefits', url('/employee/benefits/' . $this->benefit->id . '/renew'))
            ->line('Please complete your renewal by the deadline to avoid any coverage gaps.')
            ->salutation('Best regards, HR Team');
    }

    /**
     * Get cost change mail.
     */
    protected function getCostChangeMail(object $notifiable): MailMessage
    {
        $employee = $this->benefit->employee;
        $employeeName = $employee ? $employee->full_name : 'Unknown Employee';
        $changes = $this->data['changes'] ?? [];

        $message = (new MailMessage)
            ->subject('Your Benefits Costs Have Changed')
            ->greeting("Hello {$notifiable->name},")
            ->line("Your benefits costs have been updated.")
            ->line("**Benefit Details:**")
            ->line("- **Benefit Type:** {$this->benefit->benefit_type}")
            ->line("- **Benefit Name:** {$this->benefit->benefit_name}")
            ->line("- **Provider:** {$this->benefit->provider}");

        if (isset($changes['premium_amount'])) {
            $message->line("- **Monthly Premium:** $" . number_format($this->benefit->premium_amount, 2));
        }

        if (isset($changes['employee_contribution'])) {
            $message->line("- **Your Monthly Contribution:** $" . number_format($this->benefit->employee_contribution, 2));
        }

        $message->line('**Effective Date:** ' . now()->toDateString())
            ->action('View Benefits Details', url('/employee/benefits/' . $this->benefit->id))
            ->line('Contact HR if you have questions about these changes.')
            ->salutation('Best regards, HR Team');

        return $message;
    }

    /**
     * Get default mail.
     */
    protected function getDefaultMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Employee Benefits Notification')
            ->greeting("Hello {$notifiable->name},")
            ->line("You have a notification regarding your employee benefits.")
            ->action('View Details', url('/employee/benefits/' . $this->benefit->id))
            ->salutation('Best regards, HR Team');
    }

    /**
     * Get the notification's database type.
     */
    public function getDatabaseType(): string
    {
        return 'employee_benefits_' . $this->type;
    }
}
