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
    public string $contentBlockId;
    public string $content;
    public string $customPrompt;
    public bool $faster;
    public bool $showCustomPrompt = false;
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
            "echo-private:User.$userId,.ContentBlockUpdated" => 'finished',
        ];
    }

    public function expand()
    {
        $this->rewrite(__('prompt.expand'));
    }

    public function shorten()
    {
        $this->rewrite(__('prompt.shorten'));
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

    private function rewrite(string $prompt)
    {
        $this->dispatchBrowserEvent('alert', [
            'type' => 'info',
            'message' => __('alerts.rewriting_text')
        ]);
        $contentBlock = DocumentContentBlock::findOrFail($this->contentBlockId);
        $this->processing = true;
        DocumentRepository::createTask(
            $contentBlock->document->id,
            DocumentTaskEnum::REWRITE_TEXT_BLOCK,
            [
                'order' => 1,
                'process_id' => Str::uuid(),
                'meta' => [
                    'text' => $this->content,
                    'document_content_block_id' => $contentBlock->id,
                    'prompt' => $prompt,
                    'faster' => $this->faster
                ]
            ]
        );
        DispatchDocumentTasks::dispatch($contentBlock->document);
    }

    public function render()
    {
        return view('livewire.common.blocks.text-block');
    }

    public function updated()
    {
        $this->emitUp('textBlockUpdated', [
            'document_content_block_id' => $this->contentBlockId,
            'type' => 'text',
            'content' => $this->content
        ]);
    }

    public function finished($params)
    {
        if ($params['document_content_block_id'] === $this->contentBlockId) {
            $contentBlock = DocumentContentBlock::findOrFail($this->contentBlockId);
            $this->content = $contentBlock->content;
            $this->dispatchBrowserEvent('alert', [
                'type' => 'success',
                'message' => __('alerts.text_regenerated')
            ]);
            $this->processing = false;
        }
    }
}
