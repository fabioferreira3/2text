<?php

namespace App\Http\Livewire\Common;

use Livewire\Component;


class CreateDocument extends Component
{

    public function __construct()
    {
    }

    public function templates()
    {
        return redirect()->to('/templates');
    }

    public function render()
    {
        return view('livewire.common.create-document-button');
    }
}
