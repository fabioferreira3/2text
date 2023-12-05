<?php

namespace App\Http\Livewire\Blog;

use WireUi\Traits\Actions;
use Livewire\Component;

class Dashboard extends Component
{
    use Actions;

    public $document;

    protected $listeners = ['invokeNew' => 'new'];

    public function render()
    {
        return view('livewire.blog.dashboard')->layout('layouts.app', []);
    }

    public function new()
    {
        return redirect()->route('new-post');
    }
}
