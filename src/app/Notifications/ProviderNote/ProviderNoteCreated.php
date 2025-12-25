<?php

namespace Fereydooni\Shopping\app\Notifications\ProviderNote;

use Fereydooni\Shopping\app\DTOs\ProviderNoteDTO;
use Fereydooni\Shopping\app\Models\ProviderNote;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ProviderNoteCreated extends Notification implements ShouldQueue
{
    use Queueable;

    public ProviderNote $providerNote;

    public ProviderNoteDTO $providerNoteDTO;

    /**
     * Create a new notification instance.
     */
    public function __construct(ProviderNote $providerNote, ProviderNoteDTO $providerNoteDTO)
    {
        $this->providerNote = $providerNote;
        $this->providerNoteDTO = $providerNoteDTO;
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
        $title = $this->providerNote->title ?? $this->providerNote->note ?? 'New Provider Note';
        $noteType = $this->providerNote->note_type ?? $this->providerNote->type ?? 'general';
        $priority = $this->providerNote->priority ?? 'medium';

        $mailMessage = (new MailMessage)
            ->subject("New {$noteType} Note: {$title}")
            ->greeting("Hello {$notifiable->name},")
            ->line('A new provider note has been created.')
            ->line("**Title:** {$title}")
            ->line('**Type:** '.ucfirst($noteType))
            ->line('**Priority:** '.ucfirst($priority))
            ->line("**Provider ID:** {$this->providerNote->provider_id}");

        if ($this->providerNote->content || $this->providerNote->note) {
            $content = $this->providerNote->content ?? $this->providerNote->note;
            $mailMessage->line('**Content:** '.substr($content, 0, 200).(strlen($content) > 200 ? '...' : ''));
        }

        $mailMessage->action('View Note', url("/provider-notes/{$this->providerNote->id}"))
            ->line('Thank you for using our application!');

        return $mailMessage;
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'id' => $this->providerNote->id,
            'type' => 'provider_note_created',
            'title' => $this->providerNote->title ?? $this->providerNote->note ?? 'New Provider Note',
            'provider_id' => $this->providerNote->provider_id,
            'user_id' => $this->providerNote->user_id,
            'note_type' => $this->providerNote->note_type ?? $this->providerNote->type ?? 'general',
            'priority' => $this->providerNote->priority ?? 'medium',
            'is_private' => $this->providerNote->is_private ?? ! ($this->providerNote->is_public ?? true),
            'created_at' => $this->providerNote->created_at?->toISOString(),
            'message' => 'A new '.($this->providerNote->note_type ?? $this->providerNote->type ?? 'general')." note has been created for provider {$this->providerNote->provider_id}",
        ];
    }

    /**
     * Get the notification's database type.
     */
    public function toDatabase(object $notifiable): array
    {
        return $this->toArray($notifiable);
    }
}
