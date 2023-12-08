<?php

namespace App\Factories;

use App\Interfaces\ChatGPTFactoryInterface;
use App\Packages\OpenAI\ChatGPT;

class ChatGPTFactory implements ChatGPTFactoryInterface
{
    public function make(): ChatGPT
    {
        return new ChatGPT();
    }
}
