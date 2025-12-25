<?php

namespace App\Notifications\EmployeeBenefits;

use App\Models\EmployeeBenefits;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BenefitsEnrollmentTerminated extends Notification implements ShouldQueue
{
    use Queueable;

    protected EmployeeBenefits $benefit;

    protected ?string $reason;

    /**
     * Create a new notification instance.
     */
    public function __construct(EmployeeBenefits $benefit, ?string $reason = null)
    {
        $this->benefit = $benefit;
        $this->reason = $reason;
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
        $reasonText = $this->reason ? "Reason: {$this->reason}" : '';

        return (new MailMessage)
            ->subject('Your Benefits Enrollment Has Been Terminated')
            ->greeting("Hello {$notifiable->name},")
            ->line('Your benefits enrollment has been terminated.')
            ->line('**Termination Details:**')
            ->line("- **Benefit Type:** {$this->benefit->benefit_type}")
            ->line("- **Benefit Name:** {$this->benefit->benefit_name}")
            ->line("- **Provider:** {$this->benefit->provider}")
            ->line('- **Termination Date:** '.now()->toDateString())
            ->line("- **End Date:** {$this->benefit->end_date}")
            ->when($this->reason, function ($message) {
                return $message->line("- **Reason:** {$this->reason}");
            })
            ->line('**Important Information:**')
            ->line('- Your benefits coverage will end on the end date listed above.')
            ->line('- You may be eligible for COBRA continuation coverage.')
            ->line('- Please contact HR for information about alternative coverage options.')
            ->action('Contact HR', url('/contact/hr'))
            ->line('If you have any questions about this termination, please contact HR immediately.')
            ->salutation('Best regards, HR Team');
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        $employee = $this->benefit->employee;

        return [
            'type' => 'benefits_enrollment_terminated',
            'title' => 'Benefits Enrollment Terminated',
            'message' => "Your {$this->benefit->benefit_type} benefits enrollment has been terminated",
            'benefit_id' => $this->benefit->id,
            'employee_id' => $this->benefit->employee_id,
            'employee_name' => $employee ? $employee->full_name : 'Unknown Employee',
            'benefit_type' => $this->benefit->benefit_type,
            'benefit_name' => $this->benefit->benefit_name,
            'provider' => $this->benefit->provider,
            'status' => $this->benefit->status,
            'end_date' => $this->benefit->end_date,
            'termination_date' => now()->toDateString(),
            'reason' => $this->reason,
            'created_at' => now()->toISOString(),
            'action_url' => '/contact/hr',
        ];
    }

    /**
     * Get the notification's database type.
     */
    public function getDatabaseType(): string
    {
        return 'benefits_enrollment_terminated';
    }
}
