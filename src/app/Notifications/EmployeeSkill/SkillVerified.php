<?php

namespace App\Notifications\EmployeeSkill;

use App\Models\EmployeeSkill;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SkillVerified extends Notification implements ShouldQueue
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
        $skillName = $this->skill->skill_name;
        $category = $this->skill->skill_category->label();
        $level = $this->skill->proficiency_level->label();
        
        return (new MailMessage)
            ->subject("Skill Verified: {$skillName}")
            ->greeting("Hello {$notifiable->name},")
            ->line("Congratulations! Your skill '{$skillName}' has been verified.")
            ->line("Category: {$category}")
            ->line("Proficiency Level: {$level}")
            ->line("Years of Experience: {$this->skill->years_experience}")
            ->action('View Your Skills', url("/profile/skills"))
            ->line('This skill is now part of your verified profile.');
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'skill_verified',
            'skill_id' => $this->skill->id,
            'skill_name' => $this->skill->skill_name,
            'skill_category' => $this->skill->skill_category->value,
            'proficiency_level' => $this->skill->proficiency_level->value,
            'message' => "Skill '{$this->skill->skill_name}' has been verified",
            'timestamp' => now()->toISOString(),
        ];
    }
}
