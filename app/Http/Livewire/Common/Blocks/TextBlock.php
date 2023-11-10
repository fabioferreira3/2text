<?php

namespace App\Http\Livewire\Common\Blocks;

use App\Enums\DocumentTaskEnum;
use App\Helpers\PromptHelper;
use App\Jobs\DispatchDocumentTasks;
use App\Models\DocumentContentBlock;
use App\Repositories\DocumentRepository;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Illuminate\Support\Str;


class TextBlock extends Component
{
    public $contentBlock;
    public string $content;
    public string $type;
    public string $customPrompt;
    public string $tone = 'default';
    public bool $faster = true;
    public array $hide = [];
    public bool $showCustomPrompt = false;
    public bool $processing;
    public $hasPastVersions;
    public $hasFutureVersions;
    public array $info;

    protected $rules = [
        'customPrompt' => 'required|string'
    ];

    protected $messages = [
        'customPrompt.required' => 'Please provide the instructions for me to rewrite the text'
    ];

    public function getListeners()
    {
        $userId = Auth::user()->id;
        return [
            "echo-private:User.$userId,.ContentBlockUpdated" => 'onProcessFinished',
            "setTone"
        ];
    }

    public function mount(DocumentContentBlock $contentBlock)
    {
        $this->contentBlock = $contentBlock;
        $this->setup();
    }

    public function setup()
    {
        $this->hasPastVersions = $this->contentBlock->hasPastVersions();
        $this->hasFutureVersions = $this->contentBlock->hasFutureVersions();
        $this->tone = $this->contentBlock->document->getMeta('tone') ?? 'default';
        $this->content = $this->contentBlock->content;
        $this->type = $this->contentBlock->type;
        $this->info = [
            'char_count' => mb_strlen($this->content),
            'word_count' => Str::wordCount($this->content)
        ];
    }

    public function expand()
    {
        if (in_array($this->type, ['h1', 'h2', 'h3', 'h4', 'h5', 'h6'])) {
            $this->rewrite(__('prompt.expand_title', ['text' => $this->content]));
        } else {
            $this->rewrite(__('prompt.expand', ['text' => $this->content]));
        }
    }

    public function paraphrase()
    {
        $this->rewrite(__('prompt.paraphrase_text', ['tone' => $this->tone, 'text' => $this->content]));
    }

    public function moreComplex()
    {
        $this->rewrite(__('prompt.increase_complexity', ['text' => $this->content]));
    }

    public function lessComplex()
    {
        $this->rewrite(__('prompt.reduce_complexity', ['text' => $this->content]));
    }

    public function shorten()
    {
        if (in_array($this->type, ['h1', 'h2', 'h3', 'h4', 'h5', 'h6'])) {
            $this->rewrite(__('prompt.shorten_title', ['text' => $this->content]));
        } else {
            $this->rewrite(__('prompt.shorten', ['text' => $this->content]));
        }
    }

    public function undo()
    {
        $this->contentBlock->rollbackVersion();
        $this->contentBlock->refresh();
        $this->setup();
    }

    public function redo()
    {
        $this->contentBlock->fastForwardVersion();
        $this->contentBlock->refresh();
        $this->setup();
    }

    public function copy()
    {
        $this->emit('addToClipboard', $this->content);
        $this->dispatchBrowserEvent('alert', [
            'type' => 'info',
            'message' => __('social_media.text_copied')
        ]);
    }

    public function delete()
    {
        $this->contentBlock->delete();
        $this->emitUp('blockDeleted');
        $this->dispatchBrowserEvent('alert', [
            'type' => 'success',
            'message' => __('alerts.text_block_removed')
        ]);
    }

    public function runCustomPrompt()
    {
        $this->validate();
        $promptHelper = new PromptHelper($this->contentBlock->document->language->value);
        $this->showCustomPrompt = false;
        $this->rewrite($promptHelper->modifyText($this->customPrompt, $this->content));
    }

    public function toggleCustomPrompt()
    {
        $this->customPrompt = '';
        $this->showCustomPrompt = !$this->showCustomPrompt;
    }

    private function rewrite(string $prompt)
    {
        $this->dispatchBrowserEvent('alert', [
            'type' => 'info',
            'message' => __('alerts.rewriting_text')
        ]);
        $this->processing = true;
        DocumentRepository::createTask(
            $this->contentBlock->document->id,
            DocumentTaskEnum::REWRITE_TEXT_BLOCK,
            [
                'order' => 1,
                'process_id' => Str::uuid(),
                'meta' => [
                    'text' => $this->content,
                    'document_content_block_id' => $this->contentBlock->id,
                    'prompt' => $prompt,
                    'faster' => true
                ]
            ]
        );
        DispatchDocumentTasks::dispatch($this->contentBlock->document);
    }

    public function updatedContent()
    {
        $this->contentBlock->update(['content' => $this->content]);
    }

    public function setTone($newTone)
    {
        $this->tone = $newTone;
    }

    public function onProcessFinished($params)
    {
        if ($params['document_content_block_id'] === $this->contentBlock->id) {
            $this->contentBlock->refresh();
            $this->contentBlock->document->recalculateWordCount();
            $this->setup();
            $this->dispatchBrowserEvent('alert', [
                'type' => 'success',
                'message' => __('alerts.text_regenerated')
            ]);
            $this->processing = false;
            $this->dispatchBrowserEvent('adjustTextArea');
        }
    }

    public function render()
    {
        return view('livewire.common.blocks.text-block');
    }
}
