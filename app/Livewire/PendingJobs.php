<?php

namespace App\Livewire;

use Livewire\Component;

/**
 * @codeCoverageIgnore
 */
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
