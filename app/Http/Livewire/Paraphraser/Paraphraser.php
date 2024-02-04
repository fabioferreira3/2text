<?php

namespace App\Http\Livewire\Paraphraser;

use App\Enums\DocumentStatus;
use App\Exceptions\InsufficientUnitsException;
use App\Helpers\DocumentHelper;
use App\Models\Document;
use App\Repositories\DocumentRepository;
use App\Repositories\GenRepository;
use App\Traits\UnitCheck;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Livewire\Component;

class Paraphraser extends Component
{
    use UnitCheck;

    public $document;
    protected $repo;
    public $inputText = '';
    public $outputBlocks = [];
    public $tone = 'default';
    public bool $copied = false;
    public $isSaving = false;
    public string $processId = '';

    protected $rules = [
        'inputText' => 'required|string',
    ];

    public function getListeners()
    {
        $userId = Auth::user()->id;
        return [
            "echo-private:User.$userId,.TextParaphrased" => 'ready',
            'blockDeleted'
        ];
    }

    public function mount(Document $document)
    {
        $this->processId;
        $this->document = $document;
        $this->setup();
    }

    public function setup()
    {
        if ($this->document->status === DocumentStatus::FINISHED) {
            if ($this->isSaving === true) {
                $this->dispatchBrowserEvent('alert', [
                    'type' => 'success',
                    'message' => __('alerts.paraphrase_completed')
                ]);
            }

            $this->isSaving = false;
        } elseif (in_array($this->document->status, [DocumentStatus::IN_PROGRESS, DocumentStatus::ON_HOLD])) {
            $this->isSaving = true;
        };
        $this->tone = $this->document->getMeta('tone') ?? 'default';
        $this->inputText = $this->document->content ?? '';
        $this->outputBlocks = $this->document->contentBlocks()->ofTextType()->get();
        $this->dispatchBrowserEvent('adjustTextArea');
    }

    public function ready($params)
    {
        if ($params['document_id'] === $this->document->id) {
            $this->document->refresh();
            $this->setup($this->document);
        }
    }

    public function blockDeleted()
    {
        $this->setup();
    }

    public function copy()
    {
        $content = $this->document->getTextContentBlocksAsText();
        $this->emit('addToClipboard', $content);
        $this->copied = true;
    }

    public function validateUnitCosts()
    {
        $this->totalCost = 0;
        $this->estimateWordsGenerationCost(Str::wordCount($this->inputText));
        $this->authorizeTotalCost();
    }

    public function paraphrase()
    {
        $this->validate();
        try {
            $this->validateUnitCosts();
            if ($this->isSaving) {
                return;
            }
            DocumentRepository::clearContentBlocks($this->document);
            $this->outputBlocks = [];
            $this->isSaving = true;
            $repo = new DocumentRepository($this->document);
            $repo->updateMeta('tone', $this->tone);
            $repo->updateMeta('add_content_block', true);
            $this->document->update(['content' => $this->inputText]);

            $originalSentencesArray = DocumentHelper::breakTextIntoSentences($this->inputText);
            $repo->updateMeta('sentences', $originalSentencesArray);

            GenRepository::paraphraseDocument($this->document->fresh());
        } catch (InsufficientUnitsException $e) {
            $this->dispatchBrowserEvent('alert', [
                'type' => 'error',
                'message' => __('alerts.insufficient_units')
            ]);
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    public function setTone($tone)
    {
        $this->tone = $tone;
        $repo = new DocumentRepository($this->document);
        $repo->updateMeta('tone', $this->tone);
        $this->emit('setTone', $tone);
    }

    public function render()
    {
        return view('livewire.paraphraser.paraphrase');
    }
}
