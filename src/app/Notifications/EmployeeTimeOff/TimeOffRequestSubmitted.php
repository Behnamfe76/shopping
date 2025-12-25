<?php

namespace App\Notifications\EmployeeTimeOff;

use App\Models\EmployeeTimeOff;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\URL;

class TimeOffRequestSubmitted extends Notification implements ShouldQueue
{
    use Queueable;

    protected EmployeeTimeOff $timeOff;

    public function __construct(EmployeeTimeOff $timeOff)
    {
        $this->timeOff = $timeOff;
    }

    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable): MailMessage
    {
        $employee = $this->timeOff->employee;
        $startDate = $this->timeOff->start_date->format('M d, Y');
        $endDate = $this->timeOff->end_date->format('M d, Y');

        return (new MailMessage)
            ->subject('New Time-Off Request Submitted')
            ->greeting('Hello '.$notifiable->name)
            ->line('A new time-off request has been submitted and requires your approval.')
            ->line('**Employee:** '.$employee->full_name)
            ->line('**Type:** '.ucfirst($this->timeOff->time_off_type))
            ->line('**Dates:** '.$startDate.' to '.$endDate)
            ->line('**Total Days:** '.$this->timeOff->total_days)
            ->line('**Reason:** '.$this->timeOff->reason)
            ->action('Review Request', URL::to('/admin/time-off/'.$this->timeOff->id))
            ->line('Please review and approve or reject this request as soon as possible.')
            ->salutation('Best regards, HR System');
    }

    public function toArray($notifiable): array
    {
        return [
            'time_off_id' => $this->timeOff->id,
            'employee_id' => $this->timeOff->employee_id,
            'employee_name' => $this->timeOff->employee->full_name,
            'time_off_type' => $this->timeOff->time_off_type,
            'start_date' => $this->timeOff->start_date->toDateString(),
            'end_date' => $this->timeOff->end_date->toDateString(),
            'total_days' => $this->timeOff->total_days,
            'reason' => $this->timeOff->reason,
            'message' => 'New time-off request submitted by '.$this->timeOff->employee->full_name,
            'action_url' => '/admin/time-off/'.$this->timeOff->id,
        ];
    }
}
