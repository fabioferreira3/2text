<?php

namespace App\Http\Livewire;

use Livewire\Component;


class Templates extends Component
{
    public function render()
    {
        return view('livewire.templates')->layout('layouts.app', ['title' => __('common.tools')]);
    }
}
