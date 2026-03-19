<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

use App\Channels\SmsChannel;

class BookingNotification extends Notification implements ShouldQueue, ShouldBroadcast
{
    use Queueable;

    protected $title;
    protected $message;
    protected $actionUrl;

    /**
     * Create a new notification instance.
     */
    public function __construct(string $title, string $message, string $actionUrl = '')
    {
        $this->title = $title;
        $this->message = $message;
        $this->actionUrl = $actionUrl;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via(object $notifiable): array
    {
        return ['database', 'mail', 'broadcast', SmsChannel::class];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $mail = (new MailMessage)
            ->subject('BookEase: ' . $this->title)
            ->greeting('Hello ' . ($notifiable->name ?? 'there') . '!')
            ->line($this->message);

        if ($this->actionUrl) {
            $mail->action('View Booking Details', $this->actionUrl);
        }

        return $mail->line('Thank you for choosing BookEase for your scheduling needs.');
    }

    /**
     * Get the SMS representation of the notification.
     */
    public function toSms(object $notifiable): string
    {
        return "BookEase: {$this->title}. {$this->message}";
    }

    /**
     * Get the array representation for database storage.
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title'      => $this->title,
            'message'    => $this->message,
            'action_url' => $this->actionUrl,
        ];
    }

    /**
     * Get the broadcastable representation of the notification.
     */
    public function toBroadcast(object $notifiable): \Illuminate\Notifications\Messages\BroadcastMessage
    {
        return new \Illuminate\Notifications\Messages\BroadcastMessage([
            'title'      => $this->title,
            'message'    => $this->message,
            'action_url' => $this->actionUrl,
        ]);
    }
}
