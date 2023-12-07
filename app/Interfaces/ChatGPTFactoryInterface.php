<?php

namespace App\Interfaces;

use App\Packages\OpenAI\ChatGPT;

interface ChatGPTFactoryInterface
{
    public function make(): ChatGPT;
}
