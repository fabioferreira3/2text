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
    public $imgStyle;
    public bool $processing = false;
    public string $processId;
    public array $previewImgs;
    public $stylePresets;
    public $selectedStylePreset;
    private $documentRepo;

    public function __construct()
    {
        $this->documentRepo = app(DocumentRepository::class);
    }

    public function getListeners()
    {
        $userId = Auth::user()->id;
        return [
            "echo-private:User.$userId,.ProcessFinished" => 'onProcessFinished',
            'setOriginalPreviewImage'
        ];
    }

    public function mount(DocumentContentBlock $contentBlock = null)
    {
        $this->contentBlock = $contentBlock;
        $this->prompt = $this->contentBlock->document->getMeta('img_prompt');
        $this->imgStyle = $this->contentBlock->document->getMeta('img_style') ?? null;
        $this->stylePresets = StylePreset::getMappedValues();
        $this->selectedStylePreset = $this->imgStyle ? $this->selectStylePreset($this->imgStyle) : null;
        $this->processing = false;
        $this->processId = '';
        $this->previewImgs = [
            // 'original' => null,
            'original' => MediaFile::where('meta->document_id', $this->contentBlock->document->id)
                ->first(),
            'variants' => MediaFile::take(4)->latest()->get()
            //'variants' => []
        ];
    }

    public function toggleModal()
    {
        $this->emitUp('toggleImageGenerator');
    }

    public function selectImage($fileUrl)
    {
        $this->emitUp('imageSelected', [
            'file_url' => $fileUrl
        ]);
    }

    public function selectStylePreset($style)
    {
        $found = array_values(array_filter($this->stylePresets, function ($item) use ($style) {
            return $item["value"] === $style;
        }));

        return $found[0] ?? null;
    }

    public function generateNewImages()
    {
        if (!$this->prompt) {
            return $this->dispatchBrowserEvent('alert', [
                'type' => 'error',
                'message' => "Please provide an image description"
            ]);
        }
        $this->setProcessingState();
        $this->documentRepo->setDocument($this->contentBlock->document);
        $this->documentRepo->updateMeta('img_prompt', $this->prompt);

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
                    'style_preset' => $this->imgStyle,
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

    public function generateImageVariants($mediaFileId)
    {
        $this->documentRepo->setDocument($this->contentBlock->document);
        $this->documentRepo->updateMeta('img_prompt', $this->prompt);
        $this->documentRepo->updateMeta('img_style', $this->imgStyle);
        $this->contentBlock->document->refresh();

        $mediaFile = MediaFile::findOrFail($mediaFileId);
        $this->setProcessingState();
        DocumentRepository::createTask(
            $this->contentBlock->document->id,
            DocumentTaskEnum::GENERATE_IMAGE_VARIANTS,
            [
                'order' => 1,
                'process_id' => $this->processId,
                'meta' => [
                    'file_name' => $mediaFile->file_path,
                    'prompt' => $this->prompt,
                    'style_preset' => $this->imgStyle,
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

    public function setOriginalPreviewImage(array $params)
    {
        $this->previewImgs['original'] = MediaFile::where('meta->document_id', $this->contentBlock->document->id)
            ->where('file_url', $params['file_url'])->first();
    }

    public function downloadImage($mediaFileId)
    {
        $mediaFile = MediaFile::findOrFail($mediaFileId);
        return Storage::download($mediaFile->file_path);
    }

    public function render()
    {
        return view('livewire.image.image-generator-modal');
    }

    public function updatedImgStyle($newValue)
    {
        $this->selectedStylePreset = $this->selectStylePreset($newValue);
    }

    protected function setProcessingState()
    {
        $this->processing = true;
        $this->processId = Str::uuid();
    }

    public function onProcessFinished(array $params)
    {
        if ($params['process_id'] === $this->processId) {
            $this->previewImgs['variants'] = MediaFile::where('meta->document_id', $this->contentBlock->document->id)
                ->where('meta->process_id', $this->processId)->get();
            $this->processing = false;
            $this->dispatchBrowserEvent('alert', [
                'type' => 'success',
                'message' => "Images generated successfully!"
            ]);
        }
    }
}
