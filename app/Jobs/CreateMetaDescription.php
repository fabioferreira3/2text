<?php

namespace App\Jobs;

use App\Enums\ChatGptModel;
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

class CreateMetaDescription implements ShouldQueue, ShouldBeUnique
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
                'content' => $this->promptHelper->writeMetaDescription($this->document->normalized_structure, $this->meta['tone'], $this->meta['keyword'])
            ]]);
            $this->repo->updateMeta('meta_description', $response['content']);
            $this->repo->addHistory(
                [
                    'field' => 'meta_description',
                    'content' => $response['content']
                ],
                $response['token_usage']
            );
            $this->jobSucceded();
        } catch (Exception $e) {
            $this->jobFailed('Failed to create meta description: ' . $e->getMessage());
        }
    }
}
