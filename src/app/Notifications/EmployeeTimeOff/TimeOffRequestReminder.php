<?php

namespace App\Notifications\EmployeeTimeOff;

use App\Models\EmployeeTimeOff;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\URL;

class TimeOffRequestReminder extends Notification implements ShouldQueue
{
    use Queueable;

    protected EmployeeTimeOff $timeOff;
    protected int $daysPending;

    public function __construct(EmployeeTimeOff $timeOff, int $daysPending = 0)
    {
        $this->timeOff = $timeOff;
        $this->daysPending = $daysPending;
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

        $message = (new MailMessage)
            ->subject('Time-Off Request Reminder - Action Required')
            ->greeting('Hello ' . $notifiable->name)
            ->line('This is a reminder that you have a pending time-off request that requires your attention.');

        if ($this->daysPending > 0) {
            $message->line('**This request has been pending for ' . $this->daysPending . ' day(s).**');
        }

        $message->line('**Employee:** ' . $employee->full_name)
            ->line('**Type:** ' . ucfirst($this->timeOff->time_off_type))
            ->line('**Dates:** ' . $startDate . ' to ' . $endDate)
            ->line('**Total Days:** ' . $this->timeOff->total_days)
            ->line('**Reason:** ' . $this->timeOff->reason)
            ->line('**Submitted:** ' . $this->timeOff->created_at->format('M d, Y g:i A'))
            ->action('Review Request Now', URL::to('/admin/time-off/' . $this->timeOff->id))
            ->line('Please review and take action on this request as soon as possible to avoid delays for the employee.')
            ->salutation('Best regards, HR System');

        return $message;
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
            'days_pending' => $this->daysPending,
            'submitted_at' => $this->timeOff->created_at->toDateTimeString(),
            'message' => 'Reminder: Pending time-off request from ' . $this->timeOff->employee->full_name,
            'action_url' => '/admin/time-off/' . $this->timeOff->id,
        ];
    }
}
