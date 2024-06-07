<?php

namespace App\Domain\Agents\Resources;

use App\Domain\Agents\Enums\Agent;

class AgentResource
{
    public Agent $agent;

    public function __construct(Agent $agent)
    {
        $this->agent = $agent;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->agent->id(),
            'name' => $this->agent->label(),
            'slug' => $this->agent->value,
        ];
    }
}
