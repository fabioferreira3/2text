<?php

namespace App\Jobs\Blog;

use App\Enums\AIModel;
use App\Helpers\PromptHelperFactory;
use App\Jobs\Traits\JobEndings;
use App\Models\Document;
use App\Models\User;
use App\Packages\OpenAI\ChatGPT;
use App\Repositories\DocumentRepository;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class GenerateFinishedNotification implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, JobEndings;

    public Document $document;
    public array $meta;
    public $repo;
    public $chatGpt;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Document $document, array $meta = [])
    {
        $this->document = $document->fresh();
        $this->repo = new DocumentRepository($this->document);
        $this->meta = $meta;
        $this->chatGpt = new ChatGPT(AIModel::GPT_LATEST->value);
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            $user = User::findOrFail($this->document->getMeta('user_id'));
            $promptHelper = PromptHelperFactory::create($this->document->language->value);

            $response = $this->chatGpt->request([
                [
                    'role' => 'user',
                    'content' =>  $promptHelper->generateFinishedNotification([
                        'jobName' => $this->document->type->label(),
                        'context' => $this->document->getContext(),
                        'owner' => $user->name,
                        'document_link' => route('blog-post-view', ['document' => $this->document])
                    ])
                ]
            ]);
            $this->repo->updateMeta('finished_email_content', $response['content']);
            $this->jobSucceded(true);
        } catch (Exception $e) {
            $this->jobFailed();
        }
    }

    /**
     * The unique ID of the job.
     */
    public function uniqueId(): string
    {
        return 'generating_finished_notification_' . $this->document->id;
    }
}
