<?php

namespace App\Http\Livewire\Summarizer;

use App\Enums\DocumentType;
use App\Enums\Language;
use App\Enums\SourceProvider;
use App\Exceptions\CreatingSummaryException;
use App\Jobs\Summarizer\PrepareCreationTasks;
use App\Repositories\DocumentRepository;
use App\Rules\CsvFile;
use App\Rules\DocxFile;
use App\Rules\PdfFile;
use Exception;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\File;
use Livewire\WithFileUploads;

class NewSummarizer extends Component
{
    use WithFileUploads;

    public $document;
    public $context;
    public $source;
    public $sourceUrl = null;
    public $sourceLanguage;
    public $targetLanguage;
    public $fileInput = null;
    public $filePath = null;
    public string $tempSourceUrl;
    public int $maxWordsCount = 100;
    public bool $isProcessing;

    public function rules()
    {
        return [
            'context' => [
                'required_if:source,free_text',
                'max:30000'
            ],
            'sourceUrl' => ['required_if:source,youtube,website_url', 'nullable', 'url', $this->source === 'youtube' ? new \App\Rules\YouTubeUrl() : ''],
            'source' => [
                'required',
                Rule::in(array_map(fn ($value) => $value->value, SourceProvider::cases()))
            ],
            'sourceLanguage' => 'required|in:en,pt,es,fr,de,it,ru,ja,ko,ch,pl,el,ar,tr',
            'targetLanguage' => 'required|in:en,pt,es,fr,de,it,ru,ja,ko,ch,pl,el,ar,tr',
            'maxWordsCount' => 'required|numeric|min:50|max:3000',
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
            'context.required_if' => __('validation.summarizer_context_required'),
            'sourceUrl.url' => __('validation.active_url'),
            'sourceUrl.required_if' => __('validation.summarizer_sourceurl_required'),
            'sourceLanguage.required' => __('validation.language_required'),
            'targetLanguage.required' => __('validation.language_required')
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
        $this->document = null;
        $this->source = SourceProvider::FREE_TEXT->value;
        $this->sourceLanguage = Language::ENGLISH->value;
        $this->targetLanguage = Language::ENGLISH->value;
    }

    public function storeFile()
    {
        $accountId = Auth::check() ? Auth::user()->account_id : 'guest';
        $filename = Str::uuid() . '.' . $this->fileInput->getClientOriginalExtension();
        $this->filePath = "documents/$accountId/" . $filename;
        $this->fileInput->storeAs("documents/$accountId", $filename, 's3');
    }

    public function process()
    {
        $this->validate();
        try {
            $this->isProcessing = true;
            if ($this->fileInput) {
                $this->storeFile();
            }
            $this->document = DocumentRepository::createGeneric([
                'type' => DocumentType::SUMMARIZER->value,
                'source' => $this->source,
                'language' => $this->sourceLanguage,
                'content' => $this->context,
                'meta' => [
                    'source_url' => $this->sourceUrl,
                    'source_file_path' => $this->filePath ?? null,
                    'max_words_count' => $this->maxWordsCount,
                    'target_language' => $this->sourceLanguage === $this->targetLanguage ? null : $this->targetLanguage
                ]
            ]);

            PrepareCreationTasks::dispatch($this->document, []);
        } catch (Exception $e) {
            throw new CreatingSummaryException($e->getMessage());
        }
    }

    public function onProcessFinished($params)
    {
        if ($params['document_id'] === $this->document->id) {
            $this->isProcessing = false;
            redirect()->route('summary-view', ['document' => $this->document]);
        }
    }

    public function render()
    {
        return view('livewire.summarizer.new');
    }
}
