<?php

namespace Fereydooni\Shopping\App\Notifications\ProviderRating;

use App\Models\ProviderRating;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class RatingRejected extends Notification implements ShouldQueue
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

        $mail = (new MailMessage)
            ->subject('Your provider rating has been reviewed')
            ->greeting("Hello {$notifiable->name},")
            ->line('We have reviewed your rating for ' . $provider->name . ' and unfortunately, it does not meet our community guidelines.')
            ->line("Rating: {$this->rating->rating_value}/{$this->rating->max_rating} stars")
            ->line("Category: {$this->rating->category}");

        if ($this->reason) {
            $mail->line("Reason: {$this->reason}");
        }

        $mail->line('You can submit a new rating that complies with our guidelines.')
            ->action('Submit New Rating', url("/provider/{$provider->id}/rate"))
            ->line('Thank you for understanding.')
            ->salutation('Best regards, The Team');

        return $mail;
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'rating_rejected',
            'rating_id' => $this->rating->id,
            'provider_id' => $this->rating->provider_id,
            'provider_name' => $this->rating->provider->name ?? 'Unknown Provider',
            'rating_value' => $this->rating->rating_value,
            'max_rating' => $this->rating->max_rating,
            'category' => $this->rating->category,
            'reason' => $this->reason,
            'rejected_at' => now(),
        ];
    }

    /**
     * Get the notification's database type.
     */
    public function getDatabaseType(): string
    {
        return 'rating_rejected';
    }
}
