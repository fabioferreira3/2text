<?php

namespace App\Interfaces;

interface LLMFactoryInterface
{
    public function request(array $messages): array;
}
