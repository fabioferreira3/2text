<?php

namespace App\Http\Livewire\Paraphraser;

use App\Enums\DocumentType;
use App\Enums\Language;
use App\Enums\SourceProvider;
use App\Jobs\Paraphraser\CreateFromWebsite;
use App\Repositories\DocumentRepository;
use Livewire\Component;
use Illuminate\Support\Str;

class NewParaphraser extends Component
{
    public $source = SourceProvider::FREE_TEXT->value;
    public $source_url = null;
    public $tone = null;
    public $displaySourceUrl = null;
    public $language = Language::ENGLISH->value;

    public function render()
    {
        return view('livewire.paraphraser.new');
    }

    public function start()
    {
        $repo = new DocumentRepository();
        $document = $repo->createGeneric([
            'type' => DocumentType::PARAPHRASED_TEXT->value,
            'source' => $this->source,
            'language' => $this->language,
            'meta' => [
                'tone' => $this->tone,
                'source_url' => $this->source_url,
            ]
        ]);

        if ($this->source === SourceProvider::FREE_TEXT->value) {
            redirect()->route('paraphrase-view', ['document' => $document]);
        } else {
            $processId = Str::uuid();
            CreateFromWebsite::dispatchIf($this->source === SourceProvider::WEBSITE_URL->value, $document, [
                'process_id' => $processId
            ]);
        }
    }

    public function setTone($tone)
    {
        $this->tone = $tone;
    }

    public function updated()
    {
        $this->displaySourceUrl = in_array($this->source, [
            SourceProvider::WEBSITE_URL->value,
            SourceProvider::YOUTUBE->value
        ]);
    }
}
