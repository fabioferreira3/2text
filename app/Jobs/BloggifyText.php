<?php

namespace App\Jobs;

use App\Models\TextRequest;
use App\Packages\ChatGPT\ChatGPT;
use App\Repositories\TextRequestRepository;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class BloggifyText implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public TextRequestRepository $repo;
    public TextRequest $textRequest;
    public ChatGPT $chatGpt;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(TextRequest $textRequest)
    {
        $this->repo = new TextRequestRepository();
        $this->textRequest = $textRequest;
        $this->chatGpt = new ChatGPT();
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if (!$this->textRequest->summary) {
            if ($this->textRequest->word_count > 2000) {
                $this->repo->generateSummary($this->textRequest);
            }
            $this->increaseProgressBy(15);
        }

        if (!$this->textRequest->outline) {
            $this->repo->generateOutline($this->textRequest);
            $this->increaseProgressBy(15);
        }

        if (!$this->textRequest->final_text) {
            $this->repo->createFirstPass($this->textRequest);
            $this->repo->expandText($this->textRequest);
            $this->increaseProgressBy(50);
        }

        if (!$this->textRequest->meta_description) {
            $this->repo->generateMetaDescription($this->textRequest);
            $this->increaseProgressBy(10);
        }

        if (!$this->textRequest->title) {
            $this->repo->generateTitle($this->textRequest);
            $this->increaseProgressBy(10);
        }
    }

    public function increaseProgressBy(int $amount)
    {
        $this->textRequest->update(['progress' => $this->textRequest->progress + $amount]);
    }
}
