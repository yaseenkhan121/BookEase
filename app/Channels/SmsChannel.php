<?php

namespace App\Channels;

use App\Services\SmsService;
use Illuminate\Notifications\Notification;

class SmsChannel
{
    protected $smsService;

    public function __construct(SmsService $smsService)
    {
        $this->smsService = $smsService;
    }

    /**
     * Send the given notification.
     *
     * @param  mixed  $notifiable
     * @param  \Illuminate\Notifications\Notification  $notification
     * @return void
     */
    public function send($notifiable, Notification $notification)
    {
        if (!method_exists($notification, 'toSms')) {
            return;
        }

        // Use dynamic method call to avoid IDE warning for unknown method on base Notification class
        $message = $notification->{'toSms'}($notifiable);
        $to = $notifiable->routeNotificationFor('sms', $notification);

        if (!$to) {
            $to = $notifiable->phone_number;
        }

        if ($to && $message) {
            $this->smsService->sendMessage($to, $message);
        }
    }
}
