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
    public $outputBlocks = [];
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
            //    "echo-private:User.$userId,.ProcessFinished" => 'processFinished',
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
        if (!in_array(
            $this->document->status,
            [
                DocumentStatus::FINISHED,
                DocumentStatus::ON_HOLD,
                DocumentStatus::DRAFT
            ]
        ) && $this->isSaving === false) {
            $this->isSaving = true;
        };
        $this->tone = $document->meta['tone'] ?? null;
        $this->inputText = $document->content ?? '';
        $this->outputBlocks = $document->contentBlocks()->ofTextType()->get();
        ///$originalSentences = collect($this->document['meta']['original_sentences'] ?? []);
        //$paraphrasedSentences = collect($this->document['meta']['paraphrased_sentences'] ?? []);
        // $paraphrasedTextArray = $paraphrasedSentences->sortBy('sentence_order')->map(function ($sentence) {
        //     return $sentence['text'];
        // });

        // if ($originalSentences->count() && $paraphrasedTextArray->count()) {
        //     foreach ($paraphrasedTextArray as $key => $sentence) {
        //         $this->outputBlocks[] = [
        //             'original' => $originalSentences[$key]['text'],
        //             'paraphrased' => $sentence
        //         ];
        //     }
        // }
    }

    public function ready($params)
    {
        if ($params['document_id'] === $this->document->id) {
            $this->document->refresh();
            $this->setup($this->document);
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

    // public function paraphraseSentence()
    // {
    //     $this->isSaving = true;
    //     $this->copied = false;
    //     $this->processId = GenRepository::paraphraseText($this->document, [
    //         'text' => $this->selectedSentence['original'],
    //         'sentence_order' => $this->selectedSentenceIndex + 1,
    //         'tone' => $this->tone
    //     ]);
    // }

    public function paraphrase()
    {
        $this->validate();
        $this->isSaving = true;
        $repo = new DocumentRepository($this->document);
        $repo->updateMeta('tone', $this->tone);
        $repo->updateMeta('add_content_block', true);
        $this->document->update(['content' => $this->inputText]);

        // Break down inputText into sentences
        $originalSentencesArray = DocumentHelper::breakTextIntoSentences($this->inputText);
        $repo->updateMeta('sentences', $originalSentencesArray);

        GenRepository::paraphraseDocument($this->document->fresh());
    }

    public function saveDoc()
    {
        $this->document->update([
            'content' => implode('', array_column($this->outputText, 'paraphrased'))
        ]);
    }

    public function resetSentence()
    {
        // $this->outputText[$this->selectedSentenceIndex]['paraphrased'] = $this->selectedSentence['original'];
        // $repo = new DocumentRepository($this->document);
        // $repo->updateMeta('paraphrased_sentences', collect($this->outputText)->map(function ($sentence, $idx) {
        //     return ['sentence_order' => $idx + 1, 'text' => $sentence['paraphrased']];
        // })->toArray());
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
