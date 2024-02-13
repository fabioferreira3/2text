<?php

use App\Repositories\UnitRepository;

beforeEach(function () {
    $this->be($this->authUser);
    $this->repository = new UnitRepository();
});

describe(
    'UnitRepository',
    function () {
        it('validates cost successfully', function () {
            $this->authUser->account->units = 99999;
            expect($this->repository->validateCost('wordsGeneration', ['word_count' => 480]))->toBeTrue();
            expect($this->repository->validateCost('imageGeneration', ['img_count' => 10]))->toBeTrue();
            expect($this->repository->validateCost('audioTranscription', ['duration' => 500]))->toBeTrue();
            expect($this->repository->validateCost('audioGeneration', ['word_count' => 700]))->toBeTrue();
        });

        it('fails to validate cost due to insufficient units', function () {
            $this->authUser->account->units = 5;
            expect($this->repository->validateCost('wordsGeneration', ['word_count' => 10000]))->toBeFalse();
        });

        it('estimates cost accurately', function () {
            expect($this->repository->estimateCost('wordsGeneration', ['word_count' => 480]))->toEqual('1.00000000');
            expect($this->repository->estimateCost('imageGeneration', ['img_count' => 10]))->toEqual(10);
            expect($this->repository->estimateCost('audioTranscription', ['duration' => 100]))->toEqual(10.0);
            expect($this->repository->estimateCost('audioGeneration', ['word_count' => 700]))->toEqual(10.0);
        });

        it('handles words generation cost calculation', function () {
            $cost = $this->repository->handleWordsGeneration(['word_count' => 480]);
            expect($cost)->toEqual('1.00000000');
        });

        it('handles image generation cost calculation', function () {
            $cost = $this->repository->handleImageGeneration(['img_count' => 5]);
            expect($cost)->toEqual(5);
        });

        it('handles audio transcription cost calculation', function () {
            $cost = $this->repository->handleAudioTranscription(['duration' => 200]);
            expect($cost)->toEqual(20.0);
        });

        it('handles audio generation cost calculation', function () {
            $cost = $this->repository->handleAudioGeneration(['word_count' => 700]);
            expect($cost)->toEqual(10.0);
        });
    }
)->group('repositories');
