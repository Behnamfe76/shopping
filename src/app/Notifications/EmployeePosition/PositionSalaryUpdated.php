<?php

namespace App\Notifications\EmployeePosition;

use App\Models\EmployeePosition;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PositionSalaryUpdated extends Notification implements ShouldQueue
{
    use Queueable;

    public EmployeePosition $position;

    public array $salaryChanges;

    /**
     * Create a new notification instance.
     */
    public function __construct(EmployeePosition $position, array $salaryChanges = [])
    {
        $this->position = $position;
        $this->salaryChanges = $salaryChanges;
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
            ->subject('Position Salary Updated: '.$this->position->title)
            ->greeting('Hello '.$notifiable->name)
            ->line('The salary range for your position has been updated.');

        if (isset($this->salaryChanges['old_salary_min']) && isset($this->salaryChanges['new_salary_min'])) {
            $mailMessage->line('Annual Salary Range:')
                ->line('Previous: $'.number_format($this->salaryChanges['old_salary_min']).' - $'.number_format($this->salaryChanges['old_salary_max']))
                ->line('New: $'.number_format($this->salaryChanges['new_salary_min']).' - $'.number_format($this->salaryChanges['new_salary_max']));
        }

        if (isset($this->salaryChanges['old_hourly_rate_min']) && isset($this->salaryChanges['new_hourly_rate_min'])) {
            $mailMessage->line('Hourly Rate Range:')
                ->line('Previous: $'.number_format($this->salaryChanges['old_hourly_rate_min'], 2).' - $'.number_format($this->salaryChanges['old_hourly_rate_max'], 2))
                ->line('New: $'.number_format($this->salaryChanges['new_hourly_rate_min'], 2).' - $'.number_format($this->salaryChanges['new_hourly_rate_max'], 2));
        }

        $mailMessage->action('View Position Details', $url)
            ->line('Please contact HR if you have any questions about these changes.')
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
            'salary_changes' => $this->salaryChanges,
            'new_salary_range' => $this->position->salary_range,
            'new_hourly_rate_range' => $this->position->hourly_rate_range,
            'updated_at' => $this->position->updated_at->toISOString(),
            'type' => 'position_salary_updated',
            'message' => 'Salary range updated for position "'.$this->position->title.'"',
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
            'salary_changes' => $this->salaryChanges,
            'new_salary_range' => $this->position->salary_range,
            'new_hourly_rate_range' => $this->position->hourly_rate_range,
            'updated_at' => $this->position->updated_at->toISOString(),
            'type' => 'position_salary_updated',
            'message' => 'Salary range updated for position "'.$this->position->title.'"',
            'timestamp' => now()->toISOString(),
        ]);
    }

    /**
     * Get the notification's database type.
     */
    public function getDatabaseType(): string
    {
        return 'position_salary_updated';
    }

    /**
     * Get the notification's database data.
     */
    public function getDatabaseData(): array
    {
        return $this->toArray(null);
    }
}
