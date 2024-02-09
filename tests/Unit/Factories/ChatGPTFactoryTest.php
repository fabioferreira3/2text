<?php

use App\Factories\ChatGPTFactory;
use App\Packages\OpenAI\ChatGPT;

describe('ChatGPTFactory factory', function () {
    it('creates an instance of ChatGPT', function () {
        $factory = new ChatGPTFactory();

        $chatGpt = $factory->make();

        expect($chatGpt)->toBeInstanceOf(ChatGPT::class);
    });
})->group('factories');
