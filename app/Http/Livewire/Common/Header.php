<?php

namespace App\Http\Livewire\Common;

use Livewire\Component;


class Header extends Component
{
    public $document = null;
    public $editable = false;
    public $icon;
    public $title = '';
    public $suffix = null;

    public function render()
    {
        return view('livewire.common.header');
    }

    public function updatedTitle($newTitle)
    {
        if ($this->document) {
            $this->document->update(['title' => $newTitle]);
        }
    }
}
