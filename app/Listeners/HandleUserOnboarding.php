<?php

namespace App\Listeners;

use App\Events\UserCreated;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 * @codeCoverageIgnore
 */
class HandleUserOnboarding implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Handle the event.
     *
     * @param UserCreated $event
     * @return void
     */
    public function handle(UserCreated $event)
    {
        $event->user->account->addUnits(100, [
            'onboarding'
        ]);
    }
}
