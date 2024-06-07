<?php

namespace App\Domain\Agents\Interfaces;

use App\Domain\Thread\Thread;

interface AgentInterface
{
    public function run(Thread $thread): array;
}
