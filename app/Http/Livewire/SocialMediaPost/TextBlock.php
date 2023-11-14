<?php

namespace App\Http\Livewire\SocialMediaPost;

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

    public function messages()
    {
        return [
            'customPrompt.required' => __('validation.custom_prompt_required')
        ];
    }

    public function getListeners()
    {
        $userId = Auth::user()->id;
        return [
            "echo-private:User.$userId,.ContentBlockUpdated" => 'onProcessFinished',
        ];
    }

    public function expand()
    {
        $this->rewrite(__('prompt.expand', ['text' => $this->content]));
    }

    public function shorten()
    {
        $this->rewrite(__('prompt.shorten', ['text' => $this->content]));
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
                    'document_content_block_id' => $contentBlock->id,
                    'prompt' => $prompt
                ]
            ]
        );
        DispatchDocumentTasks::dispatch($contentBlock->document);
    }


    public function render()
    {
        return view('livewire.social-media-post.text-block');
    }

    public function updated()
    {
        $this->emitUp('textBlockUpdated', [
            'document_content_block_id' => $this->contentBlockId,
            'type' => 'text',
            'content' => $this->content
        ]);
    }

    public function onProcessFinished($params)
    {
        if ($params['document_content_block_id'] === $this->contentBlockId) {
            $contentBlock = DocumentContentBlock::findOrFail($this->contentBlockId);
            $this->content = $contentBlock->content;
            $this->dispatchBrowserEvent('alert', [
                'type' => 'success',
                'message' => __('alerts.text_regenerated')
            ]);
            $this->processing = false;
            $this->emitUp('contentBlockUpdated', ['document_content_block_id' => $this->contentBlockId]);
        }
    }
}
