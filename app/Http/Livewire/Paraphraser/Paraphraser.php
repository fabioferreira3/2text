<?php

namespace App\Http\Livewire\Paraphraser;

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

    public function getListeners()
    {
        $userId = Auth::user()->id;
        return [
            "echo-private:User.$userId,.TextParaphrased" => 'notify',
            'select',
            //    'refreshComponent' => '$refresh'
        ];
    }

    public function notify()
    {
        $this->document->refresh();
        $this->setup($this->document);
    }

    public function mount(Document $document)
    {
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
        $paraphrasedTextArray = $paraphrasedSentences->map(function ($sentence) {
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

    protected $rules = [
        'inputText' => 'required|string',
        'language' => 'required'
    ];

    protected $validationAttributes = [
        'inputText' => 'original text',
    ];

    public function copy()
    {
        $this->emit('addToClipboard', $this->selectedSentence['paraphrased']);
        $this->copied = true;
    }

    public function copyAll()
    {
        $outputAsText = '';
        foreach ($this->outputText as $sentence) {
            $outputAsText .= $sentence['paraphrased'] . $sentence['punctuation'] . ' ';
        }

        $this->emit('addToClipboard', trim($outputAsText));
        $this->copiedAll = true;
    }

    public function paraphraseSentence()
    {
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
        $repo = new DocumentRepository($this->document);
        $this->unselect();

        // Break down inputText into sentences and punctuation
        $sentences = $this->splitIntoSentences($this->inputText);
        $sentencesArray = $this->splitSentencesIntoArray($sentences);
        $repo->updateMeta('tone', $this->tone);
        $repo->updateMeta('original_text', $this->inputText);
        $originalSentencesArray = collect($sentencesArray)->map(function ($sentenceStructure, $idx) {
            return ['sentence_order' => $idx + 1, 'text' => $sentenceStructure[0] . $sentenceStructure[1]];
        });
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

    public function splitIntoSentences($text)
    {
        return preg_split('/(\\.|\?|!)/', $text, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
    }

    public function splitSentencesIntoArray(array $sentences)
    {
        $array = [];
        for ($i = 0; $i < count($sentences); $i += 2) {
            $array[] = [$sentences[$i], $sentences[$i + 1] ?? '.'];
        }
        return $array;
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
