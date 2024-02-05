<?php

namespace App\Livewire;

use Livewire\Component;


class Templates extends Component
{
    public function render()
    {
        return view('livewire.templates')->title(__('common.tools'));
    }
}
