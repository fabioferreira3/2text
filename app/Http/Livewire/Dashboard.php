<?php

namespace App\Http\Livewire;

use App\Jobs\DownloadAudio;
use App\Jobs\GenerateMetaDescription;
use App\Jobs\GenerateTitle;
use App\Jobs\ParaphraseText;
use App\Jobs\ProcessAudio;
use App\Jobs\ProcessRequestFromUrl;
use App\Jobs\SummarizeText;
use App\Repositories\TextRequestRepository;
use Illuminate\Support\Facades\Bus;
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

        ProcessRequestFromUrl::dispatchIf($textRequest->source_url, $textRequest);

        // Bus::chain([
        //     new DownloadAudio($textRequest->refresh()),
        //     new ProcessAudio($textRequest->refresh()),
        //     function () use ($textRequest) {
        //         $textRequest->refresh();
        //         ParaphraseText::dispatchIf($textRequest->language == 'en', $textRequest);
        //     },
        //     new SummarizeText($textRequest->refresh()),
        //     new GenerateTitle($textRequest->refresh()),
        //     new GenerateMetaDescription($textRequest->refresh()),
        //     function () use ($textRequest) {
        //         $textRequest->refresh();
        //         $textRequest->update(['status' => 'finished']);
        //     }
        // ])->dispatch();
    }
}
