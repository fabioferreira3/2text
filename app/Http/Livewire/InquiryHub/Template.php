<?php

namespace App\Http\Livewire\InquiryHub;

use Livewire\Component;

class Template extends Component
{
    public string $icon;
    public string $title;
    public string $description;

    public function __construct()
    {
        $this->icon = 'search-circle';
        $this->title = __('templates.inquiry_hub');
        $this->description = __('templates.create_inquiry');
    }

    public function render()
    {
        return view('livewire.common.template');
    }

    public function execute()
    {
        return redirect()->to('/inquiry-hub/new');
    }
}
