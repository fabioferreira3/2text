<?php

namespace App\Domain\Agents\Repositories;

use App\Domain\Thread\Enum\MessageRole;
use App\Domain\Thread\Thread;
use App\Packages\OpenAI\Assistant;

class AgentRepository
{
    public $assistant;

    public function __construct()
    {
        $this->assistant = new Assistant();
    }

    public function createThread(string $content): Thread
    {
        $threadRequest = $this->assistant->createThread();
        $thread = Thread::factory()->create([
            'external_id' => $threadRequest->id
        ]);

        $messageRequest = $this->assistant->createMessage(
            $thread->external_id,
            MessageRole::USER,
            $content
        );

        $thread->messages()->create([
            'external_id' => $messageRequest->id,
            'role' => MessageRole::USER,
            'content' => [
                'text' => $content
            ]
        ]);

        return $thread;
    }
}
