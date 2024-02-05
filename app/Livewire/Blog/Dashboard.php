<?php

namespace App\Livewire\Blog;

use WireUi\Traits\Actions;
use Livewire\Component;

class Dashboard extends Component
{
    use Actions;

    protected $listeners = ['invokeNew' => 'new'];

    public function render()
    {
        return view('livewire.blog.dashboard')->title(__('blog.blog_posts'));
    }

    public function new()
    {
        return redirect()->route('new-post');
    }
}
