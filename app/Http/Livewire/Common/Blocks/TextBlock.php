<?php

namespace App\Http\Livewire\Common\Blocks;

use App\Models\Document;
use Livewire\Component;


class TextBlock extends Component
{
    private Document $document;
    public $content;

    public function render()
    {
        return view('livewire.common.blocks.text-block');
    }
}
