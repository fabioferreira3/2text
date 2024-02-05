<?php

namespace App\Livewire;

use Livewire\Component;

class Tooltip extends Component
{
    public $content;

    public function render()
    {
        return view('livewire.tooltip');
    }
}
