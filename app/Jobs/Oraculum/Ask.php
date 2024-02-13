<?php

namespace App\Jobs\Oraculum;

use App\Enums\DocumentTaskEnum;
use App\Events\ChatMessageReceived;
use App\Interfaces\OraculumFactoryInterface;
use App\Jobs\RegisterAppUsage;
use App\Jobs\RegisterUnitsConsumption;
use App\Jobs\Traits\JobEndings;
use App\Models\ChatThreadIteration;
use App\Models\Document;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Str;

class Ask implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, JobEndings;

    public ChatThreadIteration $iteration;
    public Document $document;
    public array $meta;
    public OraculumFactoryInterface $oraculumFactory;

    public function __construct(ChatThreadIteration $iteration, array $meta)
    {
        $this->iteration = $iteration;
        $this->document = $iteration->thread->document;
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
            $this->oraculumFactory = App::make(OraculumFactoryInterface::class);
            $client = $this->oraculumFactory->make($this->iteration->thread->user, $this->meta['collection_name']);
            $response = $client->chat($this->iteration->response);
            $newIteration = $this->iteration->thread->iterations()->create([
                'response' => $response['content'],
                'origin' => 'sys'
            ]);

            RegisterUnitsConsumption::dispatch($this->iteration->thread->user->account, 'words_generation', [
                'word_count' => Str::wordCount($response['content']),
                'document_id' => $this->iteration->thread->document_id,
                'job' => DocumentTaskEnum::ASK_ORACULUM->value
            ]);

            RegisterAppUsage::dispatch($this->iteration->thread->user->account, [
                ...$response['token_usage'],
                'meta' => [
                    'name' => DocumentTaskEnum::ASK_ORACULUM->value
                ]
            ]);

            event(new ChatMessageReceived($newIteration));
            $this->jobSucceded(true);
        } catch (Exception $e) {
            $this->jobFailed('Failed to ask question to Oraculum: ' . $e->getMessage());
        }
    }
}
