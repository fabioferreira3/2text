<?php

namespace App\Http\Livewire\SocialMediaPost;

use Livewire\Component;

class Template extends Component
{
    public string $icon;
    public string $title;
    public string $description;

    public function __construct()
    {
        $this->icon = 'hashtag';
        $this->title = __('templates.social_media_post');
        $this->description = __('templates.create_social_media');
    }

    public function render()
    {
        return view('livewire.common.template');
    }

    public function execute()
    {
        return redirect()->to('/social-media-post/new');
    }
}
