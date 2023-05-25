<?php

namespace App\Http\Livewire\Blog;

use App\Repositories\DocumentRepository;
use Exception;
use Livewire\Component;
use Illuminate\Support\Str;

class Title extends Component
{
    public string $content;
    public bool $copied = false;
    protected $listeners = ['refreshContent' => 'updateContent'];

    public function mount(string $content)
    {
        $this->content = Str::of($content)->trim('"');
    }

    public function render()
    {
        return view('livewire.blog.title');
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

    public function showHistoryModal()
    {
        $this->emit('showHistoryModal', 'title');
    }

    public function updateContent($params)
    {
        if ($params['field'] === 'title') {
            $this->content = Str::of($params['content'])->trim('"');
        }
    }
}
