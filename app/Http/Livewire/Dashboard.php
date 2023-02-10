<?php

namespace App\Http\Livewire;

use App\Jobs\DownloadAudio;
use Livewire\Component;


class Dashboard extends Component
{
    public string $url;
    public string $language;

    public function __construct()
    {
        $this->url = '';
        $this->language = 'en';
    }

    public function render()
    {
        return view('livewire.dashboard');
    }

    public function process()
    {
        DownloadAudio::dispatch($this->url, $this->language);
    }
}
