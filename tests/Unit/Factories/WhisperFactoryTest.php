<?php

use App\Factories\WhisperFactory;
use App\Packages\Whisper\Whisper;

describe('WhisperFactory factory', function () {
    it('creates an instance of Whisper', function () {
        $factory = new WhisperFactory();

        $whisper = $factory->make('file.mp3');

        expect($whisper)->toBeInstanceOf(Whisper::class);
    });
})->group('factories');
