<?php

namespace App\Factories;

use App\Enums\AIModel;
use App\Interfaces\ChatGPTFactoryInterface;
use App\Interfaces\ClaudeFactoryInterface;
use App\Interfaces\LLMFactoryInterface;
use App\Packages\Anthropic\Claude;
use App\Packages\OpenAI\ChatGPT;

class LLMFactory
{
    public function make(string $llm = 'chatgpt', string $model = null): LLMFactoryInterface
    {
        switch ($llm) {
            case 'claude':
                return new ClaudeFactoryInterface(new Claude($model));
            case 'chatgpt':
            default:
                return new ChatGPTFactoryInterface(new ChatGPT($model));
        }
    }
}
