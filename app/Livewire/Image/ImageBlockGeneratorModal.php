<?php

namespace App\Livewire\Image;

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

class ImageBlockGeneratorModal extends Component
{
    public DocumentContentBlock $contentBlock;
    public $prompt;
    public $imgStyle;
    public bool $processing = false;
    public string $processId;
    public string $processGroupId;
    public array $previewImgs;
    public int $samples;
    public mixed $action = '';
    private $mediaHelper;
    private $documentRepo;

    public function __construct()
    {
        $this->documentRepo = app(DocumentRepository::class);
        $this->mediaHelper = app(MediaHelper::class);
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
        $this->action = __('modals.other_images');
        $this->contentBlock = $contentBlock;
        $this->prompt = $this->contentBlock->document->getMeta('img_prompt');
        $this->imgStyle = $this->contentBlock->document->getMeta('img_style') ?? null;
        $this->processGroupId = Str::uuid();
        $this->processing = false;
        $this->samples = 1;
        $this->processId = '';
        $this->previewImgs = [
            'original' => $this->contentBlock->getMediaFile() ?? null,
            'variants' => $this->contentBlock->document->getLatestImages(4)
        ];
    }

    public function toggleModal()
    {
        $this->dispatch('toggleImageGenerator');
    }

    public function selectImage($mediaFileId)
    {
        $mediaFile = MediaFile::findOrFail($mediaFileId);
        $this->dispatch(
            'imageSelected',
            media_file_id: $mediaFileId,
            file_url: $mediaFile->file_url
        );
    }

    public function generateNewImages()
    {
        if (!$this->validateParams()) {
            return;
        }
        $this->action = __('modals.new_images');
        $this->setProcessingState();
        $this->documentRepo->setDocument($this->contentBlock->document);
        $this->documentRepo->updateMeta('img_prompt', $this->prompt);

        $imageSize = $this->mediaHelper->getImageSizeByDocumentType($this->contentBlock->document);

        for ($i = 1; $i <= $this->samples; $i++) {
            $processId = Str::uuid();
            DocumentRepository::createTask(
                $this->contentBlock->document->id,
                DocumentTaskEnum::GENERATE_IMAGE,
                [
                    'order' => 1,
                    'process_group_id' => $this->processGroupId,
                    'process_id' => $processId,
                    'meta' => [
                        'prompt' => $this->prompt,
                        'height' => $imageSize['height'],
                        'width' => $imageSize['width']
                    ]
                ]
            );
        }

        DispatchDocumentTasks::dispatch($this->contentBlock->document);
    }

    public function generateImageVariants($mediaFileId)
    {
        if (!$this->validateParams()) {
            return;
        }
        $this->action = 'Variants';
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
                    'steps' => 21,
                    'samples' => $this->samples
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

    public function previewImage($mediaFileId)
    {
        $mediaFile = MediaFile::findOrFail($mediaFileId);
        $this->dispatch('openLinkInNewTab', link: $mediaFile->file_url);
    }

    public function render()
    {
        return view('livewire.image.image-block-generator-modal');
    }

    protected function setProcessingState()
    {
        $this->processing = true;
        $this->processId = Str::uuid();
        $this->dispatch(
            'alert',
            type: 'info',
            message: __('alerts.generating_images')
        );
    }

    public function onProcessFinished(array $params)
    {
        if ((!$params['has_siblings'] && $params['process_group_id'] === $this->processGroupId) ||
            ($params['has_siblings'] && $params['group_finished'])
        ) {
            $this->dispatch('refreshImages');
            $mediaFiles = MediaFile::where('meta->process_group_id', $this->processGroupId)
                ->latest()->take($this->samples)->get();
            $this->previewImgs['variants'] = $mediaFiles->toArray();
            $this->processing = false;
            $this->dispatch(
                'alert',
                type: 'success',
                message: __('alerts.images_generated')
            );
        }
    }

    public function validateParams()
    {
        if (!$this->prompt) {
            $this->dispatch(
                'alert',
                type: 'error',
                message: __('alerts.image_description')
            );
            return false;
        }

        return true;
    }
}
