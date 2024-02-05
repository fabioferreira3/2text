<?php

namespace App\Livewire\Help;

use Livewire\Component;

class Help extends Component
{
    public $title;
    public $content;

    public function render()
    {
        return view('livewire.help.help');
    }
}
