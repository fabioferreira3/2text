<?php

namespace App\Jobs\Paraphraser;

use App\Events\Paraphraser\TextParaphrased;
use App\Helpers\PromptHelper;
use App\Jobs\Traits\JobEndings;
use App\Models\Document;
use App\Packages\ChatGPT\ChatGPT;
use App\Repositories\DocumentRepository;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ParaphraseText implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, JobEndings;

    protected $document;
    protected $repo;
    protected array $meta;
    protected array $paraphrasedSentences;

    public function __construct(Document $document, array $params)
    {
        $this->document = $document;
        $this->meta = $params;
    }

    public function handle()
    {
        try {
            $this->document = $this->document->fresh();
            $this->paraphrasedSentences = $this->document->meta['paraphrased_sentences'] ?? [];
            $this->repo = new DocumentRepository($this->document);
            $index = null;
            $promptHelper = new PromptHelper($this->document->language->value);
            $chatGpt = new ChatGPT();
            $response = $chatGpt->request([
                [
                    'role' => 'user',
                    'content' =>   $promptHelper->paraphrase($this->meta['text'], $this->meta['tone'] ?? $this->document->meta['tone'])
                ]
            ]);

            $paraphrasedSentence = [
                'sentence_order' => $this->meta['sentence_order'],
                'text' => $response['content']
            ];

            foreach ($this->paraphrasedSentences as $key => $item) {
                if ($item['sentence_order'] === $paraphrasedSentence['sentence_order']) {
                    $index = $key;
                    break;
                }
            }

            if (is_null($index)) {
                $this->paraphrasedSentences[] = $paraphrasedSentence;
            } else {
                $this->paraphrasedSentences[$index] = $paraphrasedSentence;
            }
            $this->repo->addHistory(
                [
                    'field' => 'paraphrased_text',
                    'content' => $response['content']
                ],
                $response['token_usage']
            );
            $this->repo->updateMeta('paraphrased_sentences', $this->paraphrasedSentences);
            TextParaphrased::dispatch($this->document->meta['user_id']);

            $this->jobSucceded();
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            $this->jobFailed();
        }
    }
}
