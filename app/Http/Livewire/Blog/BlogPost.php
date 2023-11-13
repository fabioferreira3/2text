<?php

namespace App\Http\Livewire\Blog;

use App\Models\Document;
use Livewire\Component;

class BlogPost extends Component
{
    public Document $document;
    public $title;
    public $showInfo = false;
    public $showMetaDescription = false;
    public $metaDescription = null;

    public function getListeners()
    {
        return [
            'blockDeleted'
        ];
    }

    public function mount(Document $document)
    {
        $this->document = $document;
        $this->metaDescription = $document->getContentBlockOfType('meta_description') ?? null;
        $this->title = $this->defineTitle();
    }

    public function blockDeleted()
    {
        $this->document->flushWordCount();
        $this->document->refresh();
    }

    public function defineTitle()
    {
        return $this->document->title ?? 'Blog post';
    }

    public function copyPost()
    {
        $content = $this->document->getHtmlContentBlocksAsText();
        $this->emit('addToClipboard', $content);
        $this->dispatchBrowserEvent('alert', [
            'type' => 'info',
            'message' => __('alerts.copied_to_clipboard')
        ]);
    }

    public function render()
    {
        return view('livewire.blog.blog-post')->layout('layouts.app', ['title' => $this->title]);
    }
}
