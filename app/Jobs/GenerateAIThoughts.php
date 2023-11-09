<?php

namespace App\Jobs;

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

class GenerateAIThoughts implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, JobEndings;

    protected Document $document;
    protected array $meta;
    protected $repo;

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
            $chatGpt = new ChatGPT(AIModel::GPT_3_TURBO1106->value);

            $response = $chatGpt->request([
                [
                    'role' => 'user',
                    'content' =>  $promptHelper->generateThoughts([
                        'context' => $this->document->getContext(),
                        'owner' => $user->name,
                        'sentences_count' => $this->meta['sentences_count'] * 2,
                        'tone' => $this->document->getMeta('tone'),
                        'style' => $this->document->getMeta('style') ?? null
                    ])
                ]
            ]);
            $array = json_decode($response['content']);
            if ($array === null && json_last_error() !== JSON_ERROR_NONE) {
                throw new Exception("Error decoding AI thoughts generation: " . json_last_error_msg());
            } else {
                $this->repo->updateMeta('thoughts', $array);
            }
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
        return 'generating_thoughts_' . $this->document->id;
    }
}
