<?php

namespace App\Listeners\EmployeeSkill;

use App\Events\EmployeeSkill\EmployeeSkillCertified;
use App\Events\EmployeeSkill\EmployeeSkillCreated;
use App\Events\EmployeeSkill\EmployeeSkillExpiring;
use App\Events\EmployeeSkill\EmployeeSkillSetPrimary;
use App\Events\EmployeeSkill\EmployeeSkillVerified;
use App\Notifications\EmployeeSkill\CertificationExpiringReminder;
use App\Notifications\EmployeeSkill\SkillAdded;
use App\Notifications\EmployeeSkill\SkillCertified;
use App\Notifications\EmployeeSkill\SkillSetPrimary;
use App\Notifications\EmployeeSkill\SkillVerified;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class SendSkillNotification implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Handle the event.
     */
    public function handle($event): void
    {
        try {
            match (true) {
                $event instanceof EmployeeSkillCreated => $this->handleSkillCreated($event),
                $event instanceof EmployeeSkillVerified => $this->handleSkillVerified($event),
                $event instanceof EmployeeSkillCertified => $this->handleSkillCertified($event),
                $event instanceof EmployeeSkillSetPrimary => $this->handleSkillSetPrimary($event),
                $event instanceof EmployeeSkillExpiring => $this->handleSkillExpiring($event),
                default => Log::info('Unknown skill event type', ['event' => get_class($event)]),
            };
        } catch (\Exception $e) {
            Log::error('Failed to send skill notification', [
                'event' => get_class($event),
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Handle skill created event
     */
    private function handleSkillCreated(EmployeeSkillCreated $event): void
    {
        $skill = $event->employeeSkill;

        // Notify managers about new skill
        if ($skill->employee && $skill->employee->manager) {
            $skill->employee->manager->notify(new SkillAdded($skill));
        }

        Log::info('Skill added notification sent', [
            'skill_id' => $skill->id,
            'employee_id' => $skill->employee_id,
        ]);
    }

    /**
     * Handle skill verified event
     */
    private function handleSkillVerified(EmployeeSkillVerified $event): void
    {
        $skill = $event->employeeSkill;

        // Notify employee about skill verification
        if ($skill->employee && $skill->employee->user) {
            $skill->employee->user->notify(new SkillVerified($skill));
        }

        Log::info('Skill verified notification sent', [
            'skill_id' => $skill->id,
            'employee_id' => $skill->employee_id,
        ]);
    }

    /**
     * Handle skill certified event
     */
    private function handleSkillCertified(EmployeeSkillCertified $event): void
    {
        $skill = $event->employeeSkill;

        // Notify employee about skill certification
        if ($skill->employee && $skill->employee->user) {
            $skill->employee->user->notify(new SkillCertified($skill));
        }

        Log::info('Skill certified notification sent', [
            'skill_id' => $skill->id,
            'employee_id' => $skill->employee_id,
        ]);
    }

    /**
     * Handle skill set primary event
     */
    private function handleSkillSetPrimary(EmployeeSkillSetPrimary $event): void
    {
        $skill = $event->employeeSkill;

        // Notify employee about primary skill designation
        if ($skill->employee && $skill->employee->user) {
            $skill->employee->user->notify(new SkillSetPrimary($skill));
        }

        Log::info('Skill set primary notification sent', [
            'skill_id' => $skill->id,
            'employee_id' => $skill->employee_id,
        ]);
    }

    /**
     * Handle skill expiring event
     */
    private function handleSkillExpiring(EmployeeSkillExpiring $event): void
    {
        $skill = $event->employeeSkill;

        // Notify employee about expiring certification
        if ($skill->employee && $skill->employee->user) {
            $skill->employee->user->notify(new CertificationExpiringReminder($skill, $event->daysUntilExpiry));
        }

        Log::info('Skill expiring notification sent', [
            'skill_id' => $skill->id,
            'employee_id' => $skill->employee_id,
            'days_until_expiry' => $event->daysUntilExpiry,
        ]);
    }
}
