<?php

namespace App\Livewire\UnitCalculator;

use App\Repositories\CheckoutRepository;
use WireUi\Traits\Actions;
use Livewire\Component;

class UnitCalculator extends Component
{
    use Actions;

    public $units = 0;
    public $wordsCount = 0;
    public $imagesCount = 0;
    public $transcriptionLength = 0;
    public $audioWordsCount = 0;

    public function rules()
    {
        return [
            'units' => 'integer|min:100|max:10000'
        ];
    }

    public function messages()
    {
        return [
            'units.min' => 'You may purchase a minimum of 100 units'
        ];
    }

    public function render()
    {
        return view('livewire.unit-calculator.unit-calculator');
    }

    public function updatedUnits($value)
    {
        $this->wordsCount = number_format(($value * 48000) / 100);
        $this->imagesCount = number_format(($value * 75) / 100);
        $this->transcriptionLength = number_format((($value * 1000) / 100) / 60);
        $this->audioWordsCount = number_format((($value * 56000) / 100) / 8);
    }

    public function processPurchase()
    {
        $this->validate();
        $discount = 0;
        if ($this->units >= 500 && $this->units < 1000) {
            $discount = 3;
        } elseif ($this->units >= 1000 && $this->units < 10001) {
            $discount = 7;
        }
        $checkoutRepo = new CheckoutRepository();

        return $checkoutRepo->processUnitPurchase($this->units, $discount);
    }
}
