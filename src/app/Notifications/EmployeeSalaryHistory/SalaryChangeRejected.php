<?php

namespace App\Notifications\EmployeeSalaryHistory;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SalaryChangeRejected extends Notification implements ShouldQueue
{
    use Queueable;

    public $salaryHistory;

    public function __construct($salaryHistory)
    {
        $this->salaryHistory = $salaryHistory;
    }

    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable): MailMessage
    {
        $mail = (new MailMessage)
            ->subject('Salary Change Request Rejected')
            ->line('Your salary change request has been reviewed and unfortunately cannot be approved at this time.')
            ->line('Change Type: '.$this->salaryHistory->change_type->label())
            ->line('Requested Amount: $'.number_format($this->salaryHistory->change_amount, 2))
            ->line('Effective Date: '.$this->salaryHistory->effective_date->format('M d, Y'));

        if ($this->salaryHistory->rejection_reason) {
            $mail->line('Reason: '.$this->salaryHistory->rejection_reason);
        }

        $mail->line('If you have questions about this decision, please contact your manager or HR representative.')
            ->action('View Details', url('/salary-changes/'.$this->salaryHistory->id));

        return $mail;
    }

    public function toArray($notifiable): array
    {
        return [
            'salary_history_id' => $this->salaryHistory->id,
            'employee_id' => $this->salaryHistory->employee_id,
            'change_type' => $this->salaryHistory->change_type->value,
            'change_amount' => $this->salaryHistory->change_amount,
            'effective_date' => $this->salaryHistory->effective_date->format('Y-m-d'),
            'rejection_reason' => $this->salaryHistory->rejection_reason,
            'message' => 'Salary change rejected',
            'type' => 'salary_rejected',
        ];
    }
}
