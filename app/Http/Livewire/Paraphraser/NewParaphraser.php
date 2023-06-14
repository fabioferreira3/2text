<?php

namespace App\Http\Livewire\Paraphraser;

use App\Enums\DocumentType;
use App\Enums\SourceProvider;
use App\Repositories\DocumentRepository;
use Livewire\Component;

class NewParaphraser extends Component
{
    public $source = SourceProvider::FREE_TEXT->value;

    public function render()
    {
        return view('livewire.paraphraser.new');
    }

    public function start()
    {
        $repo = new DocumentRepository();
        $document = $repo->createGeneric([
            'type' => DocumentType::PARAPHRASED_TEXT->value,
            'source' => $this->source
        ]);
        redirect()->route('paraphrase-view', ['document' => $document]);
    }
}
