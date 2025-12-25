<?php

namespace App\Notifications\EmployeeEmergencyContact;

use App\Models\EmployeeEmergencyContact;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class EmergencyContactSetPrimary extends Notification implements ShouldQueue
{
    use Queueable;

    public $contact;

    public $previousPrimary;

    /**
     * Create a new notification instance.
     */
    public function __construct(EmployeeEmergencyContact $contact, ?EmployeeEmergencyContact $previousPrimary = null)
    {
        $this->contact = $contact;
        $this->previousPrimary = $previousPrimary;
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
        $employee = $notifiable;
        $contact = $this->contact;
        $previousPrimary = $this->previousPrimary;

        $message = (new MailMessage)
            ->subject('Primary Emergency Contact Updated')
            ->greeting("Hello {$employee->first_name},")
            ->line('Your primary emergency contact has been updated.');

        if ($previousPrimary) {
            $message->line('**Previous Primary Contact:**')
                ->line("Name: {$previousPrimary->contact_name}")
                ->line("Relationship: {$previousPrimary->relationship}")
                ->line("Phone: {$previousPrimary->phone_primary}");
        }

        $message->line('**New Primary Emergency Contact:**')
            ->line("Name: {$contact->contact_name}")
            ->line("Relationship: {$contact->relationship}")
            ->line("Primary Phone: {$contact->phone_primary}");

        if ($contact->phone_secondary) {
            $message->line("Secondary Phone: {$contact->phone_secondary}");
        }

        if ($contact->email) {
            $message->line("Email: {$contact->email}");
        }

        $message->line('**Important:** This contact will now be the first person contacted in case of an emergency.')
            ->line('If you did not request this change or if any information is incorrect, please contact HR immediately.')
            ->action('View Your Profile', url('/employee/profile'))
            ->line('Thank you for keeping your emergency contact information up to date.');

        return $message;
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'emergency_contact_set_primary',
            'contact_id' => $this->contact->id,
            'contact_name' => $this->contact->contact_name,
            'relationship' => $this->contact->relationship,
            'previous_primary_id' => $this->previousPrimary?->id,
            'previous_primary_name' => $this->previousPrimary?->contact_name,
            'set_at' => now(),
            'message' => "Emergency contact {$this->contact->contact_name} ({$this->contact->relationship}) has been set as your primary contact.",
        ];
    }

    /**
     * Get the notification's database type.
     */
    public function toDatabase(object $notifiable): array
    {
        return $this->toArray($notifiable);
    }

    /**
     * Determine which queues should be used for each notification channel.
     */
    public function viaQueues(): array
    {
        return [
            'mail' => 'emails',
            'database' => 'notifications',
        ];
    }

    /**
     * Get the tags that should be assigned to the job.
     */
    public function tags(): array
    {
        return [
            'emergency_contact',
            'primary_contact',
            'employee_'.$this->contact->employee_id,
            'contact_'.$this->contact->id,
        ];
    }
}
