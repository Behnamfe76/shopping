<?php

namespace App\Notifications\EmployeeEmergencyContact;

use App\Models\EmployeeEmergencyContact;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class EmergencyContactRemoved extends Notification implements ShouldQueue
{
    use Queueable;

    public $contact;

    /**
     * Create a new notification instance.
     */
    public function __construct(EmployeeEmergencyContact $contact)
    {
        $this->contact = $contact;
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

        $message = (new MailMessage)
            ->subject('Emergency Contact Removed')
            ->greeting("Hello {$employee->first_name},")
            ->line('An emergency contact has been removed from your profile.');

        $message->line('**Removed Contact Details:**')
            ->line("Name: {$contact->contact_name}")
            ->line("Relationship: {$contact->relationship}")
            ->line("Primary Phone: {$contact->phone_primary}");

        if ($contact->phone_secondary) {
            $message->line("Secondary Phone: {$contact->phone_secondary}");
        }

        if ($contact->email) {
            $message->line("Email: {$contact->email}");
        }

        if ($contact->is_primary) {
            $message->line('**Important:** This was your primary emergency contact. You should designate a new primary contact as soon as possible.')
                ->line('Please log into your profile and add a new emergency contact or designate an existing one as primary.');
        }

        $message->line('If you did not request this removal or if this was done in error, please contact HR immediately.')
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
            'type' => 'emergency_contact_removed',
            'contact_id' => $this->contact->id,
            'contact_name' => $this->contact->contact_name,
            'relationship' => $this->contact->relationship,
            'is_primary' => $this->contact->is_primary,
            'removed_at' => now(),
            'message' => "Emergency contact {$this->contact->contact_name} ({$this->contact->relationship}) has been removed from your profile.",
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
            'contact_removed',
            'employee_'.$this->contact->employee_id,
            'contact_'.$this->contact->id,
        ];
    }
}
