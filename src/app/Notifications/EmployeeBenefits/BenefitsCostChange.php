<?php

namespace App\Notifications\EmployeeBenefits;

use App\Models\EmployeeBenefits;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BenefitsCostChange extends Notification implements ShouldQueue
{
    use Queueable;

    protected EmployeeBenefits $benefit;
    protected array $changes;

    /**
     * Create a new notification instance.
     */
    public function __construct(EmployeeBenefits $benefit, array $changes)
    {
        $this->benefit = $benefit;
        $this->changes = $changes;
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

        $message = (new MailMessage)
            ->subject('Your Benefits Costs Have Changed')
            ->greeting("Hello {$notifiable->name},")
            ->line("Your benefits costs have been updated.")
            ->line("**Benefit Details:**")
            ->line("- **Benefit Type:** {$this->benefit->benefit_type}")
            ->line("- **Benefit Name:** {$this->benefit->benefit_name}")
            ->line("- **Provider:** {$this->benefit->provider}");

        // Add cost change details
        if (isset($this->changes['premium_amount'])) {
            $message->line("- **Monthly Premium:** $" . number_format($this->benefit->premium_amount, 2));
        }

        if (isset($this->changes['employee_contribution'])) {
            $message->line("- **Your Monthly Contribution:** $" . number_format($this->benefit->employee_contribution, 2));
        }

        if (isset($this->changes['employer_contribution'])) {
            $message->line("- **Employer Contribution:** $" . number_format($this->benefit->employer_contribution, 2));
        }

        $message->line('**Effective Date:** ' . now()->toDateString())
            ->line('**Important Notes:**')
            ->line('- These changes will be reflected in your next payroll.')
            ->line('- Please review your updated benefits summary.')
            ->line('- Contact HR if you have questions about these changes.')
            ->action('View Benefits Details', url('/employee/benefits/' . $this->benefit->id))
            ->salutation('Best regards, HR Team');

        return $message;
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        $employee = $this->benefit->employee;

        return [
            'type' => 'benefits_cost_change',
            'title' => 'Benefits Costs Updated',
            'message' => "Your {$this->benefit->benefit_type} benefits costs have been updated",
            'benefit_id' => $this->benefit->id,
            'employee_id' => $this->benefit->employee_id,
            'employee_name' => $employee ? $employee->full_name : 'Unknown Employee',
            'benefit_type' => $this->benefit->benefit_type,
            'benefit_name' => $this->benefit->benefit_name,
            'provider' => $this->benefit->provider,
            'changes' => $this->changes,
            'effective_date' => now()->toDateString(),
            'created_at' => now()->toISOString(),
            'action_url' => '/employee/benefits/' . $this->benefit->id
        ];
    }

    /**
     * Get the notification's database type.
     */
    public function getDatabaseType(): string
    {
        return 'benefits_cost_change';
    }
}
