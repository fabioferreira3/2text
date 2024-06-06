<?php

use App\Factories\LLMFactory;
use App\Interfaces\ChatGPTFactoryInterface;
use App\Interfaces\ClaudeFactoryInterface;

describe('LLms factory', function () {
    it('creates an instance of ChatGPT', function () {
        $factory = app(LLMFactory::class);
        $chatGptFactory = $factory->make('chatgpt');

        expect($chatGptFactory)->toBeInstanceOf(ChatGPTFactoryInterface::class);
    });

    it('creates an instance of Claude', function () {
        $factory = app(LLMFactory::class);
        $claudeFactory = $factory->make('claude');

        expect($claudeFactory)->toBeInstanceOf(ClaudeFactoryInterface::class);
    });
})->group('factories');
