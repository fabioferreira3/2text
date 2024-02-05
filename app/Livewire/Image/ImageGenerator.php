<?php

namespace App\Livewire\Image;

use App\Enums\DocumentTaskEnum;
use App\Enums\DocumentType;
use App\Exceptions\InsufficientUnitsException;
use App\Helpers\MediaHelper;
use App\Jobs\DispatchDocumentTasks;
use App\Models\MediaFile;
use App\Repositories\DocumentRepository;
use App\Traits\UnitCheck;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Component;

class ImageGenerator extends Component
{
    use UnitCheck;

    public string $prompt = '';
    public $imgStyle = null;
    public bool $errorGenerating = false;
    public bool $processing = false;
    public bool $shouldPreviewImage = false;
    public string $processGroupId = '';
    public array $previewImgs = [];
    public $selectedImage;
    public int $samples = 1;
    public $main;

    public function getListeners()
    {
        $userId = Auth::user()->id;
        return [
            "echo-private:User.$userId,.ProcessFinished" => 'onProcessFinished',
            "echo-private:User.$userId,.ImageNotGenerated" => 'onImageNotGenerated',
        ];
    }

    public function render()
    {
        return view('livewire.image.generator');
    }

    public function generate()
    {
        if (!$this->validateParams()) {
            return;
        }

        try {
            $this->validateUnitCost();

            $this->errorGenerating = false;
            $this->setProcessingState();

            $document = DocumentRepository::createGeneric([
                'type' => DocumentType::GENERIC,
                'language' => 'en'
            ]);

            $mediaHelper = new MediaHelper();
            $imageSize = $mediaHelper->getImageSizeByDocumentType($document);

            for ($i = 1; $i <= $this->samples; $i++) {
                $processId = Str::uuid();
                DocumentRepository::createTask(
                    $document->id,
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

            DispatchDocumentTasks::dispatch($document);
        } catch (InsufficientUnitsException $e) {
            $this->dispatch(
                'alert',
                type: 'error',
                message: __('alerts.insufficient_units')
            );
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    public function validateUnitCost()
    {
        $this->totalCost = 0;
        $this->estimateImageGenerationCost($this->samples);
        $this->authorizeTotalCost();
    }

    public function downloadImage($mediaFileId)
    {
        $mediaFile = MediaFile::findOrFail($mediaFileId);
        return Storage::download($mediaFile->file_path);
    }

    public function generateVariants($mediaFileId)
    {
        $this->dispatch('selectImage', mediaFileId: $mediaFileId);
        $this->dispatch('toggleVariantsGenerator');
    }

    public function previewImage($mediaFileId)
    {
        $this->selectedImage = MediaFile::findOrFail($mediaFileId);
        $this->shouldPreviewImage = true;
    }

    public function toggleModal()
    {
        $this->dispatch('toggleNewGenerator');
    }

    protected function setProcessingState()
    {
        $this->processing = true;
        $this->processGroupId = Str::uuid();
        $this->dispatch(
            'alert',
            type: 'info',
            message: __('alerts.generating_wait')
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
            $this->previewImgs = $mediaFiles->toArray();
            $this->processing = false;
            $this->dispatch(
                'alert',
                type: 'success',
                message: __('alerts.image_generated')
            );
        }
    }

    public function onImageNotGenerated(array $params)
    {
        if ($params['process_group_id'] === $this->processGroupId) {
            $this->processing = false;
            $this->errorGenerating = true;
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
