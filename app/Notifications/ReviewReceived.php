<?php

namespace App\Notifications;

use App\Models\Review;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ReviewReceived extends Notification
{
    use Queueable;

    protected $review;

    /**
     * Create a new notification instance.
     */
    public function __construct(Review $review)
    {
        $this->review = $review;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
                    ->subject('New Review Received')
                    ->line('A customer has left a review for your services.')
                    ->line('Rating: ' . str_repeat('⭐', $this->review->rating))
                    ->line('Review: ' . $this->review->review_text)
                    ->action('View Review', route('dashboard'))
                    ->line('Thank you for providing great service!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'review_id'   => $this->review->id,
            'customer_name' => $this->review->customer->name,
            'rating'      => $this->review->rating,
            'message'     => 'New ' . $this->review->rating . '-star review from ' . $this->review->customer->name,
            'type'        => 'review',
        ];
    }
}
