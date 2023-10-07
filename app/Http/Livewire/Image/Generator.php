<?php

namespace App\Http\Livewire\Image;

use App\Enums\DocumentTaskEnum;
use App\Enums\DocumentType;
use App\Helpers\MediaHelper;
use App\Jobs\DispatchDocumentTasks;
use App\Models\MediaFile;
use App\Repositories\DocumentRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Component;
use Talendor\StabilityAI\Enums\StylePreset;

class Generator extends Component
{
    public $prompt;
    public $imgStyle;
    public bool $processing = false;
    public bool $shouldPreviewImage = false;
    public string $processId;
    public $previewImgs;
    public $stylePresets;
    public $selectedStylePreset;
    public $selectedImage;
    public $samples;
    public $main;

    public function getListeners()
    {
        $userId = Auth::user()->id;
        return [
            "echo-private:User.$userId,.ProcessFinished" => 'onProcessFinished',
        ];
    }

    public function mount()
    {
        $this->prompt = '';
        $this->imgStyle = null;
        $this->stylePresets = StylePreset::getMappedValues();
        $this->selectedStylePreset = $this->imgStyle ? $this->selectStylePreset($this->imgStyle) : null;
        $this->processing = false;
        $this->processId = '';
        $this->previewImgs = [];
        $this->samples = 4;
    }

    public function render()
    {
        return view('livewire.image.generator');
    }

    public function selectStylePreset($style)
    {
        $found = array_values(array_filter($this->stylePresets, function ($item) use ($style) {
            return $item["value"] === $style;
        }));

        return $found[0] ?? null;
    }

    public function generate()
    {
        if (!$this->validateParams()) {
            return;
        }

        $this->setProcessingState();

        $document = DocumentRepository::createGeneric([
            'type' => DocumentType::GENERIC,
            'language' => 'en'
        ]);

        $imageSize = MediaHelper::getPossibleImageSize($document);

        DocumentRepository::createTask(
            $document->id,
            DocumentTaskEnum::GENERATE_IMAGE,
            [
                'order' => 1,
                'process_id' => $this->processId,
                'meta' => [
                    'prompt' => $this->prompt,
                    'style_preset' => $this->imgStyle,
                    'steps' => 21,
                    'samples' => $this->samples,
                    'height' => $imageSize['height'],
                    'width' => $imageSize['width']
                ]
            ]
        );

        DocumentRepository::createTask(
            $document->id,
            DocumentTaskEnum::REGISTER_FINISHED_PROCESS,
            [
                'order' => 2,
                'process_id' => $this->processId,
                'meta' => [
                    'silently' => true
                ]
            ]
        );

        DispatchDocumentTasks::dispatch($document);
    }

    public function downloadImage($mediaFileId)
    {
        $mediaFile = MediaFile::findOrFail($mediaFileId);
        return Storage::download($mediaFile->file_path);
    }

    public function generateVariants($mediaFileId)
    {
        $this->emitUp('selectImage', $mediaFileId);
        $this->emitUp('toggleVariantsGenerator');
    }

    public function previewImage($mediaFileId)
    {
        $this->selectedImage = MediaFile::findOrFail($mediaFileId);
        $this->shouldPreviewImage = true;
    }

    public function updatedImgStyle($newValue)
    {
        $this->selectedStylePreset = $this->selectStylePreset($newValue);
    }

    public function toggleModal()
    {
        $this->emitUp('toggleNewGenerator');
    }

    protected function setProcessingState()
    {
        $this->processing = true;
        $this->processId = Str::uuid();
        $this->dispatchBrowserEvent('alert', [
            'type' => 'info',
            'message' => "Generating images. Please wait..."
        ]);
    }

    public function onProcessFinished(array $params)
    {
        if ($params['process_id'] === $this->processId) {
            $this->emitUp('refreshImages');
            $mediaFiles = MediaFile::where('meta->process_id', $this->processId)->latest()->take($this->samples)->get();
            $this->previewImgs = $mediaFiles->toArray();
            $this->processing = false;
            $this->dispatchBrowserEvent('alert', [
                'type' => 'success',
                'message' => "Images generated successfully!"
            ]);
        }
    }

    public function validateParams()
    {
        if (!$this->prompt) {
            $this->dispatchBrowserEvent('alert', [
                'type' => 'error',
                'message' => "Please provide an image description"
            ]);
            return false;
        }

        if (!$this->imgStyle) {
            $this->dispatchBrowserEvent('alert', [
                'type' => 'error',
                'message' => "Please provide an image style"
            ]);
            return false;
        }

        return true;
    }
}
