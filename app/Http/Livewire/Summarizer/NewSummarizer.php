<?php

namespace App\Http\Livewire\Summarizer;

use App\Enums\DocumentType;
use App\Enums\Language;
use App\Enums\SourceProvider;
use App\Repositories\DocumentRepository;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Illuminate\Support\Str;

class NewSummarizer extends Component
{
    public $document;
    public $source = SourceProvider::FREE_TEXT->value;
    public $source_url = null;
    public $targetLanguage = Language::ENGLISH->value;
    public $isProcessing;

    public function rules()
    {
        return [
            'source_url' => 'nullable|url|required_if:source,website'
        ];
    }

    public function getListeners()
    {
        $userId = Auth::user()->id;
        return [
            "echo-private:User.$userId,.SummaryCompleted" => 'onProcessFinished',
        ];
    }

    public function mount()
    {
        $this->isProcessing = false;
    }

    public function render()
    {
        return view('livewire.summarizer.new');
    }

    public function start()
    {
        $document = DocumentRepository::createGeneric([
            'type' => DocumentType::SUMMARIZER->value,
            'source' => $this->source,
            'language' => $this->targetLanguage,
            'meta' => [
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
            // CreateFromWebsite::dispatchIf($this->source === SourceProvider::WEBSITE_URL->value, $document, [
            //     'process_id' => $processId
            // ]);
        }
    }

    public function redirectToDocument()
    {
        //    redirect()->route('paraphrase-view', ['document' => $this->document]);
    }
}
