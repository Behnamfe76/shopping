<?php

namespace App\Notifications\EmployeeBenefits;

use App\Models\EmployeeBenefits;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BenefitsExpiringReminder extends Notification implements ShouldQueue
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
        $daysUntilExpiry = now()->diffInDays($this->benefit->end_date);

        return (new MailMessage)
            ->subject('Your Benefits Are Expiring Soon')
            ->greeting("Hello {$notifiable->name},")
            ->line('This is a reminder that your benefits enrollment will expire soon.')
            ->line('**Expiring Benefits Details:**')
            ->line("- **Benefit Type:** {$this->benefit->benefit_type}")
            ->line("- **Benefit Name:** {$this->benefit->benefit_name}")
            ->line("- **Provider:** {$this->benefit->provider}")
            ->line("- **Current End Date:** {$this->benefit->end_date}")
            ->line("- **Days Until Expiry:** {$daysUntilExpiry} days")
            ->line('**Action Required:**')
            ->line('- Please review your renewal options before the expiration date.')
            ->line('- Contact HR if you need assistance with renewal or have questions.')
            ->line('- Consider alternative coverage options if renewal is not available.')
            ->action('Review Benefits', url('/employee/benefits/'.$this->benefit->id))
            ->line('Don\'t let your coverage lapse - take action today!')
            ->salutation('Best regards, HR Team');
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        $employee = $this->benefit->employee;
        $daysUntilExpiry = now()->diffInDays($this->benefit->end_date);

        return [
            'type' => 'benefits_expiring_reminder',
            'title' => 'Benefits Expiring Soon',
            'message' => "Your {$this->benefit->benefit_type} benefits will expire in {$daysUntilExpiry} days",
            'benefit_id' => $this->benefit->id,
            'employee_id' => $this->benefit->employee_id,
            'employee_name' => $employee ? $employee->full_name : 'Unknown Employee',
            'benefit_type' => $this->benefit->benefit_type,
            'benefit_name' => $this->benefit->benefit_name,
            'provider' => $this->benefit->provider,
            'end_date' => $this->benefit->end_date,
            'days_until_expiry' => $daysUntilExpiry,
            'created_at' => now()->toISOString(),
            'action_url' => '/employee/benefits/'.$this->benefit->id,
        ];
    }

    /**
     * Get the notification's database type.
     */
    public function getDatabaseType(): string
    {
        return 'benefits_expiring_reminder';
    }
}
