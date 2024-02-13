<?php

namespace App\Support\Notifications\Channels;

use App\Support\CustomMail;
use Illuminate\Notifications\Notification;

/**
 * @codeCoverageIgnore
 */
class CustomMailChannel
{
    /**
     * Send the given notification.
     *
     * @param mixed $notifiable
     * @param Notification $notification
     * @return void
     */
    public function send($notifiable, Notification $notification)
    {
        $customMail = new CustomMail();
        $notification = $notification->toCustomMail($notifiable);

        $customMail->getMailProvider()->sendDynamicMessage($notification['recipients'], $notification['payload'], $notification['template_id']);
    }
}
