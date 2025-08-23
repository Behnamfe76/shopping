<?php

namespace App\Notifications\EmployeeSalaryHistory;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class SalaryChangeApproved extends Notification implements ShouldQueue
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
        $changeDescription = $this->salaryHistory->getChangeDescription();

        return (new MailMessage)
            ->subject('Salary Change Approved')
            ->line("Your salary change has been approved!")
            ->line($changeDescription)
            ->line('Effective Date: ' . $this->salaryHistory->effective_date->format('M d, Y'))
            ->line('New Salary: $' . number_format($this->salaryHistory->new_salary, 2))
            ->action('View Details', url('/salary-changes/' . $this->salaryHistory->id))
            ->line('The change will be reflected in your next paycheck.');
    }

    public function toArray($notifiable): array
    {
        return [
            'salary_history_id' => $this->salaryHistory->id,
            'employee_id' => $this->salaryHistory->employee_id,
            'change_type' => $this->salaryHistory->change_type->value,
            'change_amount' => $this->salaryHistory->change_amount,
            'new_salary' => $this->salaryHistory->new_salary,
            'effective_date' => $this->salaryHistory->effective_date->format('Y-m-d'),
            'message' => 'Salary change approved',
            'type' => 'salary_approved',
        ];
    }
}
