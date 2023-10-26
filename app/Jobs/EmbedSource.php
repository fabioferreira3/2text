<?php

namespace App\Jobs;

use App\Enums\DataType;
use App\Jobs\Traits\JobEndings;
use App\Models\Document;
use App\Models\User;
use App\Packages\Oraculum\Oraculum;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class EmbedSource implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, JobEndings;

    protected Document $document;
    protected DataType $dataType;
    protected string $source;
    protected array $meta;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Document $document, array $meta)
    {
        $this->document = $document->fresh();
        $this->meta = $meta;
        $this->dataType = DataType::tryFrom($meta['data_type']);
        $this->source = $meta['source'];
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
            $oraculum = new Oraculum($user, $this->document->id);
            $oraculum->add($this->dataType, $this->source);
            $this->jobSucceded();
        } catch (Exception $e) {
            $this->jobFailed('Failed to embed source: ' . $e->getMessage());
        }
    }

    /**
     * The unique ID of the job.
     */
    public function uniqueId(): string
    {
        return 'embed_document_' . $this->dataType->value . '_' . $this->meta['process_id'] ?? $this->document->id;
    }
}
