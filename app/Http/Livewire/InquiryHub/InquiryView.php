<?php

namespace App\Http\Livewire\InquiryHub;

use App\Enums\DocumentType;
use App\Enums\Language;
use App\Enums\SourceProvider;
use App\Models\Document;
use App\Repositories\DocumentRepository;
use App\Rules\CsvFile;
use App\Rules\DocxFile;
use App\Rules\PdfFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Livewire\Component;

class InquiryView extends Component
{
    public $document;
    public $context;
    public $source = SourceProvider::FREE_TEXT->value;
    public $sourceUrl = null;
    public $fileInput = null;
    public $filePath = null;
    public $tempSourceUrl;
    public $isProcessing;
    public $activeThread;

    public function rules()
    {
        return [
            'context' => [
                'required_if:source,free_text',
                'max:30000'
            ],
            'sourceUrl' => [
                'required_if:source,youtube,website_url', 'nullable', 'url',
                $this->source === 'youtube' ? new \App\Rules\YouTubeUrl() : ''
            ],
            'source' => [
                'required',
                Rule::in(array_map(fn ($value) => $value->value, SourceProvider::cases()))
            ],
            'fileInput' => [
                'required_if:source,docx,pdf_file,csv',
                'max:51200', // in kilobytes, 50mb = 50 * 1024 = 51200kb
                new DocxFile($this->source),
                new PdfFile($this->source),
                new CsvFile($this->source),
            ]
        ];
    }

    public function messages()
    {
        return [
            'context.required_if' => __('validation.inquiry_context_required'),
            'sourceUrl.url' => __('validation.active_url'),
            'sourceUrl.required_if' => __('validation.inquiry_sourceurl_required'),
            'sourceLanguage.required' => __('validation.language_required'),
            'targetLanguage.required' => __('validation.language_required'),
            'fileInput.required_if' => __('validation.fileInput_required_if')
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

    public function embed()
    {
        $this->validate();
    }

    public function render()
    {
        return view('livewire.inquiry-hub.inquiry-view');
    }
}
