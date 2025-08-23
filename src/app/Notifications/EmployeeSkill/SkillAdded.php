<?php

namespace App\Notifications\EmployeeSkill;

use App\Models\EmployeeSkill;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SkillAdded extends Notification implements ShouldQueue
{
    use Queueable;

    public EmployeeSkill $skill;

    /**
     * Create a new notification instance.
     */
    public function __construct(EmployeeSkill $skill)
    {
        $this->skill = $skill;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $employeeName = $this->skill->employee->full_name ?? 'Employee';
        $skillName = $this->skill->skill_name;
        $category = $this->skill->skill_category->label();
        $level = $this->skill->proficiency_level->label();
        
        return (new MailMessage)
            ->subject("New Skill Added: {$skillName}")
            ->greeting("Hello {$notifiable->name},")
            ->line("A new skill has been added to {$employeeName}'s profile.")
            ->line("Skill: {$skillName}")
            ->line("Category: {$category}")
            ->line("Proficiency Level: {$level}")
            ->line("Years of Experience: {$this->skill->years_experience}")
            ->action('View Employee Profile', url("/employees/{$this->skill->employee_id}"))
            ->line('Please review and verify this skill if necessary.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'skill_added',
            'skill_id' => $this->skill->id,
            'employee_id' => $this->skill->employee_id,
            'employee_name' => $this->skill->employee->full_name ?? 'Unknown',
            'skill_name' => $this->skill->skill_name,
            'skill_category' => $this->skill->skill_category->value,
            'proficiency_level' => $this->skill->proficiency_level->value,
            'years_experience' => $this->skill->years_experience,
            'message' => "New skill '{$this->skill->skill_name}' added to " . ($this->skill->employee->full_name ?? 'employee'),
            'timestamp' => now()->toISOString(),
        ];
    }
}
