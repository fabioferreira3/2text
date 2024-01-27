<?php

namespace App\Listeners;

use App\Events\UserCreated;

class HandleStripeUserCreation
{
    /**
     * Handle the event.
     *
     * @param UserCreated $event
     * @return void
     */
    public function handle(UserCreated $event)
    {
        $event->user->createAsStripeCustomer();
    }
}
