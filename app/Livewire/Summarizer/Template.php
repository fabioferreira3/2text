<?php

namespace App\Livewire\Summarizer;

use Livewire\Component;

class Template extends Component
{
    public string $icon;
    public string $title;
    public string $description;

    public function __construct()
    {
        $this->icon = 'sort-ascending';
        $this->title = __('templates.summarizer');
        $this->description = __('templates.create_summary');
    }

    public function render()
    {
        return view('livewire.common.template');
    }

    public function execute()
    {
        return redirect()->to('/summarizer/new');
    }
}
