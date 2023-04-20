<?php

namespace App\Jobs;

use App\Enums\ChatGptModel;
use App\Helpers\PromptHelper;
use App\Jobs\Traits\JobEndings;
use App\Models\Document;
use App\Packages\ChatGPT\ChatGPT;
use App\Repositories\DocumentRepository;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;

class CreateTitle implements ShouldQueue, ShouldBeUnique
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
            $chatGpt = new ChatGPT(ChatGptModel::GPT_3_TURBO->value);
            $response = $chatGpt->request([[
                'role' => 'user',
                'content' => $this->promptHelper->writeTitle($this->document->normalized_structure, $this->meta['tone'], $this->meta['keyword'])
            ]]);
            $this->repo->updateMeta('title', Str::of(str_replace(["\r", "\n"], '', $response['content']))->trim()->trim('"'));
            $this->repo->addHistory(
                [
                    'field' => 'title',
                    'content' => $response['content']
                ],
                $response['token_usage']
            );
            $this->jobSucceded();
        } catch (Exception $e) {
            $this->jobFailed('Failed to create title: ' . $e->getMessage());
        }
    }

    /**
     * The unique ID of the job.
     */
    public function uniqueId(): string
    {
        return 'create_title_' . $this->meta['process_id'] ?? $this->document->id;
    }
}
