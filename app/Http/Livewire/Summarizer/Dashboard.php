<?php

namespace App\Http\Livewire\Summarizer;

use WireUi\Traits\Actions;
use Livewire\Component;

class Dashboard extends Component
{
    use Actions;

    public $document;

    protected $listeners = ['invokeNew' => 'new'];

    public function render()
    {
        return view('livewire.summarizer.dashboard')->layout('layouts.app', ['title' => __('summarizer.blog_posts')]);
    }

    public function new()
    {
        return redirect()->route('new-summarizer');
    }
}
