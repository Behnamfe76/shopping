<?php

namespace App\Notifications\EmployeeSalaryHistory;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SalaryReviewDue extends Notification implements ShouldQueue
{
    use Queueable;

    public $employee;

    public $reviewDate;

    public $lastReviewDate;

    public function __construct($employee, $reviewDate, $lastReviewDate = null)
    {
        $this->employee = $employee;
        $this->reviewDate = $reviewDate;
        $this->lastReviewDate = $lastReviewDate;
    }

    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable): MailMessage
    {
        $mail = (new MailMessage)
            ->subject('Salary Review Due')
            ->line('A salary review is due for '.$this->employee->name)
            ->line('Review Due Date: '.$this->reviewDate->format('M d, Y'));

        if ($this->lastReviewDate) {
            $mail->line('Last Review Date: '.$this->lastReviewDate->format('M d, Y'));
        }

        $mail->line('Current Salary: $'.number_format($this->employee->current_salary ?? 0, 2))
            ->line('Position: '.($this->employee->position?->title ?? 'N/A'))
            ->line('Department: '.($this->employee->department?->name ?? 'N/A'))
            ->action('Review Employee', url('/employees/'.$this->employee->id.'/salary-review'))
            ->line('Please complete the salary review by the due date.');

        return $mail;
    }

    public function toArray($notifiable): array
    {
        return [
            'employee_id' => $this->employee->id,
            'employee_name' => $this->employee->name,
            'review_due_date' => $this->reviewDate->format('Y-m-d'),
            'last_review_date' => $this->lastReviewDate?->format('Y-m-d'),
            'current_salary' => $this->employee->current_salary,
            'position' => $this->employee->position?->title,
            'department' => $this->employee->department?->name,
            'message' => 'Salary review due',
            'type' => 'salary_review_due',
        ];
    }
}
