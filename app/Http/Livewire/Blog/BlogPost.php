<?php

namespace App\Http\Livewire\Blog;

use App\Models\Document;
use Livewire\Component;

class BlogPost extends Component
{
    public Document $document;
    public $title;

    public function mount(Document $document)
    {
        $this->document = $document;
        $this->title = $document->title ?? 'Blog post';
    }

    public function copyPost()
    {
        $content = $this->document->getHtmlContentBlocksAsText();
        $this->emit('addToClipboard', $content);
    }

    public function render()
    {
        return view('livewire.blog.blog-post')->layout('layouts.app', ['title' => $this->title]);
    }
}
