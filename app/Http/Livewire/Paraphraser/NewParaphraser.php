<?php

namespace App\Http\Livewire\Paraphraser;

use App\Enums\DocumentType;
use App\Enums\Language;
use App\Enums\SourceProvider;
use App\Jobs\Paraphraser\CreateFromWebsite;
use App\Repositories\DocumentRepository;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Illuminate\Support\Str;

class NewParaphraser extends Component
{
    public $document;
    public $source = SourceProvider::FREE_TEXT->value;
    public $source_url = null;
    public $tone = null;
    public $displaySourceUrl = null;
    public $language = Language::ENGLISH->value;
    public $isProcessing;

    protected $rules = [
        'source_url' => 'nullable|url|required_if:source,website',
    ];

    public function getListeners()
    {
        $userId = Auth::user()->id;
        return [
            "echo-private:User.$userId,.WebsiteCrawled" => 'ready',
        ];
    }

    public function mount()
    {
        $this->isProcessing = false;
    }

    public function render()
    {
        return view('livewire.paraphraser.new');
    }

    public function ready()
    {
        $this->redirectToDocument();
    }

    public function start()
    {
        $document = DocumentRepository::createGeneric([
            'type' => DocumentType::PARAPHRASED_TEXT->value,
            'source' => $this->source,
            'language' => $this->language,
            'meta' => [
                'tone' => $this->tone,
                'source_url' => $this->source_url
            ]
        ]);
        $this->document = $document;

        if ($this->source === SourceProvider::FREE_TEXT->value) {
            $this->redirectToDocument();
        } else {
            $this->validate();
            $this->dispatchBrowserEvent('alert', [
                'type' => 'info',
                'message' => __('alerts.working_request')
            ]);
            $this->isProcessing = true;
            $processId = Str::uuid();
            CreateFromWebsite::dispatchIf($this->source === SourceProvider::WEBSITE_URL->value, $document, [
                'process_id' => $processId
            ]);
        }
    }

    public function redirectToDocument()
    {
        redirect()->route('paraphrase-view', ['document' => $this->document]);
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
