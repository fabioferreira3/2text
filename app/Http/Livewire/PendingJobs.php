<?php

namespace App\Http\Livewire;

use Livewire\Component;


class PendingJobs extends Component
{

    public function __construct()
    {
    }

    public function render()
    {
        return view('livewire.blog.pending');
    }
}
