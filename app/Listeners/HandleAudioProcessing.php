<?php

namespace App\Listeners;

use App\Events\AudioDownloaded;
use App\Jobs\ProcessAudio;

class HandleAudioProcessing
{

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle(AudioDownloaded $event)
    {
        ProcessAudio::dispatch($event->filePath, $event->fileName, $event->language);
    }
}
