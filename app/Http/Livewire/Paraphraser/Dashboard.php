<?php

namespace App\Http\Livewire\Paraphraser;

use WireUi\Traits\Actions;
use Livewire\Component;

class Dashboard extends Component
{
    use Actions;

    public $document;

    protected $listeners = ['invokeNew' => 'new'];

    public function render()
    {
        return view('livewire.paraphraser.dashboard')
            ->layout('layouts.app', ['title' => __('paraphraser.paraphraser')]);
    }

    public function new()
    {
        return redirect()->route('new-paraphraser');
    }
}
