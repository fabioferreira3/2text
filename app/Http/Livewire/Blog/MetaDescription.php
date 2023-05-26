<?php

namespace App\Http\Livewire\Blog;

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
        $this->emitUp('saveField', ['field' => 'meta_description', 'content' => $this->content]);
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
