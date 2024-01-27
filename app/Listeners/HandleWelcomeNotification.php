<?php

namespace App\Listeners;

use App\Events\UserCreated;
use App\Notifications\WelcomeNotification;

class HandleWelcomeNotification
{
    /**
     * Handle the event.
     *
     * @param UserCreated $event
     * @return void
     */
    public function handle(UserCreated $event)
    {
        $event->user->notify(new WelcomeNotification());
    }
}
