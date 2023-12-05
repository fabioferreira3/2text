<?php

namespace App\Http\Livewire\SocialMediaPost;

use WireUi\Traits\Actions;
use Livewire\Component;

class Dashboard extends Component
{
    use Actions;

    public $document;

    protected $listeners = ['invokeNew' => 'new'];

    public function render()
    {
        return view('livewire.social-media-post.dashboard')->layout('layouts.app');
    }

    public function new()
    {
        return redirect()->route('new-social-media-post');
    }
}
