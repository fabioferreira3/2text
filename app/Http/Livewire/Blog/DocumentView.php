<?php

namespace App\Http\Livewire\Blog;

use App\Models\Document;
use App\Repositories\DocumentRepository;
use Exception;
use Livewire\Component;
use Illuminate\Support\Str;

class DocumentView extends Component
{
    public Document $document;
    public string $title;
    public string $meta_description;
    public string $copied = '';
    public bool $blurModal = true;

    protected $listeners = ['contentUpdated'];

    public function mount(Document $document)
    {
        $this->fill([
            'document' => $document,
            'title' => Str::of($document->meta['title'])->trim('"'),
            'meta_description' => Str::of($document->meta['meta_description'])->trim()
        ]);
    }

    public function render()
    {
        return view('livewire.blog.document-view');
    }

    public function regenerateTitle()
    {
        dd('regenerate');
    }

    public function saveTitle()
    {
        try {
            $repo = new DocumentRepository($this->document);
            $repo->updateMeta('title', $this->title);
            $repo->addHistory(['field' => 'title', 'content' => $this->title]);
            $this->dispatchBrowserEvent('alert', [
                'type' => 'success',
                'message' => "Title updated!"
            ]);
        } catch (Exception $error) {
            $this->dispatchBrowserEvent('alert', [
                'type' => 'error',
                'message' => "There was an error saving the title!"
            ]);
        }
    }

    public function copyTitle()
    {
        $this->emit('addToClipboard', $this->title);
        $this->copied = 'title';
    }

    public function copyMetaDescription()
    {
        $this->emit('addToClipboard', $this->meta_description);
        $this->copied = 'meta_description';
    }

    public function saveMetaDescription()
    {
        try {
            $repo = new DocumentRepository($this->document);
            $repo->updateMeta('meta_description', $this->meta_description);
            $repo->addHistory(['field' => 'meta_description', 'content' => $this->meta_description]);
            $this->dispatchBrowserEvent('alert', [
                'type' => 'success',
                'message' => "Meta description updated!"
            ]);
        } catch (Exception $error) {
            $this->dispatchBrowserEvent('alert', [
                'type' => 'error',
                'message' => "There was an error saving the meta description!"
            ]);
        }
    }
}
