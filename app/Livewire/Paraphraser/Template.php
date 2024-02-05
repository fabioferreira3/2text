<?php

namespace App\Livewire\Paraphraser;

use Livewire\Component;

class Template extends Component
{
    public string $icon;
    public string $title;
    public string $description;

    public function __construct()
    {
        $this->icon = 'switch-horizontal';
        $this->title = __('templates.paraphraser');
        $this->description = __('templates.create_paraphrase');
    }

    public function render()
    {
        return view('livewire.common.template');
    }

    public function execute()
    {
        return redirect()->to('/paraphraser/new');
    }
}
