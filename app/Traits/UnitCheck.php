<?php

namespace App\Traits;

use App\Exceptions\InsufficientUnitsException;
use App\Repositories\UnitRepository;
use Illuminate\Support\Facades\Log;

trait UnitCheck
{
    public $totalCost = 0;

    public function estimateCost($taskType, array $meta)
    {
        $unitRepo = new UnitRepository();
        $this->totalCost = $this->totalCost + $unitRepo->estimateCost($taskType, $meta);
    }

    public function authorizeCost($taskType, array $meta)
    {
        $this->estimateCost($taskType, $meta);
        $this->authorizeTotalCost(true);
    }

    public function authorizeTotalCost($hardLimit = false)
    {
        $totalCost = $hardLimit ? $this->totalCost : $this->totalCost - ($this->totalCost * 0.1);
        Log::debug($totalCost);
        if (auth()->user()->account->units < ($totalCost)) {
            throw new InsufficientUnitsException();
        }
    }
}
