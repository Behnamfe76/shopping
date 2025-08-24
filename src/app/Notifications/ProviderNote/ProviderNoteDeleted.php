<?php

namespace Fereydooni\Shopping\app\Notifications\ProviderNote;

use Fereydooni\Shopping\app\Models\ProviderNote;
use Fereydooni\Shopping\app\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\URL;

class ProviderNoteDeleted extends Notification implements ShouldQueue
{
    use Queueable;

    protected ProviderNote $providerNote;
    protected string $deletedBy;

    /**
     * Create a new notification instance.
     */
    public function __construct(ProviderNote $providerNote, string $deletedBy = '')
    {
        $this->providerNote = $providerNote;
        $this->deletedBy = $deletedBy;
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
        $deletedBy = $this->deletedBy ?: 'System';

        $mailMessage = (new MailMessage)
            ->subject("Provider Note Deleted - {$provider->name}")
            ->greeting("Hello {$notifiable->name},")
            ->line("A provider note has been deleted for {$provider->name}.")
            ->line("**Note Title:** {$this->providerNote->title}")
            ->line("**Deleted By:** {$deletedBy}")
            ->line("**Deleted At:** " . now()->format('M j, Y g:i A'));

        // Add note details
        $mailMessage->line("**Note Type:** " . ucfirst($this->providerNote->note_type))
            ->line("**Priority:** " . ucfirst($this->providerNote->priority))
            ->line("**Content Preview:** " . substr($this->providerNote->content, 0, 100) . "...");

        // Add action button to view provider
        $mailMessage->action('View Provider', URL::to("/providers/{$provider->id}"))
            ->line('Thank you for using our application!');

        return $mailMessage;
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        $provider = $this->providerNote->provider;
        $deletedBy = $this->deletedBy ?: 'System';

        return [
            'type' => 'provider_note_deleted',
            'title' => "Provider Note Deleted - {$provider->name}",
            'message' => "A provider note has been deleted for {$provider->name} by {$deletedBy}.",
            'provider_note_id' => $this->providerNote->id,
            'provider_id' => $provider->id,
            'provider_name' => $provider->name,
            'deleted_by' => $deletedBy,
            'note_title' => $this->providerNote->title,
            'note_type' => $this->providerNote->note_type,
            'priority' => $this->providerNote->priority,
            'content_preview' => substr($this->providerNote->content, 0, 100) . "...",
            'deleted_at' => now()->toISOString(),
            'action_url' => "/providers/{$provider->id}",
            'icon' => 'delete',
            'color' => 'red'
        ];
    }

    /**
     * Get the notification's database type.
     */
    public function getDatabaseType(): string
    {
        return 'provider_note_deleted';
    }

    /**
     * Determine if the notification should be sent.
     */
    public function shouldSend(object $notifiable): bool
    {
        // Don't send notification to the person who deleted the note
        if ($notifiable->name === $this->deletedBy) {
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
        return "provider_note_deleted_{$this->providerNote->id}_" . now()->timestamp;
    }
}
