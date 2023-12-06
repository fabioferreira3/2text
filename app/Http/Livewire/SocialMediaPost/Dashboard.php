<?php

namespace App\Http\Livewire\SocialMediaPost;

use WireUi\Traits\Actions;
use Livewire\Component;

class Dashboard extends Component
{
    use Actions;

    protected $listeners = ['invokeNew' => 'new'];

    public function render()
    {
        return view('livewire.social-media-post.dashboard')->layout('layouts.app', ['title' => __('social_media.title')]);
    }

    public function new()
    {
        return redirect()->route('new-social-media-post');
    }
}
