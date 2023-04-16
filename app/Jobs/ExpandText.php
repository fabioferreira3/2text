<?php

namespace App\Jobs;

use App\Enums\DocumentTaskEnum;
use App\Helpers\PromptHelper;
use App\Jobs\Traits\JobEndings;
use App\Models\Document;
use App\Repositories\DocumentRepository;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;
use Exception;

class ExpandText implements ShouldQueue, ShouldBeUnique
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
            $rawStructure = $this->document->meta['raw_structure'];
            $normalizedStructure = $this->document->normalized_structure;

            $prompt = $this->promptHelper->givenFollowingText($normalizedStructure);

            if (Str::wordCount($normalizedStructure) <= 1000) {
                $prompt .= $this->promptHelper->andGivenFollowingContext($this->document->context);
            }
            $order = $this->meta['order'];
            foreach ($rawStructure as $key => $section) {
                $this->repo->createTask(DocumentTaskEnum::EXPAND_TEXT_SECTION, [
                    'process_id' => $this->meta['process_id'],
                    'order' => $order,
                    'meta' => [
                        'tone' => $this->meta['tone'],
                        'text_section' => $section['content'],
                        'section_key' => $key,
                    ]
                ]);
                $order++;
            }
            $this->jobSucceded();
        } catch (Exception $e) {
            $this->jobFailed('Failed to expand text: ' . $e->getMessage());
        }
    }

    /**
     * The unique ID of the job.
     */
    public function uniqueId(): string
    {
        return 'expand_text_' . $this->meta['process_id'] ?? $this->document->id;
    }
}
