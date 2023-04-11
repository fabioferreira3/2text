<?php

namespace App\Listeners;

use App\Events\FailedTextRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class HandleFailedTextRequest implements ShouldQueue
{
    use InteractsWithQueue, Queueable;
    /**
     * Handle the event.
     *
     * @param FailedTextRequest $event
     * @return void
     */
    public function handle(FailedTextRequest $event)
    {
        $event->textRequest->update(['status' => 'failed']);
    }
}
