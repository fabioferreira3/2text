<?php

namespace App\Livewire\Paraphraser;

use App\Domain\Agents\Enums\Agent;
use App\Domain\Agents\Factories\AgentFactory;
use App\Domain\Agents\Repositories\AgentRepository;
use App\Enums\DocumentStatus;
use App\Exceptions\InsufficientUnitsException;
use App\Helpers\DocumentHelper;
use App\Models\Document;
use App\Models\DocumentThread;
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
            "echo-private:User.$userId,.ParaphraserCheckout" => 'ready',
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
                $this->dispatch(
                    'alert',
                    type: 'success',
                    message: __('alerts.paraphrase_completed')
                );
            }

            $this->isSaving = false;
        } elseif (in_array($this->document->status, [DocumentStatus::IN_PROGRESS, DocumentStatus::ON_HOLD])) {
            $this->isSaving = true;
        };
        $this->inputText = $this->document->content ?? '';
        $this->outputBlocks = $this->document->contentBlocks()->ofTextType()->get();
        $this->dispatch('adjustTextArea');
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
        $this->dispatch('addToClipboard', message: $content);
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
            $this->isSaving = true;

            $agentFactory = new AgentFactory();
            $agent = $agentFactory->make(Agent::THE_PARAPHRASER);
            $agentRepo = new AgentRepository();

            $originalSentencesArray = DocumentHelper::breakTextIntoSentences($this->inputText);
            DocumentRepository::clearContentBlocks($this->document);
            $this->outputBlocks = [];
            $this->document->update(['content' => $this->inputText]);
            $this->document->updateMeta('sentences', $originalSentencesArray);

            foreach ($originalSentencesArray as $item) {
                dispatch(function () use ($agentRepo, $agent, $item) {
                    $thread = $agentRepo->createThread($item['text']);
                    DocumentThread::create([
                        'document_id' => $this->document->id,
                        'thread_id' => $thread->id,
                    ]);
                    $agent->run($thread, [
                        'agent' => Agent::THE_PARAPHRASER->value,
                        'document_id' => $this->document->id,
                        'sentence_order' => $item['sentence_order']
                    ]);
                });
            }

            // $genRepo = new GenRepository();
            // $genRepo->registerParaphraseDocumentTasks($this->document->fresh());
        } catch (InsufficientUnitsException $e) {
            $this->dispatch(
                'alert',
                type: 'error',
                message: __('alerts.insufficient_units')
            );
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    // public function setTone($tone)
    // {
    //     $this->tone = $tone;
    //     $repo = new DocumentRepository($this->document);
    //     $repo->updateMeta('tone', $this->tone);
    //     $this->dispatch('setTone', tone: $tone);
    // }

    public function render()
    {
        return view('livewire.paraphraser.paraphrase');
    }
}
