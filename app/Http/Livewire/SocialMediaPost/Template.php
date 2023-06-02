<?php

namespace App\Http\Livewire\SocialMediaPost;

use Livewire\Component;

class Template extends Component
{
    public string $icon = 'hashtag';
    public string $title = 'Social Media Post';
    public string $description = 'Create social media posts using AI';

    public function render()
    {
        return view('livewire.common.template');
    }

    public function execute()
    {
        return redirect()->to('/social-media-post/new');
    }
}
