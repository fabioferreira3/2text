<?php

namespace App\Http\Livewire\SocialMediaPost;

use App\Models\Document;
use App\Repositories\DocumentRepository;
use Livewire\Component;

class PostsList extends Component
{
    public Document $document;
    public string $content;
    public bool $displayHistory = false;

    public function mount(Document $document)
    {
        $this->document = $document;
    }

    public function render()
    {
        return view('livewire.social-media-post.posts-list');
    }
}
