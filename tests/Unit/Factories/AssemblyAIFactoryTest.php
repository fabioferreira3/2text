<?php

use App\Factories\AssemblyAIFactory;
use App\Packages\AssemblyAI\AssemblyAI;

describe('AssemblyAIFactory factory', function () {
    it('creates an instance of AssemblyAI', function () {
        $factory = new AssemblyAIFactory();

        $assemblyAI = $factory->make();

        expect($assemblyAI)->toBeInstanceOf(AssemblyAI::class);
    });
})->group('factories');
