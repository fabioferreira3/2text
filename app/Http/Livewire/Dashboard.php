<?php

namespace App\Http\Livewire;

use App\Jobs\DownloadAudio;
use App\Repositories\TextRequestRepository;
use Livewire\Component;


class Dashboard extends Component
{
    public string $source_url;
    public string $language;

    public function __construct()
    {
        $this->source_url = '';
        $this->language = 'en';
    }

    public function render()
    {
        return view('livewire.dashboard');
    }

    public function process()
    {
        $textRequest = TextRequestRepository::create([
            'source_url' => $this->source_url,
            'source_provider' => 'youtube',
            'language' => $this->language
        ]);

        DownloadAudio::dispatch($textRequest);
    }
}
