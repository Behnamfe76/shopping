<?php

namespace Fereydooni\Shopping\App\Notifications\ProviderRating;

use App\Models\ProviderRating;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class RatingFlagged extends Notification implements ShouldQueue
{
    use Queueable;

    public ProviderRating $rating;

    public ?string $reason;

    /**
     * Create a new notification instance.
     */
    public function __construct(ProviderRating $rating, ?string $reason = null)
    {
        $this->rating = $rating;
        $this->reason = $reason;
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
        $provider = $this->rating->provider;
        $user = $this->rating->user;

        return (new MailMessage)
            ->subject('Provider rating flagged for moderation')
            ->greeting("Hello {$notifiable->name},")
            ->line('A provider rating has been flagged and requires your attention.')
            ->line("Provider: {$provider->name}")
            ->line("User: {$user->name}")
            ->line("Rating: {$this->rating->rating_value}/{$this->rating->max_rating} stars")
            ->line("Category: {$this->rating->category}")
            ->when($this->rating->title, function ($mail) {
                return $mail->line("Title: {$this->rating->title}");
            })
            ->when($this->rating->comment, function ($mail) {
                return $mail->line("Comment: {$this->rating->comment}");
            })
            ->when($this->reason, function ($mail) {
                return $mail->line("Flag Reason: {$this->reason}");
            })
            ->action('Review Rating', url("/admin/ratings/{$this->rating->id}/moderate"))
            ->line('Please review this rating as soon as possible.')
            ->salutation('Best regards, The Team');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'rating_flagged',
            'rating_id' => $this->rating->id,
            'provider_id' => $this->rating->provider_id,
            'provider_name' => $this->rating->provider->name ?? 'Unknown Provider',
            'user_id' => $this->rating->user_id,
            'user_name' => $this->rating->user->name ?? 'Unknown User',
            'rating_value' => $this->rating->rating_value,
            'max_rating' => $this->rating->max_rating,
            'category' => $this->rating->category,
            'title' => $this->rating->title,
            'comment' => $this->rating->comment,
            'reason' => $this->reason,
            'flagged_at' => now(),
        ];
    }

    /**
     * Get the notification's database type.
     */
    public function getDatabaseType(): string
    {
        return 'rating_flagged';
    }
}
