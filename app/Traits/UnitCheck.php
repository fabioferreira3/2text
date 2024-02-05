<?php

namespace App\Traits;

use App\Exceptions\InsufficientUnitsException;
use App\Repositories\UnitRepository;
use Illuminate\Support\Facades\Log;

trait UnitCheck
{
    public $totalCost = 0;

    public function authorizeTotalCost($account = null)
    {
        Log::debug($this->totalCost);
        $units = $account ? $account->units : auth()->user()->account->units;
        if ($units < ($this->totalCost)) {
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

    public function estimateAudioTranscriptionCost(int $duration)
    {
        return $duration * 0.1;
    }
}
