<?php

namespace App\Notifications\EmployeePosition;

use App\Models\EmployeePosition;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PositionSetHiring extends Notification implements ShouldQueue
{
    use Queueable;

    public EmployeePosition $position;

    public array $hiringDetails;

    /**
     * Create a new notification instance.
     */
    public function __construct(EmployeePosition $position, array $hiringDetails = [])
    {
        $this->position = $position;
        $this->hiringDetails = $hiringDetails;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via($notifiable): array
    {
        return ['mail', 'database', 'broadcast'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable): MailMessage
    {
        $url = url('/positions/'.$this->position->id.'/hiring');

        $mailMessage = (new MailMessage)
            ->subject('New Hiring Position: '.$this->position->title)
            ->greeting('Hello '.$notifiable->name)
            ->line('A new position has been set to hiring status and requires your attention.');

        $mailMessage->line('Position: '.$this->position->title)
            ->line('Department: '.$this->position->department?->name)
            ->line('Level: '.$this->position->level->label())
            ->line('Urgency: '.($this->hiringDetails['urgency_level'] ?? 'Normal'));

        if (isset($this->hiringDetails['expected_fill_date'])) {
            $mailMessage->line('Expected Fill Date: '.$this->hiringDetails['expected_fill_date']);
        }

        if (isset($this->hiringDetails['hiring_manager'])) {
            $mailMessage->line('Hiring Manager: '.$this->hiringDetails['hiring_manager']);
        }

        if (isset($this->hiringDetails['recruitment_budget'])) {
            $mailMessage->line('Recruitment Budget: $'.number_format($this->hiringDetails['recruitment_budget']));
        }

        $mailMessage->line('Requirements: '.($this->position->requirements ?? 'Not specified'))
            ->line('Skills Required: '.implode(', ', $this->position->skills_required ?? []))
            ->line('Experience Required: '.($this->position->experience_required ?? 'Not specified').' years')
            ->action('View Hiring Details', $url)
            ->line('Please begin the recruitment process for this position.')
            ->salutation('Best regards, HR Team');

        return $mailMessage;
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray($notifiable): array
    {
        return [
            'id' => $this->position->id,
            'title' => $this->position->title,
            'code' => $this->position->code,
            'department_id' => $this->position->department_id,
            'department_name' => $this->position->department?->name,
            'level' => $this->position->level->value,
            'level_label' => $this->position->level->label(),
            'hiring_details' => $this->hiringDetails,
            'requirements' => $this->position->requirements,
            'skills_required' => $this->position->skills_required,
            'experience_required' => $this->position->experience_required,
            'education_required' => $this->position->education_required,
            'salary_range' => $this->position->salary_range,
            'hourly_rate_range' => $this->position->hourly_rate_range,
            'is_remote' => $this->position->is_remote,
            'is_travel_required' => $this->position->is_travel_required,
            'set_at' => now()->toISOString(),
            'type' => 'position_set_hiring',
            'message' => 'Position "'.$this->position->title.'" is now hiring',
        ];
    }

    /**
     * Get the broadcastable representation of the notification.
     */
    public function toBroadcast($notifiable): BroadcastMessage
    {
        return new BroadcastMessage([
            'id' => $this->position->id,
            'title' => $this->position->title,
            'code' => $this->position->code,
            'department_id' => $this->position->department_id,
            'department_name' => $this->position->department?->name,
            'level' => $this->position->level->value,
            'level_label' => $this->position->level->label(),
            'hiring_details' => $this->hiringDetails,
            'requirements' => $this->position->requirements,
            'skills_required' => $this->position->skills_required,
            'experience_required' => $this->position->experience_required,
            'education_required' => $this->position->education_required,
            'salary_range' => $this->position->salary_range,
            'hourly_rate_range' => $this->position->hourly_rate_range,
            'is_remote' => $this->position->is_remote,
            'is_travel_required' => $this->position->is_travel_required,
            'set_at' => now()->toISOString(),
            'type' => 'position_set_hiring',
            'message' => 'Position "'.$this->position->title.'" is now hiring',
            'timestamp' => now()->toISOString(),
        ]);
    }

    /**
     * Get the notification's database type.
     */
    public function getDatabaseType(): string
    {
        return 'position_set_hiring';
    }

    /**
     * Get the notification's database data.
     */
    public function getDatabaseData(): array
    {
        return $this->toArray(null);
    }
}
