<?php

namespace App\Http\Livewire\Image;

use App\Enums\DocumentTaskEnum;
use App\Helpers\MediaHelper;
use App\Jobs\DispatchDocumentTasks;
use App\Models\DocumentContentBlock;
use App\Repositories\DocumentRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Livewire\Component;
use Talendor\StabilityAI\Enums\StylePreset;

class ImageGeneratorModal extends Component
{
    private string $contentBlockId;
    public $prompt;
    public $style;
    public bool $saving;
    private string $processId;

    public function getListeners()
    {
        $userId = Auth::user()->id;
        return [
            "echo-private:User.$userId,.ProcessFinished" => 'finishedProcess'
        ];
    }

    public function mount(DocumentContentBlock $contentBlock, string $prompt = '')
    {
        $this->contentBlockId = $contentBlock->id;
        $this->prompt = $prompt;
        $this->style = StylePreset::CINEMATIC->value;
        $this->saving = false;
        $this->processId = '';
    }

    public function toggle()
    {
        $this->emitUp('toggleImageGenerator');
    }

    public function process()
    {
        dd($this->contentBlockId);
        if (!$this->prompt) {
            return $this->dispatchBrowserEvent('alert', [
                'type' => 'error',
                'message' => "Please provide an image description"
            ]);
        }

        $this->saving = true;
        $imageSize = MediaHelper::socialMediaImageSize($this->contentBlock->document->getMeta('platform'));
        $this->processId = Str::uuid();
        DocumentRepository::createTask(
            $this->contentBlock->document->id,
            DocumentTaskEnum::GENERATE_IMAGE,
            [
                'order' => 1,
                'process_id' => $this->processId,
                'meta' => [
                    'prompt' => $this->prompt,
                    'height' => $imageSize['height'],
                    'width' => $imageSize['width'],
                    'style_preset' => $this->style,
                    'samples' => 4
                ]
            ]
        );

        DocumentRepository::createTask(
            $this->contentBlock->document->id,
            DocumentTaskEnum::REGISTER_FINISHED_PROCESS,
            [
                'order' => 2,
                'process_id' => $this->processId,
                'meta' => [
                    'silently' => true
                ]
            ]
        );

        DispatchDocumentTasks::dispatch($this->contentBlock->document);
    }

    public function render()
    {
        return view('livewire.image.image-generator-modal');
    }

    public function finishedProcess(array $params)
    {
        if ($params['process_id'] === $this->processId) {
            dd('gerou');
        }
    }
}
