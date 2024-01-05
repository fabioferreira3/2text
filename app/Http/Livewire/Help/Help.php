<?php

namespace App\Http\Livewire\Help;

use Livewire\Component;


class Help extends Component
{
    public $title;

    public function render()
    {
        return view('livewire.help.help');
    }
}
