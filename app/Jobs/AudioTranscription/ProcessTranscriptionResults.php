<?php

namespace App\Jobs\AudioTranscription;

use App\Enums\AIModel;
use App\Enums\DocumentTaskEnum;
use App\Jobs\DispatchDocumentTasks;
use App\Jobs\RegisterAppUsage;
use App\Jobs\Traits\JobEndings;
use App\Models\Document;
use App\Models\DocumentContentBlock;
use App\Repositories\DocumentRepository;
use App\Repositories\MediaRepository;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;

class ProcessTranscriptionResults implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, JobEndings;

    protected Document $document;
    public array $meta;
    public $mediaRepo;
    public $docRepo;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Document $document, array $meta)
    {
        $this->document = $document->fresh();
        $this->meta = $meta;
        $this->mediaRepo = app(MediaRepository::class);
        $this->docRepo = app(DocumentRepository::class);
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            $this->docRepo->updateTask($this->meta['pending_task_id'], 'finished');
            $transcription = $this->mediaRepo->getTranscription($this->meta['transcript_id']);
            $subtitles = $this->mediaRepo->getTranscriptionSubtitles($this->meta['transcript_id']);
            $this->document->update([
                'meta' => [
                    ...$this->document->meta,
                    'context' => $transcription['text'],
                    'original_text' => $transcription['text'],
                    'transcript_id' => $this->meta['transcript_id'],
                    'vtt_file_path' => $subtitles['vtt_file_path'],
                    'srt_file_path' => $subtitles['srt_file_path']
                ]
            ]);
            $order = 1;
            foreach ($transcription['utterances'] as $utterance) {
                $contentBlock = $this->document->contentBlocks()->save(new DocumentContentBlock([
                    'type' => 'text',
                    'content' => $utterance['text'],
                    'prefix' => 'Speaker ' . $utterance['speaker'],
                    'prompt' => null,
                    'order' => $order
                ]));
                if ($this->document->getMeta('target_language')) {
                    DocumentRepository::createTask(
                        $this->document->id,
                        DocumentTaskEnum::TRANSLATE_TEXT_BLOCK,
                        [
                            'order' => 1,
                            'process_id' => Str::uuid(),
                            'meta' => [
                                'content_block_id' => $contentBlock->id,
                                'target_language' => $this->document->getMeta('target_language')
                            ]
                        ]
                    );
                    DispatchDocumentTasks::dispatch($this->document);
                }
                $order++;
            }
            RegisterAppUsage::dispatch($this->document->account, [
                'model' => AIModel::ASSEMBLY_AI->value,
                'length' => $this->document->getMeta('duration'),
                'meta' => [
                    'document_id' => $this->document->id,
                    'document_task_id' => $this->meta['task_id'] ?? null,
                    'name' => DocumentTaskEnum::PROCESS_TRANSCRIPTION_RESULTS->value,
                    'length' => $this->document->getMeta('duration')
                ]
            ]);
            $this->jobSucceded();
        } catch (Exception $e) {
            $this->jobFailed($e);
        }
    }
}
