<?php

namespace App\Http\Livewire;

use App\Models\MediaFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;


class MyImages extends Component
{

    public $selectedImage;
    public $showNewGenerator;
    public $showVariantsGenerator;
    public $shouldPreviewImage;
    public $images;

    public function mount()
    {
        $this->images = MediaFile::images()->latest()->get();
        $this->selectedImage = null;
        $this->showVariantsGenerator = false;
        $this->showNewGenerator = true;
        $this->shouldPreviewImage = false;
    }

    public function getListeners()
    {
        return [
            'toggleVariantsGenerator',
            'toggleNewGenerator',
            'selectImage',
            'refreshImages'
        ];
    }

    public function toggleVariantsGenerator()
    {
        $this->showVariantsGenerator = !$this->showVariantsGenerator;
        $this->showNewGenerator = false;
    }

    public function toggleNewGenerator()
    {
        $this->showNewGenerator = !$this->showNewGenerator;
        $this->showVariantsGenerator = false;
    }

    public function downloadImage($mediaFileId)
    {
        $mediaFile = MediaFile::findOrFail($mediaFileId);
        return Storage::download($mediaFile->file_path);
    }

    public function deleteImage($mediaFileId)
    {
        $mediaFile = MediaFile::findOrFail($mediaFileId);
        $mediaFile->delete();
        $this->refreshImages();
        $this->dispatchBrowserEvent('alert', [
            'type' => 'success',
            'message' => 'Image deleted successfully'
        ]);
    }

    public function selectImage($mediaFileId)
    {
        $this->selectedImage = $this->images->where('id', $mediaFileId)->first();
    }

    public function previewImage($mediaFileId)
    {
        $this->selectImage($mediaFileId);
        $this->shouldPreviewImage = true;
    }

    public function generateVariants($mediaFileId)
    {
        $this->selectImage($mediaFileId);
        $this->showVariantsGenerator = true;
    }

    public function refreshImages()
    {
        $this->images = MediaFile::images()->latest()->get();
    }

    public function render()
    {
        return view('livewire.my-images');
    }
}
