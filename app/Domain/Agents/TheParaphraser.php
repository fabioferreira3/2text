<?php

namespace App\Domain\Agents;

use App\Domain\Agents\Enums\Agent;
use App\Domain\Agents\Interfaces\AgentInterface;
use App\Domain\Agents\Resources\AgentResource;
use App\Domain\Thread\Thread;

class TheParaphraser implements AgentInterface
{
    public $resource;

    public function __construct()
    {
        $this->resource = (new AgentResource(Agent::THE_PARAPHRASER))->toArray();
    }

    public function run(Thread $thread): array
    {
        return [];
    }
}
