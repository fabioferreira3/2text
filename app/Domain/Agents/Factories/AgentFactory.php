<?php

namespace App\Domain\Agents\Factories;

use App\Domain\Agents\Enums\Agent;
use App\Domain\Agents\Interfaces\AgentInterface;

class AgentFactory
{
    public function make(Agent $agent): AgentInterface
    {
        return new \App\Domain\Agents\TheAgent($agent);
    }
}
