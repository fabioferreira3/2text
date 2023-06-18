<?php

namespace App\Jobs\Paraphraser;

use App\Events\Paraphraser\TextParaphrased;
use App\Helpers\PromptHelper;
use App\Jobs\Traits\JobEndings;
use App\Models\Document;
use App\Packages\ChatGPT\ChatGPT;
use App\Repositories\DocumentRepository;
use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ParaphraseText
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
        $this->repo = new DocumentRepository($this->document);
        $this->paraphrasedSentences = $this->document->meta['paraphrased_sentences'] ?? [];
    }

    public function handle()
    {
        try {
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
                    'field' => 'partial_content',
                    'content' => $response['content']
                ],
                $response['token_usage']
            );
            $this->repo->updateMeta('paraphrased_sentences', $this->paraphrasedSentences);
            $this->jobSucceded();

            TextParaphrased::dispatch($this->meta['user_id']);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            $this->jobFailed();
        }
    }
}
