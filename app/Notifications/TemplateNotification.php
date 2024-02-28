<?php

namespace App\Notifications;

use App\Support\CustomMail;
use App\Support\Notifications\Channels\CustomMailChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class TemplateNotification extends Notification
{
    use Queueable;

    public $templateId;
    private $customMail;

    /**
     * Create a new notification instance.
     *
     * @param $digestReport
     */
    public function __construct(string $templateId)
    {
        $this->customMail = new CustomMail();
        $this->templateId = $templateId;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return [CustomMailChannel::class];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param mixed $notifiable
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
            'template_id' => $this->templateId
        ];
    }
}
