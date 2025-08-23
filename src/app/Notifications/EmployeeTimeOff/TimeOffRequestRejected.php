<?php

namespace App\Notifications\EmployeeTimeOff;

use App\Models\EmployeeTimeOff;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\URL;

class TimeOffRequestRejected extends Notification implements ShouldQueue
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
        $rejector = $this->timeOff->rejector;

        return (new MailMessage)
            ->subject('Time-Off Request Rejected')
            ->greeting('Hello ' . $notifiable->name)
            ->line('Your time-off request has been rejected.')
            ->line('**Type:** ' . ucfirst($this->timeOff->time_off_type))
            ->line('**Dates:** ' . $startDate . ' to ' . $endDate)
            ->line('**Total Days:** ' . $this->timeOff->total_days)
            ->line('**Rejected By:** ' . $rejector->name)
            ->line('**Rejected At:** ' . $this->timeOff->rejected_at->format('M d, Y g:i A'))
            ->line('**Reason for Rejection:** ' . $this->timeOff->rejection_reason)
            ->action('View Details', URL::to('/time-off/' . $this->timeOff->id))
            ->line('If you have any questions about this decision, please contact your manager or HR representative.')
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
            'rejected_by' => $this->timeOff->rejector->name,
            'rejected_at' => $this->timeOff->rejected_at->toDateTimeString(),
            'rejection_reason' => $this->timeOff->rejection_reason,
            'message' => 'Your time-off request has been rejected',
            'action_url' => '/time-off/' . $this->timeOff->id,
        ];
    }
}
