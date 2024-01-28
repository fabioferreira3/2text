<?php

namespace App\Http\Livewire\Product;

use Livewire\Component;


class Purchase extends Component
{
    public $title;

    public function mount()
    {
    }

    public function render()
    {
        return view('livewire.purchase')->layout('layouts.app', ['title' => 'eita']);
    }
}
