<?php

namespace App\Livewire\Image;

use Livewire\Component;

class Template extends Component
{
    public string $icon;
    public string $title;
    public string $description;

    public function __construct()
    {
        $this->icon = 'photograph';
        $this->title = __('templates.image');
        $this->description = __('templates.create_ai_image');
    }

    public function render()
    {
        return view('livewire.common.template');
    }

    public function execute()
    {
        return redirect()->to('/dashboard?tab=images');
    }
}
