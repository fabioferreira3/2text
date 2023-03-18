<?php

namespace App\Http\Livewire;

use Livewire\Component;


class PendingJobs extends Component
{
    public string $free_text;
    public string $source_url;
    public string $source_provider;
    public string $language;
    public string $keyword;
    public string $tone;

    public function __construct()
    {
    }

    public function render()
    {
        return view('livewire.blog.pending');
    }
}
