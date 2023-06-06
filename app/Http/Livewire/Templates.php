<?php

namespace App\Http\Livewire;

use Livewire\Component;


class Templates extends Component
{
    public $title;

    public function mount()
    {
        $this->title = 'Templates';
    }

    public function render()
    {
        return view('livewire.templates')->layout('layouts.app', ['title' => $this->title]);
    }
}
