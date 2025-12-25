<?php

namespace Fereydooni\Shopping\app\Notifications\ProviderNote;

use Fereydooni\Shopping\app\Models\ProviderNote;
use Fereydooni\Shopping\app\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\URL;

class ProviderNoteUpdated extends Notification implements ShouldQueue
{
    use Queueable;

    protected ProviderNote $providerNote;

    protected array $changes;

    /**
     * Create a new notification instance.
     */
    public function __construct(ProviderNote $providerNote, array $changes = [])
    {
        $this->providerNote = $providerNote;
        $this->changes = $changes;
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
        $provider = $this->providerNote->provider;
        $updater = $this->providerNote->user;

        $mailMessage = (new MailMessage)
            ->subject("Provider Note Updated - {$provider->name}")
            ->greeting("Hello {$notifiable->name},")
            ->line("A provider note has been updated for {$provider->name}.")
            ->line("**Note Title:** {$this->providerNote->title}")
            ->line("**Updated By:** {$updater->name}")
            ->line('**Updated At:** '.$this->providerNote->updated_at->format('M j, Y g:i A'));

        // Add change details if available
        if (! empty($this->changes)) {
            $mailMessage->line('**Changes Made:**');
            foreach ($this->changes as $field => $change) {
                if (isset($change['old']) && isset($change['new'])) {
                    $mailMessage->line("- **{$field}:** {$change['old']} → {$change['new']}");
                }
            }
        }

        // Add note details
        $mailMessage->line('**Note Type:** '.ucfirst($this->providerNote->note_type))
            ->line('**Priority:** '.ucfirst($this->providerNote->priority))
            ->line('**Status:** '.($this->providerNote->is_archived ? 'Archived' : 'Active'));

        // Add action button
        $mailMessage->action('View Provider Note', URL::to("/providers/{$provider->id}/notes/{$this->providerNote->id}"))
            ->line('Thank you for using our application!');

        return $mailMessage;
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        $provider = $this->providerNote->provider;
        $updater = $this->providerNote->user;

        return [
            'type' => 'provider_note_updated',
            'title' => "Provider Note Updated - {$provider->name}",
            'message' => "A provider note has been updated for {$provider->name} by {$updater->name}.",
            'provider_note_id' => $this->providerNote->id,
            'provider_id' => $provider->id,
            'provider_name' => $provider->name,
            'updater_id' => $updater->id,
            'updater_name' => $updater->name,
            'note_title' => $this->providerNote->title,
            'note_type' => $this->providerNote->note_type,
            'priority' => $this->providerNote->priority,
            'is_archived' => $this->providerNote->is_archived,
            'changes' => $this->changes,
            'updated_at' => $this->providerNote->updated_at->toISOString(),
            'action_url' => "/providers/{$provider->id}/notes/{$this->providerNote->id}",
            'icon' => 'edit',
            'color' => 'blue',
        ];
    }

    /**
     * Get the notification's database type.
     */
    public function getDatabaseType(): string
    {
        return 'provider_note_updated';
    }

    /**
     * Determine if the notification should be sent.
     */
    public function shouldSend(object $notifiable): bool
    {
        // Don't send notification to the person who updated the note
        if ($notifiable->id === $this->providerNote->user_id) {
            return false;
        }

        // Check if user has permission to view provider notes
        return $notifiable->can('provider-note.view') ||
               $notifiable->can('provider-note.view-own') ||
               $notifiable->can('provider-note.view-team') ||
               $notifiable->can('provider-note.view-department') ||
               $notifiable->can('provider-note.view-all');
    }

    /**
     * Get the notification's unique identifier.
     */
    public function getKey(): string
    {
        return "provider_note_updated_{$this->providerNote->id}_{$this->providerNote->updated_at->timestamp}";
    }
}
