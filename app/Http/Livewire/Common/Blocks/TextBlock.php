<?php

namespace App\Http\Livewire\Common\Blocks;

use App\Enums\DocumentTaskEnum;
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
    public bool $faster = true;
    public bool $showCustomPrompt = false;
    public bool $showBlockOptions = false;
    public bool $processing;

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
            'trackSelectedBlock',
        ];
    }

    public function mount(DocumentContentBlock $contentBlock)
    {
        $this->contentBlock = $contentBlock;
        $this->content = $contentBlock->content;
        $this->type = $contentBlock->type;
    }

    public function expand()
    {
        if (in_array($this->type, ['h1', 'h2', 'h3', 'h4', 'h5', 'h6'])) {
            $this->rewrite(__('prompt.expand_title'));
        } else {
            $this->rewrite(__('prompt.expand'));
        }
    }

    public function shorten()
    {
        if (in_array($this->type, ['h1', 'h2', 'h3', 'h4', 'h5', 'h6'])) {
            $this->rewrite(__('prompt.shorten_title'));
        } else {
            $this->rewrite(__('prompt.shorten'));
        }
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
        $this->dispatchBrowserEvent('refresh-page');
    }

    public function runCustomPrompt()
    {
        $this->validate();
        $this->showCustomPrompt = false;
        $this->rewrite($this->customPrompt);
    }

    public function toggleCustomPrompt()
    {
        $this->customPrompt = '';
        $this->showCustomPrompt = !$this->showCustomPrompt;
    }

    public function displayBlockOptions()
    {
        $this->showBlockOptions = true;
        $this->emit('trackSelectedBlock', $this->contentBlock->id);
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
                    'faster' => $this->faster
                ]
            ]
        );
        DispatchDocumentTasks::dispatch($this->contentBlock->document);
    }


    public function render()
    {
        return view('livewire.common.blocks.text-block');
    }

    public function updatedContent()
    {
        $this->contentBlock->update(['content' => $this->content]);
    }

    public function onProcessFinished($params)
    {
        if ($params['document_content_block_id'] === $this->contentBlock->id) {
            $this->contentBlock->refresh();
            $this->content = $this->contentBlock->content;
            $this->dispatchBrowserEvent('alert', [
                'type' => 'success',
                'message' => __('alerts.text_regenerated')
            ]);
            $this->processing = false;
            $this->showBlockOptions = false;
            $this->dispatchBrowserEvent('adjustTextArea');
        }
    }

    public function trackSelectedBlock($selectedBlockId)
    {
        if ($selectedBlockId !== $this->contentBlock->id) {
            $this->showBlockOptions = false;
        }
    }
}
