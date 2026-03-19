<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;


class VerificationOtp extends Notification implements ShouldQueue
{
    use Queueable;

    protected $code;
    protected $type;

    /**
     * Create a new notification instance.
     */
    public function __construct($code, $type)
    {
        $this->code = $code;
        $this->type = $type;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $subject = match($this->type) {
            'password_change' => 'Password Change Verification Code',
            'email' => 'Email Verification Code',
            'phone' => 'Mobile Verification Code',
            default => 'Verification Code'
        };

        return (new MailMessage)
            ->subject($subject)
            ->greeting('Hello!')
            ->line('Your verification code is:')
            ->line("**{$this->code}**")
            ->line('This code will expire in 10 minutes.')
            ->line('If you did not request this code, please ignore this email.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'code' => $this->code,
            'type' => $this->type,
        ];
    }
}
