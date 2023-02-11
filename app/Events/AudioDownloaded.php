<?php

namespace App\Events;

use App\Models\TextRequest;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AudioDownloaded
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public string $filePath;
    public string $fileName;
    public TextRequest $textRequest;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(string $filePath, string $fileName, TextRequest $textRequest)
    {
        $this->filePath = $filePath;
        $this->fileName = $fileName;
        $this->textRequest = $textRequest;
    }
}
