<?php

namespace App\Notifications\EmployeeTimeOff;

use App\Models\EmployeeTimeOff;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\URL;

class TimeOffRequestCancelled extends Notification implements ShouldQueue
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
        $startDate = $this->timeOff->start_date->format('M d, Y');
        $endDate = $this->timeOff->end_date->format('M d, Y');

        return (new MailMessage)
            ->subject('Time-Off Request Cancelled')
            ->greeting('Hello '.$notifiable->name)
            ->line('A time-off request has been cancelled.')
            ->line('**Employee:** '.$this->timeOff->employee->full_name)
            ->line('**Type:** '.ucfirst($this->timeOff->time_off_type))
            ->line('**Dates:** '.$startDate.' to '.$endDate)
            ->line('**Total Days:** '.$this->timeOff->total_days)
            ->line('**Cancelled At:** '.$this->timeOff->updated_at->format('M d, Y g:i A'))
            ->action('View Details', URL::to('/admin/time-off/'.$this->timeOff->id))
            ->line('This cancellation has been processed and the employee\'s time-off balance has been updated accordingly.')
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
            'cancelled_at' => $this->timeOff->updated_at->toDateTimeString(),
            'message' => 'Time-off request cancelled for '.$this->timeOff->employee->full_name,
            'action_url' => '/admin/time-off/'.$this->timeOff->id,
        ];
    }
}
