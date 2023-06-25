<?php

namespace App\Jobs\Paraphraser;

use App\Enums\DocumentTaskEnum;
use App\Jobs\DispatchDocumentTasks;
use App\Jobs\Traits\JobEndings;
use App\Models\Document;
use App\Repositories\DocumentRepository;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ParaphraseDocument implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, JobEndings;

    protected $document;
    protected array $meta;

    public function __construct(Document $document, array $params)
    {
        $this->document = $document;
        $this->meta = $params;
    }

    public function handle()
    {
        try {
            $this->document->refresh();
            $repo = new DocumentRepository($this->document);
            $processId = $this->meta['process_id'] ?? Str::uuid();

            foreach ($this->document->meta['original_sentences'] as $sentence) {
                $repo->createTask(DocumentTaskEnum::PARAPHRASE_TEXT, [
                    'order' => $this->meta['initial_order'] ?? 1,
                    'process_id' => $processId,
                    'meta' => [
                        'text' => $sentence['text'],
                        'sentence_order' => $sentence['sentence_order'],
                        'user_id' => $this->meta['user_id'] ?? null
                    ]
                ]);
            }

            $repo->createTask(DocumentTaskEnum::CREATE_TITLE, [
                'order' => 99999,
                'process_id' => $processId,
                'meta' => []
            ]);

            DispatchDocumentTasks::dispatch($this->document);
            $this->jobSucceded();
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            $this->jobFailed();
        }
    }
}
