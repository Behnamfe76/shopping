<?php

namespace App\Notifications\EmployeeSalaryHistory;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class RetroactiveAdjustmentProcessed extends Notification implements ShouldQueue
{
    use Queueable;

    public $salaryHistory;

    public $retroactiveAmount;

    public function __construct($salaryHistory, $retroactiveAmount)
    {
        $this->salaryHistory = $salaryHistory;
        $this->retroactiveAmount = $retroactiveAmount;
    }

    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable): MailMessage
    {
        $period = $this->salaryHistory->getRetroactivePeriod();

        return (new MailMessage)
            ->subject('Retroactive Salary Adjustment Processed')
            ->line('Your retroactive salary adjustment has been processed and will be included in your next paycheck.')
            ->line('Adjustment Period: '.$period)
            ->line('Retroactive Amount: $'.number_format($this->retroactiveAmount, 2))
            ->line('Original Change: '.$this->salaryHistory->getChangeDescription())
            ->line('Effective Date: '.$this->salaryHistory->effective_date->format('M d, Y'))
            ->action('View Details', url('/salary-changes/'.$this->salaryHistory->id))
            ->line('If you have any questions, please contact HR.');
    }

    public function toArray($notifiable): array
    {
        return [
            'salary_history_id' => $this->salaryHistory->id,
            'employee_id' => $this->salaryHistory->employee_id,
            'retroactive_amount' => $this->retroactiveAmount,
            'retroactive_period' => $this->salaryHistory->getRetroactivePeriod(),
            'effective_date' => $this->salaryHistory->effective_date->format('Y-m-d'),
            'message' => 'Retroactive adjustment processed',
            'type' => 'retroactive_processed',
        ];
    }
}
