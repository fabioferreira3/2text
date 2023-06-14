<?php

namespace App\Http\Livewire\Paraphraser;

use App\Models\Document;
use App\Repositories\DocumentRepository;
use App\Repositories\GenRepository;
use Livewire\Component;

class Paraphraser extends Component
{
    public $document;
    public $inputText = '';
    public $inputTextArray = [];
    public $outputText = [];
    public $selectedSentence;
    public $selectedSentenceIndex;
    public $tone = null;
    public bool $copied = false;
    public bool $copiedAll = false;

    protected $listeners = ['select'];

    public function mount(Document $document)
    {
        $this->document = $document;
        $this->inputText = $document->meta['original_text'] ?? '';
        $sentences = $document->content ? $this->splitIntoSentences($document->content) : [];
        if (count($sentences) > 0) {
            $this->splitSentencesIntoArray($sentences);

            // Paraphrase each sentence
            foreach ($this->inputTextArray as $sentence) {
                $this->outputText[] = [
                    'original' => $sentence[0],
                    'paraphrased' => $sentence[0],
                    'punctuation' => $sentence[1]
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
        $this->saveDoc();
    }

    public function paraphraseAll()
    {
        $repo = new DocumentRepository($this->document);
        $this->unselect();

        // Break down inputText into sentences and punctuation
        $sentences = $this->splitIntoSentences($this->inputText);
        $this->inputTextArray = [];
        $this->outputText = [];
        $this->splitSentencesIntoArray($sentences);

        // Paraphrase each sentence
        foreach ($this->inputTextArray as $sentence) {
            $this->outputText[] = [
                'original' => $sentence[0],
                'paraphrased' => $this->paraphrase($sentence[0]),
                'punctuation' => $sentence[1]
            ];
        }
        $this->saveDoc();
        $repo->updateMeta('original_text', implode('', array_column($this->outputText, 'original')));
    }

    public function saveDoc()
    {
        $this->document->update(['content' => implode('', array_column($this->outputText, 'paraphrased'))]);
    }

    public function splitIntoSentences($text)
    {
        return preg_split('/(\\.|\?|!)/', $text, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
    }

    public function splitSentencesIntoArray(array $sentences)
    {
        for ($i = 0; $i < count($sentences); $i += 2) {
            $this->inputTextArray[] = [$sentences[$i], $sentences[$i + 1] ?? '.'];
        }
    }

    public function paraphrase($string)
    {
        return GenRepository::paraphraseText($this->document, $string, $this->tone);
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
