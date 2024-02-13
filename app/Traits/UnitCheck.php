<?php

namespace App\Traits;

use App\Exceptions\InsufficientUnitsException;
use App\Repositories\UnitRepository;

trait UnitCheck
{
    public $totalCost = 0;

    public function authorizeTotalCost($account = null)
    {
        $units = $account ? $account->units : auth()->user()->account->units;
        if ($units < $this->totalCost) {
            throw new InsufficientUnitsException();
        }
    }

    public function estimateWordsGenerationCost(int $wordCount)
    {
        $unitRepo = new UnitRepository();
        $this->totalCost = $this->totalCost +
            (($this->totalCost + $unitRepo->estimateCost('words_generation', [
                'word_count' => $wordCount
            ])) * 0.1);
    }

    public function estimateImageGenerationCost(int $imgCount)
    {
        $unitRepo = new UnitRepository();
        $this->totalCost = $this->totalCost + $unitRepo->estimateCost('image_generation', [
            'img_count' => $imgCount
        ]);
    }

    public function estimateAudioGenerationCost(int $imgCount)
    {
        $unitRepo = new UnitRepository();
        $this->totalCost = $this->totalCost + $unitRepo->estimateCost('audio_generation', [
            'word_count' => $imgCount
        ]);
    }

    public function estimateAudioTranscriptionCost(int $duration)
    {
        $this->totalCost = $this->totalCost + ($duration * 0.1);
    }
}
