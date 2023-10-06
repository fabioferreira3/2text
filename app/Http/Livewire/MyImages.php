<?php

namespace App\Http\Livewire;

use App\Models\MediaFile;
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
        $this->showVariantsGenerator = true;
        $this->showNewGenerator = false;
        $this->shouldPreviewImage = false;
    }

    public function getListeners()
    {
        return [
            'toggleImageGenerator'
        ];
    }

    public function toggleImageGenerator()
    {
        $this->showVariantsGenerator = !$this->showVariantsGenerator;
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

    public function render()
    {
        return view('livewire.my-images');
    }
}
