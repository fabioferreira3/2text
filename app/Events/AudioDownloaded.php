<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AudioDownloaded
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public string $filePath;
    public string $fileName;
    public string $language;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(string $filePath, string $fileName, string $language)
    {
        $this->filePath = $filePath;
        $this->fileName = $fileName;
        $this->language = $language;
    }
}
