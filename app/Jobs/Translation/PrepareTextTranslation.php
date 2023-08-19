<?php

namespace App\Jobs\Translation;

use App\Enums\DocumentTaskEnum;
use App\Enums\Language;
use App\Helpers\DocumentHelper;
use App\Helpers\PromptHelper;
use App\Jobs\DispatchDocumentTasks;
use App\Jobs\Traits\JobEndings;
use App\Models\Document;
use App\Repositories\DocumentRepository;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class PrepareTextTranslation implements ShouldQueue, ShouldBeUnique
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
            $sentencesChunks = DocumentHelper::chunkTextSentences($this->document->meta['original_text']);
            $order = $this->meta['order'] + 1;
            foreach ($sentencesChunks as $chunk) {
                $this->repo->createTask(DocumentTaskEnum::TRANSLATE_TEXT, [
                    'order' => $order,
                    'process_id' => $this->meta['process_id'],
                    'meta' => [
                        'text' => $chunk,
                        'target_language' => Language::from($this->meta['target_language'])->name
                    ]
                ]);
                $order++;
            }
            $this->repo->createTask(DocumentTaskEnum::PUBLISH_TRANSCRIPTION, [
                'order' => 1000,
                'process_id' => $this->meta['process_id']
            ]);
            DispatchDocumentTasks::dispatch($this->document);
            $this->jobSucceded();
        } catch (Exception $e) {
            $this->jobFailed('Failed to prepare text translation: ' . $e->getMessage());
        }
    }

    /**
     * The unique ID of the job.
     */
    public function uniqueId(): string
    {
        return 'prepare_text_translation_' . $this->meta['process_id'] ?? $this->document->id;
    }
}
