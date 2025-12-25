<?php

namespace App\Notifications\EmployeeEmergencyContact;

use App\Models\EmployeeEmergencyContact;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class EmergencyContactUpdated extends Notification implements ShouldQueue
{
    use Queueable;

    public $contact;

    public $changes;

    /**
     * Create a new notification instance.
     */
    public function __construct(EmployeeEmergencyContact $contact, array $changes = [])
    {
        $this->contact = $contact;
        $this->changes = $changes;
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
        $changes = $this->changes;

        $message = (new MailMessage)
            ->subject('Emergency Contact Updated')
            ->greeting("Hello {$employee->first_name},")
            ->line('Your emergency contact information has been updated.');

        if (! empty($changes)) {
            $message->line('**Changes Made:**');
            foreach ($changes as $field => $value) {
                $fieldName = ucwords(str_replace('_', ' ', $field));
                $message->line("• {$fieldName}: {$value}");
            }
        }

        $message->line('**Current Contact Details:**')
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
            $message->line('**This contact is your primary emergency contact.**');
        }

        $message->line('If you did not request this change or if any information is incorrect, please contact HR immediately.')
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
            'type' => 'emergency_contact_updated',
            'contact_id' => $this->contact->id,
            'contact_name' => $this->contact->contact_name,
            'relationship' => $this->contact->relationship,
            'is_primary' => $this->contact->is_primary,
            'changes' => $this->changes,
            'updated_at' => now(),
            'message' => "Emergency contact {$this->contact->contact_name} ({$this->contact->relationship}) has been updated.",
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
            'employee_'.$this->contact->employee_id,
            'contact_'.$this->contact->id,
        ];
    }
}
