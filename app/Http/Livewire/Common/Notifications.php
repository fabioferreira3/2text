<?php

namespace App\Http\Livewire\Common;

use Livewire\Component;


class Notifications extends Component
{
    protected $listeners = ['caralho'];

    public function render()
    {
        return view('livewire.common.notifications');
    }

    public function caralho($message)
    {
        //   dd($message);
        //session()->flash('message', $message);
    }
}
