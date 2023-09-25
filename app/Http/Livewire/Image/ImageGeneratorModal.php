<?php

namespace App\Http\Livewire\Image;

use App\Enums\DocumentTaskEnum;
use App\Helpers\MediaHelper;
use App\Jobs\DispatchDocumentTasks;
use App\Models\DocumentContentBlock;
use App\Models\MediaFile;
use App\Repositories\DocumentRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Component;
use Talendor\StabilityAI\Enums\StylePreset;

class ImageGeneratorModal extends Component
{
    public DocumentContentBlock $contentBlock;
    public $prompt;
    public $style;
    public bool $processing = false;
    public string $processId;
    public $previewImgs;

    public function getListeners()
    {
        $userId = Auth::user()->id;
        return [
            "echo-private:User.$userId,.ProcessFinished" => 'finishedProcess'
        ];
    }

    public function mount(DocumentContentBlock $contentBlock = null)
    {
        $this->contentBlock = $contentBlock;
        $this->prompt = $this->contentBlock->document->getMeta('img_prompt');
        $this->style = StylePreset::CINEMATIC->value;
        $this->processing = false;
        $this->processId = '';
        $this->previewImgs = collect([]);
    }

    public function toggle()
    {
        $this->emitUp('toggleImageGenerator');
    }

    public function selectImg($previewImgIndex)
    {
        $selectedImg = $this->previewImgs[$previewImgIndex];
        $this->emitUp('imageSelected', [
            'file_name' => $selectedImg->file_name
        ]);
    }

    public function processNew()
    {
        if (!$this->prompt) {
            return $this->dispatchBrowserEvent('alert', [
                'type' => 'error',
                'message' => "Please provide an image description"
            ]);
        }
        $this->processing = true;
        $this->processId = Str::uuid();
        $repo = new DocumentRepository($this->contentBlock->document);
        $repo->updateMeta('img_prompt', $this->prompt);

        $imageSize = MediaHelper::socialMediaImageSize($this->contentBlock->document->getMeta('platform'));

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

    public function processVariants($previewImgIndex)
    {
        $this->processing = true;
        $this->processId = Str::uuid();
        DocumentRepository::createTask(
            $this->contentBlock->document->id,
            DocumentTaskEnum::GENERATE_IMAGE_VARIANTS,
            [
                'order' => 1,
                'process_id' => $this->processId,
                'meta' => [
                    'file_name' => $this->previewImgs[$previewImgIndex]->file_name,
                    'prompt' => $this->prompt ?? $this->contentBlock->document->getMeta('img_prompt'),
                    'style_preset' => $this->contentBlock->document->getMeta('img_style'),
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

    public function downloadImage($previewImgIndex)
    {
        return Storage::download($this->previewImgs[$previewImgIndex]->file_name);
    }

    public function render()
    {
        return view('livewire.image.image-generator-modal');
    }

    public function finishedProcess(array $params)
    {
        if ($params['process_id'] === $this->processId) {
            $this->previewImgs = MediaFile::where('meta->document_id', $this->contentBlock->document->id)
                ->where('meta->process_id', $this->processId)->get();
            $this->processing = false;
        }
    }
}
