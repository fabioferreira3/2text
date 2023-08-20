<?php

namespace App\Http\Livewire\Paraphraser;

use App\Enums\DocumentStatus;
use App\Helpers\DocumentHelper;
use App\Models\Document;
use App\Repositories\DocumentRepository;
use App\Repositories\GenRepository;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Paraphraser extends Component
{
    public $document;
    protected $repo;
    public $inputText = '';
    public $outputText = [];
    public $selectedSentence;
    public $selectedSentenceIndex;
    public $tone = null;
    public bool $copied = false;
    public bool $copiedAll = false;
    public $isSaving = false;
    public string $processId = '';

    protected $rules = [
        'inputText' => 'required|string'
    ];

    protected $validationAttributes = [
        'inputText' => 'original text',
    ];

    public function getListeners()
    {
        $userId = Auth::user()->id;
        return [
            "echo-private:User.$userId,.TextParaphrased" => 'ready',
            "echo-private:User.$userId,.ProcessFinished" => 'processFinished',
            'select',
        ];
    }

    public function mount(Document $document)
    {
        $this->processId;
        $this->setup($document);
    }

    public function setup($document)
    {
        $this->document = $document;
        if ($this->document->status !== DocumentStatus::FINISHED && $this->isSaving === false) {
            $this->isSaving = true;
        };
        $this->tone = $document->meta['tone'] ?? null;
        $this->inputText = $document->meta['original_text'] ?? '';
        $this->outputText = [];
        $originalSentences = collect($this->document['meta']['original_sentences'] ?? []);
        $paraphrasedSentences = collect($this->document['meta']['paraphrased_sentences'] ?? []);
        $paraphrasedTextArray = $paraphrasedSentences->sortBy('sentence_order')->map(function ($sentence) {
            return $sentence['text'];
        });

        if ($originalSentences->count() && $paraphrasedTextArray->count()) {
            foreach ($paraphrasedTextArray as $key => $sentence) {
                $this->outputText[] = [
                    'original' => $originalSentences[$key]['text'],
                    'paraphrased' => $sentence
                ];
            }
        }
    }

    public function ready()
    {
        $this->document->refresh();
        $this->setup($this->document);
    }

    public function processFinished(array $params)
    {
        $this->isSaving = !($params['process_id'] === $this->processId);
        $this->processId = '';
        $this->ready();
    }

    public function copy()
    {
        $this->emit('addToClipboard', $this->selectedSentence['paraphrased']);
        $this->copied = true;
    }

    public function copyAll()
    {
        $outputAsText = '';
        foreach ($this->outputText as $sentence) {
            $outputAsText .= $sentence['paraphrased'] . ' ';
        }

        $this->emit('addToClipboard', trim($outputAsText));
        $this->copiedAll = true;
    }

    public function paraphraseSentence()
    {
        $this->isSaving = true;
        $this->copied = false;
        $this->processId = GenRepository::paraphraseText($this->document, [
            'text' => $this->selectedSentence['original'],
            'sentence_order' => $this->selectedSentenceIndex + 1,
            'tone' => $this->tone
        ]);
    }

    public function paraphraseAll()
    {
        $this->validate();
        $this->isSaving = true;
        $repo = new DocumentRepository($this->document);
        $this->unselect();
        $repo->updateMeta('tone', $this->tone);
        $repo->updateMeta('original_text', $this->inputText);

        // Break down inputText into sentences
        $originalSentencesArray = DocumentHelper::breakTextIntoSentences($this->inputText);
        $repo->updateMeta('original_sentences', $originalSentencesArray);

        $this->processId = GenRepository::paraphraseDocument($this->document->fresh());
    }

    public function saveDoc()
    {
        $this->document->update([
            'content' => implode('', array_column($this->outputText, 'paraphrased'))
        ]);
    }

    public function resetSentence()
    {
        $this->outputText[$this->selectedSentenceIndex]['paraphrased'] = $this->selectedSentence['original'];
        $repo = new DocumentRepository($this->document);
        $repo->updateMeta('paraphrased_sentences', collect($this->outputText)->map(function ($sentence, $idx) {
            return ['sentence_order' => $idx + 1, 'text' => $sentence['paraphrased']];
        })->toArray());
    }

    public function select($index)
    {
        $this->selectedSentence = $this->outputText[$index];
        $this->selectedSentenceIndex = (int) $index;
    }

    public function unselect()
    {
        $this->selectedSentence = null;
        $this->selectedSentenceIndex = null;
    }

    public function setTone($tone)
    {
        $this->tone = $tone;
    }

    public function render()
    {
        return view('livewire.paraphraser.paraphrase');
    }
}
