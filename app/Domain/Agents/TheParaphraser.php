<?php

namespace App\Domain\Agents;

use App\Domain\Agents\Enums\Agent;
use App\Domain\Agents\Interfaces\AgentInterface;
use App\Domain\Agents\Repositories\AgentRepository;
use App\Domain\Agents\Resources\AgentResource;
use App\Domain\Agents\Traits\AgentActions;
use App\Models\DocumentThread;

class TheParaphraser implements AgentInterface
{
    use AgentActions;

    public $resource;

    public function __construct()
    {
        $this->resource = (new AgentResource(Agent::THE_PARAPHRASER))->toArray();
    }

    public function process(string $content, array $params)
    {
        dispatch(function () use ($content, $params) {
            $agentRepo = new AgentRepository();
            $thread = $agentRepo->createThread($content);
            DocumentThread::create([
                'document_id' => $params['document_id'],
                'thread_id' => $thread->id,
            ]);
            $this->run($thread, [
                'agent' => Agent::THE_PARAPHRASER->value,
                'document_id' => $params['document_id'],
                'user_id' => $params['user_id'],
                'sentence_order' => $params['sentence_order']
            ]);
        });
    }
}
