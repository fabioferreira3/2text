<?php

namespace App\Interfaces;

use App\Packages\OpenAI\ChatGPT;

class ChatGPTFactoryInterface implements LLMFactoryInterface
{
    protected $chatgpt;

    public function __construct(ChatGPT $chatgpt)
    {
        $this->chatgpt = $chatgpt;
    }

    public function request(array $messages): array
    {
        return $this->chatgpt->request($messages);
    }
}
