<?php

namespace App\Http\Livewire\InquiryHub;

use App\Enums\DocumentType;
use App\Enums\Language;
use App\Enums\SourceProvider;
use App\Models\Document;
use App\Repositories\DocumentRepository;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class InquiryView extends Component
{
    public $document;
    public $source = SourceProvider::FREE_TEXT->value;
    public $sourceUrls = [];
    public $isProcessing;
    public $activeThread;

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
            "echo-private:User.$userId,.InquiryCompleted" => 'onProcessFinished',
        ];
    }

    public function mount(Document $document)
    {
        $this->document = $document;
        $this->isProcessing = false;
        $this->dispatchBrowserEvent('scrollInquiryChatToBottom');
    }

    public function createNewInquiry()
    {
        $document = DocumentRepository::createGeneric([
            'type' => DocumentType::INQUIRY->value,
            'language' => Language::ENGLISH->value
        ]);

        redirect()->route('inquiry-view', ['document' => $document]);
    }

    public function render()
    {
        return view('livewire.inquiry-hub.inquiry-view');
    }
}
