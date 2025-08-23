<?php

namespace App\Notifications\EmployeePosition;

use App\Models\EmployeePosition;
use App\Models\Employee;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\BroadcastMessage;

class PositionMatchFound extends Notification implements ShouldQueue
{
    use Queueable;

    public EmployeePosition $position;
    public Employee $employee;
    public float $matchPercentage;
    public array $matchDetails;

    /**
     * Create a new notification instance.
     */
    public function __construct(EmployeePosition $position, Employee $employee, float $matchPercentage, array $matchDetails = [])
    {
        $this->position = $position;
        $this->employee = $employee;
        $this->matchPercentage = $matchPercentage;
        $this->matchDetails = $matchDetails;
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
        $url = url('/positions/' . $this->position->id . '/matches');

        $mailMessage = (new MailMessage)
            ->subject('Position Match Found: ' . $this->position->title)
            ->greeting('Hello ' . $notifiable->name)
            ->line('A potential match has been found between an employee and a position.');

        $mailMessage->line('Position: ' . $this->position->title)
            ->line('Department: ' . $this->position->department?->name)
            ->line('Level: ' . $this->position->level->label())
            ->line('Employee: ' . $this->employee->full_name)
            ->line('Current Position: ' . ($this->employee->currentPosition?->title ?? 'Not specified'))
            ->line('Match Percentage: ' . number_format($this->matchPercentage, 1) . '%');

        if (isset($this->matchDetails['matching_skills'])) {
            $mailMessage->line('Matching Skills: ' . implode(', ', $this->matchDetails['matching_skills']));
        }

        if (isset($this->matchDetails['experience_match'])) {
            $mailMessage->line('Experience Match: ' . $this->matchDetails['experience_match']);
        }

        if (isset($this->matchDetails['education_match'])) {
            $mailMessage->line('Education Match: ' . $this->matchDetails['education_match']);
        }

        $mailMessage->action('View Match Details', $url)
            ->line('Please review this match and consider if it would be a good fit.')
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
            'employee_id' => $this->employee->id,
            'employee_name' => $this->employee->full_name,
            'employee_current_position' => $this->employee->currentPosition?->title ?? 'Not specified',
            'match_percentage' => $this->matchPercentage,
            'match_details' => $this->matchDetails,
            'matching_skills' => $this->matchDetails['matching_skills'] ?? [],
            'experience_match' => $this->matchDetails['experience_match'] ?? 'Not specified',
            'education_match' => $this->matchDetails['education_match'] ?? 'Not specified',
            'found_at' => now()->toISOString(),
            'type' => 'position_match_found',
            'message' => 'Match found: ' . $this->employee->full_name . ' (' . number_format($this->matchPercentage, 1) . '%) for position "' . $this->position->title . '"',
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
            'employee_id' => $this->employee->id,
            'employee_name' => $this->employee->full_name,
            'employee_current_position' => $this->employee->currentPosition?->title ?? 'Not specified',
            'match_percentage' => $this->matchPercentage,
            'match_details' => $this->matchDetails,
            'matching_skills' => $this->matchDetails['matching_skills'] ?? [],
            'experience_match' => $this->matchDetails['experience_match'] ?? 'Not specified',
            'education_match' => $this->matchDetails['education_match'] ?? 'Not specified',
            'found_at' => now()->toISOString(),
            'type' => 'position_match_found',
            'message' => 'Match found: ' . $this->employee->full_name . ' (' . number_format($this->matchPercentage, 1) . '%) for position "' . $this->position->title . '"',
            'timestamp' => now()->toISOString(),
        ]);
    }

    /**
     * Get the notification's database type.
     */
    public function getDatabaseType(): string
    {
        return 'position_match_found';
    }

    /**
     * Get the notification's database data.
     */
    public function getDatabaseData(): array
    {
        return $this->toArray(null);
    }
}
