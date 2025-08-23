<?php

namespace App\Notifications\EmployeePosition;

use App\Models\EmployeePosition;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\BroadcastMessage;

class PositionCreated extends Notification implements ShouldQueue
{
    use Queueable;

    public EmployeePosition $position;

    /**
     * Create a new notification instance.
     */
    public function __construct(EmployeePosition $position)
    {
        $this->position = $position;
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
        $url = url('/positions/' . $this->position->id);

        return (new MailMessage)
            ->subject('New Position Created: ' . $this->position->title)
            ->greeting('Hello ' . $notifiable->name)
            ->line('A new position has been created in your department.')
            ->line('Position: ' . $this->position->title)
            ->line('Department: ' . $this->position->department?->name)
            ->line('Level: ' . $this->position->level->label())
            ->line('Status: ' . $this->position->status->label())
            ->action('View Position', $url)
            ->line('Please review the position details and requirements.')
            ->salutation('Best regards, HR Team');
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
            'status' => $this->position->status->value,
            'status_label' => $this->position->status->label(),
            'is_remote' => $this->position->is_remote,
            'is_travel_required' => $this->position->is_travel_required,
            'salary_range' => $this->position->salary_range,
            'hourly_rate_range' => $this->position->hourly_rate_range,
            'experience_required' => $this->position->experience_required,
            'education_required' => $this->position->education_required,
            'requirements' => $this->position->requirements,
            'responsibilities' => $this->position->responsibilities,
            'skills_required' => $this->position->skills_required,
            'created_at' => $this->position->created_at->toISOString(),
            'type' => 'position_created',
            'message' => 'New position "' . $this->position->title . '" has been created in ' . $this->position->department?->name,
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
            'status' => $this->position->status->value,
            'status_label' => $this->position->status->label(),
            'is_remote' => $this->position->is_remote,
            'is_travel_required' => $this->position->is_travel_required,
            'salary_range' => $this->position->salary_range,
            'hourly_rate_range' => $this->position->hourly_rate_range,
            'experience_required' => $this->position->experience_required,
            'education_required' => $this->position->education_required,
            'requirements' => $this->position->requirements,
            'responsibilities' => $this->position->responsibilities,
            'skills_required' => $this->position->skills_required,
            'created_at' => $this->position->created_at->toISOString(),
            'type' => 'position_created',
            'message' => 'New position "' . $this->position->title . '" has been created in ' . $this->position->department?->name,
            'timestamp' => now()->toISOString(),
        ]);
    }

    /**
     * Get the notification's database type.
     */
    public function getDatabaseType(): string
    {
        return 'position_created';
    }

    /**
     * Get the notification's database data.
     */
    public function getDatabaseData(): array
    {
        return $this->toArray(null);
    }
}
