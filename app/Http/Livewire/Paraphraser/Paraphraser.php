<?php

namespace App\Http\Livewire\Paraphraser;

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
    public $language = null;
    public bool $copied = false;
    public bool $copiedAll = false;
    public $isSaving;

    protected $rules = [
        'inputText' => 'required|string',
        'language' => 'required'
    ];

    protected $validationAttributes = [
        'inputText' => 'original text',
    ];

    public function getListeners()
    {
        $userId = Auth::user()->id;
        return [
            "echo-private:User.$userId,.TextParaphrased" => 'notify',
            'select',
        ];
    }

    public function notify()
    {
        $this->document->refresh();
        $this->setup($this->document);
    }

    public function mount(Document $document)
    {
        $this->isSaving = false;
        $this->setup($document);
    }

    public function setup($document)
    {
        $this->document = $document;
        $this->language = $document->language ?? 'en';
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
        GenRepository::paraphraseText($this->document, [
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

        GenRepository::paraphraseDocument($this->document);
    }

    public function saveDoc()
    {
        $this->document->update([
            'content' => implode('', array_column($this->outputText, 'paraphrased')),
            'language' => $this->language
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
