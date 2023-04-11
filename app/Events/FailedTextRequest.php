<?php

namespace App\Events;

use App\Models\TextRequest;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class FailedTextRequest
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public TextRequest $textRequest;
    public string $reason;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(TextRequest $textRequest, $reason = '')
    {
        $this->textRequest = $textRequest;
        $this->reason = $reason;
    }
}
