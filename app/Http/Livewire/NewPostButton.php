<?php

namespace App\Http\Livewire;

use Livewire\Component;


class NewPostButton extends Component
{
    public function newPost()
    {
        return redirect()->to('/new');
    }

    public function render()
    {
        return view('livewire.common.new-post-button');
    }
}
