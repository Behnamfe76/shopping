<?php

namespace App\Notifications\EmployeeSalaryHistory;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SalaryChangeRequested extends Notification implements ShouldQueue
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
        return (new MailMessage)
            ->subject('Salary Change Request Requires Approval')
            ->line('A new salary change request has been submitted and requires your approval.')
            ->line('Employee: '.$this->salaryHistory->employee->name)
            ->line('Change Type: '.$this->salaryHistory->change_type->label())
            ->line('Amount: $'.number_format($this->salaryHistory->change_amount, 2))
            ->action('Review Request', url('/salary-changes/'.$this->salaryHistory->id))
            ->line('Please review and take appropriate action.');
    }

    public function toArray($notifiable): array
    {
        return [
            'salary_history_id' => $this->salaryHistory->id,
            'employee_id' => $this->salaryHistory->employee_id,
            'change_type' => $this->salaryHistory->change_type->value,
            'change_amount' => $this->salaryHistory->change_amount,
            'effective_date' => $this->salaryHistory->effective_date->format('Y-m-d'),
            'message' => 'Salary change request requires approval',
        ];
    }
}
