<?php

namespace App\Domain\Agents\Interfaces;

use App\Domain\Thread\Thread;

interface AgentInterface
{
    public function process(string $content, array $params);
    public function run(Thread $thread, array $metadata = []);
}
