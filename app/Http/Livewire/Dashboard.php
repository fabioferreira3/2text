<?php

namespace App\Http\Livewire;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Livewire\Component;


class Dashboard extends Component
{
    public $title;

    public function mount()
    {
        $this->title = 'Dashboard';
    }

    public function getListeners()
    {
        $userId = Auth::user()->id;
        return [
            "echo-private:User.$userId,TestEvent" => 'notify',
        ];
    }

    public function render()
    {
        return view('livewire.dashboard')->layout('layouts.app', ['title' => $this->title]);
    }

    public function notify($event)
    {
        Log::debug($event);
    }
}
