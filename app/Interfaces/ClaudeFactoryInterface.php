<?php

namespace App\Interfaces;

use App\Packages\Anthropic\Claude;

class ClaudeFactoryInterface implements LLMFactoryInterface
{
    protected $claude;

    public function __construct(Claude $claude)
    {
        $this->claude = $claude;
    }

    public function request(array $messages): array
    {
        return $this->claude->request($messages);
    }
}
