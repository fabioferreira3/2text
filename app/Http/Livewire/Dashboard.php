<?php

namespace App\Http\Livewire;

use App\Models\MediaFile;
use Livewire\Component;


class Dashboard extends Component
{
    public $title;
    public $selectedTab = 'dashboard';
    public $selectedImage;
    public $showImageGenerator;
    public $images;

    public function mount()
    {
        $this->title = 'Dashboard';
        $this->images = collect([]);
        $this->selectedImage = null;
        $this->showImageGenerator = false;
    }

    public function render()
    {
        return view('livewire.dashboard')->layout('layouts.app', ['title' => $this->title]);
    }

    public function updatedSelectedTab($tab)
    {
        if ($tab === 'images') {
            $this->images = MediaFile::images()->latest()->get();
        }
    }

    public function selectImage($mediaFileId)
    {
        $this->selectedImage = $this->images->where('id', $mediaFileId)->first();
    }
}
