<?php

namespace App\Livewire\Blog;

use Livewire\Component;

class Template extends Component
{
    public string $icon;
    public string $title;
    public string $description;

    public function __construct()
    {
        $this->icon = 'newspaper';
        $this->title = __('templates.blog_post');
        $this->description = __('templates.create_blog_post');
    }

    public function render()
    {
        return view('livewire.common.template');
    }

    public function execute()
    {
        return redirect()->to('/blog/new');
    }
}
