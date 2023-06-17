<?php

namespace App\Jobs\Paraphraser;

use App\Enums\DocumentTaskEnum;
use App\Enums\DocumentType;
use App\Helpers\PromptHelper;
use App\Jobs\DispatchDocumentTasks;
use App\Jobs\Traits\JobEndings;
use App\Models\Document;
use App\Packages\ChatGPT\ChatGPT;
use App\Repositories\DocumentRepository;
use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ParaphraseText
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, JobEndings;

    protected $document;
    protected $repo;
    protected array $params;
    protected array $paraphrasedSentences;

    public function __construct(Document $document, array $params)
    {
        $this->document = $document;
        $this->params = $params;
        $this->repo = new DocumentRepository($this->document);
        $this->paraphrasedSentences = $this->document->meta['paraphrased_sentences'] ?? [];
    }

    public function handle()
    {
        Log::debug($this->paraphrasedSentences);
        $response = ['content' => 'New sentence ' . $this->params['sentence_order']];
        $paraphrasedSentence = [
            'sentence_order' => $this->params['sentence_order'],
            'text' => $response['content']
        ];

        $index = null;

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
        Log::debug($this->paraphrasedSentences);
        // $promptHelper = new PromptHelper('en');
        // $chatGpt = new ChatGPT();
        // $response = $chatGpt->request([
        //     [
        //         'role' => 'user',
        //         'content' =>   $promptHelper->paraphrase($this->params['text'], $this->document->meta['tone'])
        //     ]
        // ]);
        // $this->repo->addHistory(
        //     [
        //         'field' => 'partial_content',
        //         'content' => $response['content']
        //     ],
        //     $response['token_usage']
        // );

        //$this->repo->updateMeta('paraphrased_sentences', []);

        //return $response['content'];
    }
}
