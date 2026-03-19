<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Notifications\Messages\BroadcastMessage;

class ProviderStatusNotification extends Notification implements ShouldQueue, ShouldBroadcast
{
    use Queueable;

    protected $status;
    protected $reason;

    /**
     * Create a new notification instance.
     */
    public function __construct(string $status, string $reason = '')
    {
        $this->status = $status;
        $this->reason = $reason;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database', 'broadcast'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $subject = 'BookEase: ';
        $greeting = 'Hello ' . $notifiable->name . '!';
        $line1 = '';
        $actionText = '';
        $actionUrl = '';

        switch ($this->status) {
            case 'approved':
                $subject .= 'Account Approved!';
                $line1 = 'Great news! Your service provider account has been reviewed and approved by our team.';
                $actionText = 'Go to Dashboard';
                $actionUrl = route('dashboard');
                break;
            case 'rejected':
                $subject .= 'Application Update';
                $line1 = 'We have reviewed your application to join BookEase as a provider. Unfortunately, we are unable to approve your account at this time.';
                break;
            case 'suspended':
                $subject .= 'Account Suspended';
                $line1 = 'Your provider account has been suspended. You will not be able to accept new bookings or access your dashboard until this is resolved.';
                break;
        }

        $mail = (new MailMessage)
            ->subject($subject)
            ->greeting($greeting)
            ->line($line1);

        if ($this->reason) {
            $mail->line('Reason/Notes: ' . $this->reason);
        }

        if ($actionUrl) {
            $mail->action($actionText, $actionUrl);
        }

        return $mail->line('If you have any questions, please reply to this email or contact support.');
    }

    /**
     * Get the array representation for database storage.
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title'   => 'Account ' . ucfirst($this->status),
            'message' => $this->getMessage(),
            'status'  => $this->status,
        ];
    }

    /**
     * Get the broadcastable representation of the notification.
     */
    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage([
            'title'   => 'Account ' . ucfirst($this->status),
            'message' => $this->getMessage(),
            'status'  => $this->status,
        ]);
    }

    private function getMessage(): string 
    {
        if ($this->status === 'approved') return 'Your provider account has been approved.';
        if ($this->status === 'rejected') return 'Your provider account request was rejected.';
        return 'Your provider account has been ' . $this->status . '.';
    }
}
