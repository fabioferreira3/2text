<?php

namespace App\Http\Livewire\InquiryHub;

use App\Enums\DataType;
use App\Enums\DocumentTaskEnum;
use App\Enums\SourceProvider;
use App\Jobs\InquiryHub\PrepareTasks;
use App\Models\Document;
use App\Models\Traits\ChatTrait;
use App\Models\Traits\InquiryHub;
use App\Repositories\DocumentRepository;
use App\Rules\CsvFile;
use App\Rules\DocxFile;
use App\Rules\PdfFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Livewire\Component;

class InquiryView extends Component
{
    use ChatTrait, InquiryHub;

    public $document;
    public $chatThread;
    public $context;
    public $source = null;
    public $sourceUrl = null;
    public $sourceType = SourceProvider::FREE_TEXT->value;
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
            'sourceType' => [
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
            "echo-private:User.$userId,.EmbedCompleted" => 'onEmbeddingFinished',
            "echo-private:User.$userId,.ChatMessageReceived" => 'receiveMsg',
        ];
    }

    public function mount(Document $document)
    {
        $this->document = $document;
        $this->chatThread = $document->chatThread;
        $this->isProcessing = false;
        $this->dispatchBrowserEvent('scrollInquiryChatToBottom');
    }

    public function embed()
    {
        $this->validate();
        PrepareTasks::dispatch($this->document, [
            'source' => $this->context,
            'source_type' => $this->sourceType
        ]);
    }

    public function onEmbeddingFinished($params)
    {
        dd($params);
    }

    public function render()
    {
        return view('livewire.inquiry-hub.inquiry-view');
    }
}
