<?php

namespace App\Livewire\SocialMediaPost\Platforms;

use App\Models\Document;
use App\Models\Traits\SocialMediaTrait;
use App\Repositories\DocumentRepository;
use Livewire\Component;

class InstagramPost extends Component
{
    use SocialMediaTrait;

    public Document $document;
    public string $userId;
    public string $platform;
    protected $repo;

    public function __construct()
    {
        $this->repo = app(DocumentRepository::class);
    }

    public function render()
    {
        return view('livewire.social-media-post.platforms.instagram-post');
    }
}
