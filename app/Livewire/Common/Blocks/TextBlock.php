<?php

namespace App\Livewire\Common\Blocks;

use App\Enums\DocumentTaskEnum;
use App\Exceptions\InsufficientUnitsException;
use App\Helpers\PromptHelper;
use App\Jobs\DispatchDocumentTasks;
use App\Models\DocumentContentBlock;
use App\Repositories\DocumentRepository;
use App\Traits\UnitCheck;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Illuminate\Support\Str;


class TextBlock extends Component
{
    use UnitCheck;

    public $contentBlock;
    public string $content;
    public string $type;
    public string $customPrompt;
    public string $tone = 'default';
    public mixed $prefix = null;
    public bool $faster = true;
    public array $hide = [];
    public bool $showCustomPrompt = false;
    public bool $processing;
    public $rows;
    public $hasPastVersions;
    public $hasFutureVersions;
    public array $info;

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
            "setTone"
        ];
    }

    public function mount(DocumentContentBlock $contentBlock, $hide = [], $rows = 1)
    {
        $this->contentBlock = $contentBlock;
        $this->prefix = $contentBlock->prefix ?? null;
        $this->hide = $hide;
        $this->rows = $rows;
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
        $this->dispatch('adjustTextArea');
    }

    public function redo()
    {
        $this->contentBlock->fastForwardVersion();
        $this->contentBlock->refresh();
        $this->setup();
        $this->dispatch('adjustTextArea');
    }

    public function copy()
    {
        $this->dispatch('addToClipboard', message: $this->content);
        $this->dispatch(
            'alert',
            type: 'info',
            message: __('alerts.text_copied')
        );
    }

    public function delete()
    {
        $this->contentBlock->delete();
        $this->dispatch('blockDeleted');
        $this->dispatch(
            'alert',
            type: 'success',
            message: __('alerts.text_block_removed')
        );
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

    public function rewrite(string $prompt)
    {
        try {
            $this->validateUnitCosts();

            $this->dispatch(
                'alert',
                type: 'info',
                message: __('alerts.rewriting_text')
            );
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
                        'prompt' => $prompt
                    ]
                ]
            );
            DispatchDocumentTasks::dispatch($this->contentBlock->document);
        } catch (InsufficientUnitsException $e) {
            $this->dispatch(
                'alert',
                type: 'error',
                message: __('alerts.insufficient_units')
            );
        } catch (\Exception $e) {
            $this->dispatch(
                'alert',
                type: 'error',
                message: __('alerts.error_occurred')
            );
        }
    }

    public function updatedContent()
    {
        $this->contentBlock->update(['content' => $this->content]);
    }

    public function updatedPrefix()
    {
        $this->contentBlock->update(['prefix' => $this->prefix]);
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
            $this->dispatch(
                'alert',
                type: 'success',
                message: __('alerts.text_regenerated')
            );
            $this->processing = false;
            $this->dispatch('adjustTextArea');
        }
    }

    public function validateUnitCosts()
    {
        $this->estimateWordsGenerationCost(Str::wordCount($this->content));
        $this->authorizeTotalCost();
    }

    public function render()
    {
        return view('livewire.common.blocks.text-block');
    }
}
