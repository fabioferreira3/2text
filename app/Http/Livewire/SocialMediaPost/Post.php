<?php

namespace App\Http\Livewire\SocialMediaPost;

use App\Models\Document;
use App\Repositories\GenRepository;
use Livewire\Component;
use Illuminate\Support\Str;

class Post extends Component
{
    public Document $document;
    public string $content;
    public string $initialContent;
    public bool $copied = false;
    public string $platform;
    public int $rows;
    protected $listeners = ['refreshContent' => 'updateContent'];

    public function mount(Document $document, $platform, $rows = 12)
    {
        $this->document = $document;
        $this->platform = $platform;
        $this->rows = $rows;
        $this->setContent($document);
        $this->initialContent = $this->content;
    }

    private function setContent(Document $document)
    {
        $this->content = Str::of($document->meta[$this->platform])->trim('"');
    }

    public function render()
    {
        return view('livewire.social-media-post.post');
    }

    public function regenerate()
    {
        // GenRepository::generateMetaDescription($this->document, [
        //     'tone' => $this->document->meta['tone'],
        //     'keyword' => $this->document->meta['keyword']
        // ]);
        $this->setContent($this->document->refresh());
    }


    public function copy()
    {
        $this->emit('addToClipboard', $this->content);
        $this->copied = true;
    }

    public function save()
    {
        if ($this->content !== $this->initialContent) {
            $this->emitUp('saveField', ['field' => 'meta_description', 'content' => $this->content]);
            $this->initialContent = $this->content;
        }
    }

    public function showHistoryModal()
    {
        $this->emit('showHistoryModal', $this->platform);
    }

    public function updateContent($params)
    {
        if ($params['field'] === $this->platform) {
            $this->setContent($this->document);
        }
    }
}
