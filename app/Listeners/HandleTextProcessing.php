<?php

namespace App\Listeners;

use App\Events\AudioProcessed;
use App\Jobs\ProcessText;

class HandleTextProcessing
{

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle(AudioProcessed $event)
    {
        ProcessText::dispatch($event->textRequest);
    }
}
