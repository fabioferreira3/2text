<?php

namespace App\Notifications;

use App\Support\CustomMail;
use App\Support\Notifications\Channels\CustomMailChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class WelcomeNotification extends Notification
{
    use Queueable;

    private $customMail;

    /**
     * Create a new notification instance.
     *
     * @param $digestReport
     */
    public function __construct()
    {
        $this->customMail = new CustomMail();
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return [CustomMailChannel::class];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toCustomMail($notifiable)
    {
        return [
            'recipients' => [[
                'name' => $notifiable->name,
                'email' => $notifiable->email
            ]],
            'payload' => [
                'first_name' => $notifiable->name
            ],
            'template_id' => 'd-0809ad8ab17e423d8401acff70dd6eee'
        ];
    }
}
