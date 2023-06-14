<?php

namespace App\Http\Livewire\Paraphraser;

use App\Models\Document;
use Livewire\Component;
use Illuminate\Support\Str;

class Paraphraser extends Component
{
    public $document;
    public $inputText = '';
    public $inputTextArray = [];
    public $outputText = [];
    public $selectedSentence;
    public $selectedSentenceIndex;
    public $tone = 'default';
    public bool $copied = false;
    public bool $copiedAll = false;

    protected $listeners = ['select'];

    public function mount(Document $document)
    {
        $this->document = $document;
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
            $outputAsText .= $sentence['paraphrased'] . $sentence['punctuation'] . ' ';
        }

        $this->emit('addToClipboard', trim($outputAsText));
        $this->copiedAll = true;
    }

    public function paraphraseSentence()
    {
        $paraphrasedSentence = $this->paraphrase($this->selectedSentence['original']);
        $this->outputText[$this->selectedSentenceIndex]['paraphrased'] = $paraphrasedSentence;
        $this->copied = false;
    }

    public function paraphraseAll()
    {
        $this->unselect();
        // Break down inputText into sentences and punctuation
        $sentences = preg_split('/(\\.|\?|!)/', $this->inputText, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
        $this->inputTextArray = [];
        $this->outputText = [];
        for ($i = 0; $i < count($sentences); $i += 2) {
            $this->inputTextArray[] = [$sentences[$i], $sentences[$i + 1] ?? '.'];
        }

        // Paraphrase each sentence
        foreach ($this->inputTextArray as $sentence) {
            $this->outputText[] = [
                'original' => $sentence[0],
                'paraphrased' => $this->paraphrase($sentence[0]),
                'punctuation' => $sentence[1]
            ];
        }
    }

    public function paraphrase($string)
    {
        return '*' . Str::random(3) . $string . '*';
    }

    public function resetSentence()
    {
        $this->outputText[$this->selectedSentenceIndex]['paraphrased'] = $this->selectedSentence['original'];
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
