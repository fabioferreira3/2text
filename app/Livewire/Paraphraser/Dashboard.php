<?php

namespace App\Livewire\Paraphraser;

use WireUi\Traits\Actions;
use Livewire\Component;

class Dashboard extends Component
{
    use Actions;

    protected $listeners = ['invokeNew' => 'new'];

    public function render()
    {
        return view('livewire.paraphraser.dashboard')
            ->title(__('paraphraser.paraphraser'));
    }

    public function new()
    {
        return redirect()->route('new-paraphraser');
    }
}
