<?php

namespace App\Http\Livewire;

use Livewire\Component;


class Dashboard extends Component
{
    public $title;
    //public $selectedTab = 'dashboard';
    public $selectedTab = 'images';

    public function mount()
    {
        $this->title = 'Dashboard';
    }

    public function render()
    {
        return view('livewire.dashboard')->layout('layouts.app', ['title' => $this->title]);
    }
}
