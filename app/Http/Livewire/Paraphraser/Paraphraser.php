<?php

namespace App\Http\Livewire\Paraphraser;

use Livewire\Component;
use Illuminate\Support\Str;

class Paraphraser extends Component
{
    public $inputText = '';
    public $sentences = [];
    public $outputSentences = [];

    public function updatedInputText()
    {
        $newSentences = preg_split('/(?<=[.!?])\s+/', $this->inputText);

        // Use hash of sentence as key
        $newSentencesWithHashes = [];
        foreach ($newSentences as $sentence) {
            $hash = md5($sentence);
            $newSentencesWithHashes[$hash] = $sentence;
        }

        $this->syncSentences($newSentencesWithHashes);
    }

    public function syncSentences($newSentences)
    {
        // Synchronize the $outputSentences array with $sentences.
        $this->outputSentences = array_intersect_key($this->outputSentences, $newSentences);
        $this->sentences = $newSentences;
    }

    public function paraphraseSentence($index)
    {
        $this->outputSentences[$index] = Str::random(5);
    }

    public function render()
    {
        return view('livewire.paraphraser.new');
    }
}
