<?php

namespace App\Http\Livewire;

use App\Jobs\ProcessTextRequest;
use App\Repositories\TextRequestRepository;
use Livewire\Component;


class NewPost extends Component
{
    public string $free_text;
    public string $source_url;
    public string $source_provider;
    public string $language;
    public string $keyword;
    public string $tone;

    public function __construct()
    {
        $this->source_provider = 'free_text';
        $this->free_text = '';
        $this->source_url = '';
        $this->language = 'en';
        $this->keyword = '';
        $this->tone = '';
    }

    public function render()
    {
        return view('livewire.blog.new');
    }

    public function process()
    {
        $textRequest = TextRequestRepository::create([
            'original_text' => $this->free_text,
            'source_url' => $this->source_url,
            'source_provider' => $this->source_provider,
            'language' => $this->language,
            'keyword' => $this->keyword,
            'tone' => $this->tone
        ]);

        ProcessTextRequest::dispatch($textRequest);
        return redirect()->to('/jobs/pending');
    }
}
