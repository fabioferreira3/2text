<?php

namespace App\Jobs;

use App\Helpers\PromptHelper;
use App\Jobs\Traits\JobEndings;
use App\Models\Document;
use App\Packages\ChatGPT\ChatGPT;
use App\Repositories\DocumentRepository;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Exception;

class ExpandTextSection implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, JobEndings;

    protected Document $document;
    protected array $meta;
    protected PromptHelper $promptHelper;
    protected DocumentRepository $repo;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Document $document, array $meta = [])
    {
        $this->document = $document->fresh();
        $this->meta = $meta;
        $this->promptHelper = new PromptHelper($document->language->value);
        $this->repo = new DocumentRepository($this->document);
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            $rawStructure = $this->document->meta['raw_structure'];
            $normalizedStructure = $this->document->normalized_structure;
            $prompt = $this->promptHelper->givenFollowingText($normalizedStructure);

            $chatGpt = new ChatGPT();
            $response = $chatGpt->request([
                [
                    'role' => 'user',
                    'content' =>  $prompt . $this->promptHelper->expandOn($this->meta['text_section'], [
                        'tone' => $this->meta['tone']
                    ])
                ]
            ]);
            $rawStructure[$this->meta['section_key']]['content'] = $response['content'];
            $this->repo->updateMeta('raw_structure', $rawStructure);
            $this->repo->addHistory(
                [
                    'field' => 'expand_text_section',
                    'content' => $response['content']
                ],
                $response['token_usage']
            );
            $this->jobSucceded();
        } catch (Exception $e) {
            $this->jobFailed('Failed to expand text section: ' . $e->getMessage());
        }
    }
}
