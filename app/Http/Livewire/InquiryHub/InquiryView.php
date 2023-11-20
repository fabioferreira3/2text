<?php

namespace App\Http\Livewire\InquiryHub;

use App\Models\Document;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class InquiryView extends Component
{
    public $document;
    public $source = null;
    public $sourceUrls = [];
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
            "echo-private:User.$userId,.InquiryCompleted" => 'onProcessFinished',
        ];
    }

    public function mount(Document $document)
    {
        $this->document = $document;
        $this->isProcessing = false;
    }

    public function render()
    {
        return view('livewire.inquiry-hub.inquiry-view');
    }
}
