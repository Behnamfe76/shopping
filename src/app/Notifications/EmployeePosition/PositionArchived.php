<?php

namespace App\Notifications\EmployeePosition;

use App\Models\EmployeePosition;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PositionArchived extends Notification implements ShouldQueue
{
    use Queueable;

    public EmployeePosition $position;

    public array $archiveDetails;

    /**
     * Create a new notification instance.
     */
    public function __construct(EmployeePosition $position, array $archiveDetails = [])
    {
        $this->position = $position;
        $this->archiveDetails = $archiveDetails;
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
        $url = url('/positions/'.$this->position->id);

        $mailMessage = (new MailMessage)
            ->subject('Position Archived: '.$this->position->title)
            ->greeting('Hello '.$notifiable->name)
            ->line('A position has been archived and this may affect your role or department.');

        $mailMessage->line('Position: '.$this->position->title)
            ->line('Department: '.$this->position->department?->name)
            ->line('Level: '.$this->position->level->label())
            ->line('Archive Reason: '.($this->archiveDetails['reason'] ?? 'No reason specified'));

        if (isset($this->archiveDetails['replacement_position_id'])) {
            $mailMessage->line('Replacement Position: Available (ID: '.$this->archiveDetails['replacement_position_id'].')');
        }

        if (isset($this->archiveDetails['affected_employees_count']) && $this->archiveDetails['affected_employees_count'] > 0) {
            $mailMessage->line('Affected Employees: '.$this->archiveDetails['affected_employees_count'].' employee(s)');
        }

        if (isset($this->archiveDetails['transition_plan'])) {
            $mailMessage->line('Transition Plan: '.$this->archiveDetails['transition_plan']);
        }

        $mailMessage->action('View Archived Position', $url)
            ->line('Please review the archive details and ensure all necessary transitions are in place.')
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
            'archive_details' => $this->archiveDetails,
            'archive_reason' => $this->archiveDetails['reason'] ?? 'No reason specified',
            'replacement_position_id' => $this->archiveDetails['replacement_position_id'] ?? null,
            'affected_employees_count' => $this->archiveDetails['affected_employees_count'] ?? 0,
            'transition_plan' => $this->archiveDetails['transition_plan'] ?? null,
            'archived_at' => now()->toISOString(),
            'type' => 'position_archived',
            'message' => 'Position "'.$this->position->title.'" has been archived',
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
            'archive_details' => $this->archiveDetails,
            'archive_reason' => $this->archiveDetails['reason'] ?? 'No reason specified',
            'replacement_position_id' => $this->archiveDetails['replacement_position_id'] ?? null,
            'affected_employees_count' => $this->archiveDetails['affected_employees_count'] ?? 0,
            'transition_plan' => $this->archiveDetails['transition_plan'] ?? null,
            'archived_at' => now()->toISOString(),
            'type' => 'position_archived',
            'message' => 'Position "'.$this->position->title.'" has been archived',
            'timestamp' => now()->toISOString(),
        ]);
    }

    /**
     * Get the notification's database type.
     */
    public function getDatabaseType(): string
    {
        return 'position_archived';
    }

    /**
     * Get the notification's database data.
     */
    public function getDatabaseData(): array
    {
        return $this->toArray(null);
    }
}
