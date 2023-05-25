<?php

namespace App\Http\Livewire\Blog;

use App\Repositories\DocumentRepository;
use Exception;
use Livewire\Component;
use Illuminate\Support\Str;

class MetaDescription extends Component
{
    public string $content;
    public bool $copied = false;
    protected $listeners = ['refreshContent' => 'updateContent'];

    public function mount(string $content)
    {
        $this->content = Str::of($content)->trim();
    }

    public function render()
    {
        return view('livewire.blog.meta-description');
    }


    public function copy()
    {
        $this->emit('addToClipboard', $this->content);
        $this->copied = true;
    }

    public function save()
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

    public function showHistoryModal()
    {
        $this->emit('showHistoryModal', 'meta_description');
    }

    public function updateContent($params)
    {
        if ($params['field'] === 'meta_description') {
            $this->content = Str::of($params['content'])->trim('"');
        }
    }
}
