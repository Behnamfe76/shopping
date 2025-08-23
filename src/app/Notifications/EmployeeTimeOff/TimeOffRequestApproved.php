<?php

namespace App\Notifications\EmployeeTimeOff;

use App\Models\EmployeeTimeOff;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\URL;

class TimeOffRequestApproved extends Notification implements ShouldQueue
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
        $approver = $this->timeOff->approver;

        return (new MailMessage)
            ->subject('Time-Off Request Approved')
            ->greeting('Hello ' . $notifiable->name)
            ->line('Your time-off request has been approved!')
            ->line('**Type:** ' . ucfirst($this->timeOff->time_off_type))
            ->line('**Dates:** ' . $startDate . ' to ' . $endDate)
            ->line('**Total Days:** ' . $this->timeOff->total_days)
            ->line('**Approved By:** ' . $approver->name)
            ->line('**Approved At:** ' . $this->timeOff->approved_at->format('M d, Y g:i A'))
            ->action('View Details', URL::to('/time-off/' . $this->timeOff->id))
            ->line('Please ensure your team is aware of your absence and that all necessary arrangements are made.')
            ->salutation('Best regards, HR Team');
    }

    public function toArray($notifiable): array
    {
        return [
            'time_off_id' => $this->timeOff->id,
            'time_off_type' => $this->timeOff->time_off_type,
            'start_date' => $this->timeOff->start_date->toDateString(),
            'end_date' => $this->timeOff->end_date->toDateString(),
            'total_days' => $this->timeOff->total_days,
            'approved_by' => $this->timeOff->approver->name,
            'approved_at' => $this->timeOff->approved_at->toDateTimeString(),
            'message' => 'Your time-off request has been approved',
            'action_url' => '/time-off/' . $this->timeOff->id,
        ];
    }
}
