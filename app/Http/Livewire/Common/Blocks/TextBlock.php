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
    public $content;
    public bool $processing;

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

    private function rewrite(string $prompt)
    {
        $this->dispatchBrowserEvent('alert', [
            'type' => 'info',
            'message' => "Rewriting text..."
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
                ]
            ]
        );
        DispatchDocumentTasks::dispatch($contentBlock->document);
    }

    public function render()
    {
        return view('livewire.common.blocks.text-block');
    }

    public function finished($params)
    {
        if ($params['document_content_block_id'] === $this->contentBlockId) {
            $contentBlock = DocumentContentBlock::findOrFail($this->contentBlockId);
            $this->content = $contentBlock->content;
            $this->dispatchBrowserEvent('alert', [
                'type' => 'success',
                'message' => "Text regenerated!"
            ]);
            $this->processing = false;
        }
    }
}
