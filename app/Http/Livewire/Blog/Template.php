<?php

namespace App\Http\Livewire\Blog;

use Livewire\Component;

class Template extends Component
{
    public string $icon = 'newspaper';
    public string $title = 'Blog Post';
    public string $description = 'Create a full SEO friendly article with the help of AI';

    public function render()
    {
        return view('livewire.common.template');
    }

    public function execute()
    {
        return redirect()->to('/blog/new');
    }
}
