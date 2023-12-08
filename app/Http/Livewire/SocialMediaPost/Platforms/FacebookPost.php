<?php

namespace App\Http\Livewire\SocialMediaPost\Platforms;

use App\Models\Document;
use App\Models\Traits\SocialMediaTrait;
use Livewire\Component;

class FacebookPost extends Component
{
    use SocialMediaTrait;

    public Document $document;
    public string $userId;
    private string $platform;

    public function render()
    {
        return view('livewire.social-media-post.platforms.facebook-post');
    }
}
