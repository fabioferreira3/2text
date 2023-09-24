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
    public bool $saving;
    public string $processId;
    public $previewImgs;

    public function getListeners()
    {
        $userId = Auth::user()->id;
        return [
            "echo-private:User.$userId,.ProcessFinished" => 'finishedProcess'
        ];
    }

    public function mount(DocumentContentBlock $contentBlock)
    {
        $this->contentBlock = $contentBlock;
        $this->prompt = '';
        $this->style = StylePreset::CINEMATIC->value;
        $this->saving = false;
        $this->processId = '3597a386-a7c1-4370-a6ae-593bde10daeb';
        //$this->previewImgs = collect([]);
        $this->previewImgs = MediaFile::take(4)->latest()->get();
    }

    public function toggle()
    {
        $this->emitUp('toggleImageGenerator');
    }

    public function processNew()
    {
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

    public function processVariants($previewImgIndex)
    {
        $this->saving = true;
        $this->processId = Str::uuid();
        DocumentRepository::createTask(
            $this->contentBlock->document->id,
            DocumentTaskEnum::GENERATE_IMAGE_VARIANTS,
            [
                'order' => 1,
                'process_id' => $this->processId,
                'meta' => [
                    'file_name' => $this->previewImgs[$previewImgIndex]->file_name,
                    'prompt' => $this->prompt,
                    'style_preset' => $this->contentBlock->document->getMeta('img_style'),
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
            $this->saving = false;
        }
    }
}
