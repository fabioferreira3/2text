<?php

namespace App\Http\Livewire\SocialMediaPost;

use App\Models\Document;
use App\Repositories\DocumentRepository;
use App\Repositories\GenRepository;
use Exception;
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
    public bool $displayHistory = false;

    protected $listeners = ['refresh', 'showHistoryModal', 'closeHistoryModal', 'refreshContent' => 'updateContent'];

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

    public function refresh()
    {
        $this->document->refresh();
        $this->setContent($this->document);
    }

    public function render()
    {
        return view('livewire.social-media-post.post');
    }

    public function regenerate()
    {
        GenRepository::generateSocialMediaPost($this->document, $this->platform);
        $this->refresh();
    }


    public function copy()
    {
        $this->emit('addToClipboard', $this->content);
        $this->copied = true;
    }

    public function showHistoryModal()
    {
        $this->displayHistory = true;
        $this->emit('listDocumentHistory', $this->platform, true);
    }

    public function closeHistoryModal()
    {
        $this->displayHistory = false;
    }

    public function save()
    {
        if ($this->content === $this->initialContent) {
            $this->dispatchBrowserEvent('alert', [
                'type' => 'info',
                'message' => "No changes needed to be saved"
            ]);
            return;
        }
        try {
            $repo = new DocumentRepository($this->document);
            $repo->updateMeta($this->platform, $this->content);
            $repo->addHistory(['field' => $this->platform, 'content' => $this->content]);
            $this->dispatchBrowserEvent('alert', [
                'type' => 'success',
                'message' => "$this->platform post updated!"
            ]);
            $this->initialContent = $this->content;
        } catch (Exception $error) {
            $this->dispatchBrowserEvent('alert', [
                'type' => 'error',
                'message' => "There was an error saving!"
            ]);
        }
    }

    public function updateContent($params)
    {
        if ($params['field'] === $this->platform) {
            $this->setContent($this->document);
        }
    }
}
