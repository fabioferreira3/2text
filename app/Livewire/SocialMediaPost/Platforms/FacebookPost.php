<?php

namespace App\Livewire\SocialMediaPost\Platforms;

use App\Models\Document;
use App\Models\Traits\SocialMediaTrait;
use App\Repositories\DocumentRepository;
use Livewire\Component;

class FacebookPost extends Component
{
    use SocialMediaTrait;

    public Document $document;
    public string $userId;
    protected $repo;
    public string $platform;

    public function __construct()
    {
        $this->repo = app(DocumentRepository::class);
    }

    public function render()
    {
        return view('livewire.social-media-post.platforms.facebook-post');
    }
}
