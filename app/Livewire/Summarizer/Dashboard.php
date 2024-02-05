<?php

namespace App\Livewire\Summarizer;

use WireUi\Traits\Actions;
use Livewire\Component;

class Dashboard extends Component
{
    use Actions;

    protected $listeners = ['invokeNew' => 'new'];

    public function render()
    {
        return view('livewire.summarizer.dashboard')->title(__('summarizer.title'));
    }

    public function new()
    {
        return redirect()->route('new-summarizer');
    }
}
