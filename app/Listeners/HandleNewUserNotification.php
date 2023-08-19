<?php

namespace App\Listeners;

use App\Events\UserCreated;
use App\Mail\NewUserEmail;
use Illuminate\Support\Facades\Mail;

class HandleNewUserNotification
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param UserCreated $event
     * @return void
     */
    public function handle(UserCreated $event)
    {
        Mail::to('contact@experior.ai')->send(new NewUserEmail([
            'name' => $event->user->name,
            'email' => $event->user->email
        ]));
    }
}
