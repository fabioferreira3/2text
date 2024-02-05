<?php

namespace App\Livewire\InsightHub;

use App\Enums\DocumentStatus;
use App\Enums\Language;
use App\Enums\SourceProvider;
use App\Jobs\InsightHub\PrepareTasks;
use App\Models\Document;
use App\Models\Traits\InsightHub;
use App\Rules\CsvFile;
use App\Rules\DocxFile;
use App\Rules\PdfFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithFileUploads;

class InsightView extends Component
{
    use InsightHub, WithFileUploads;

    public $document;
    public $context;
    public $source = null;
    public $sourceUrl = null;
    public $sourceType;
    public $videoLanguage = null;
    public $fileInput = null;
    public $filePath = null;
    public $tempSourceUrl;
    public $isProcessing;
    public $hasEmbeddings;

    public function rules()
    {
        return [
            'document' => [
                'required'
            ],
            'context' => [
                'required_if:sourceType,free_text',
                'max:30000'
            ],
            'sourceUrl' => [
                Rule::requiredIf(function () {
                    return in_array($this->sourceType, ['youtube', 'website_url']);
                }),
                'nullable',
                'url',
                $this->sourceType === 'youtube' ? new \App\Rules\YouTubeUrl() : null,
            ],
            'sourceType' => [
                'required',
                Rule::in(array_map(fn ($value) => $value->value, SourceProvider::cases()))
            ],
            'fileInput' => [
                'required_if:sourceType,docx,pdf_file,csv',
                'max:51200', // in kilobytes, 50mb = 50 * 1024 = 51200kb
                new DocxFile($this->sourceType),
                new PdfFile($this->sourceType),
                new CsvFile($this->sourceType),
            ],
            'videoLanguage' => [
                'required_if:sourceType,youtube',
                Rule::in(Language::getValues())
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
            'fileInput.required_if' => __('validation.fileInput_required_if'),
            'videoLanguage.required_if' => __('validation.videoLanguage_required_if'),
            'videoLanguage.in' => __('validation.language_in'),
        ];
    }

    public function getListeners()
    {
        $userId = Auth::user()->id;
        return [
            "echo-private:User.$userId,.EmbedCompleted" => 'onEmbeddingFinished'
        ];
    }

    public function mount(Document $document)
    {
        $this->document = $document;
        $this->hasEmbeddings = $document->getMeta('has_embeddings') ?? false;
        $this->sourceType = SourceProvider::FREE_TEXT->value;
        $this->videoLanguage = Language::ENGLISH->value;
        $this->isProcessing = $this->document->status === DocumentStatus::IN_PROGRESS;
    }

    public function storeFile()
    {
        $accountId = Auth::check() ? Auth::user()->account_id : 'guest';
        $filename = Str::uuid() . '.' . $this->fileInput->getClientOriginalExtension();
        $this->filePath = "documents/$accountId/" . $filename;
        $this->fileInput->storeAs("documents/$accountId", $filename, 's3');
    }

    public function updatedFileInput($file)
    {
        $this->storeFile($file);
    }

    public function embed()
    {
        $this->validate();
        $this->isProcessing = true;

        if ($this->fileInput) {
            $this->document->updateMeta('source_file_path', $this->filePath);
        }

        PrepareTasks::dispatch($this->document, [
            'source' => $this->parsedContext(),
            'source_type' => $this->sourceType,
            'source_url' => $this->sourceUrl,
            'video_language' => $this->videoLanguage,
        ]);
    }

    public function parsedContext()
    {
        $encodedText = htmlspecialchars($this->context, ENT_QUOTES, 'UTF-8');
        return nl2br(trim($encodedText));
    }

    public function onEmbeddingFinished($params)
    {
        if ($params['document_id'] !== $this->document->id) {
            return;
        }

        if (!$this->hasEmbeddings) {
            $this->document->updateMeta('has_embeddings', true);
            $this->hasEmbeddings = true;
        }

        $this->isProcessing = false;
        $this->context = null;
        $this->sourceUrl = null;
        $this->sourceType = SourceProvider::FREE_TEXT->value;
        $this->fileInput = null;
        $this->videoLanguage = Language::ENGLISH->value;

        $this->dispatch(
            'alert',
            type: 'success',
            message: __('insight-hub.embed_success')
        );
    }

    public function render()
    {
        return view('livewire.insight-hub.insight-view');
    }
}
