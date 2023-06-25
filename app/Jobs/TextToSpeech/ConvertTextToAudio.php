<?php

namespace App\Jobs\TextToSpeech;

use App\Helpers\DocumentHelper;
use App\Helpers\PromptHelper;
use App\Jobs\Traits\JobEndings;
use App\Models\Document;
use App\Packages\ChatGPT\ChatGPT;
use App\Repositories\DocumentRepository;
use Cion\TextToSpeech\Facades\TextToSpeech;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ConvertTextToAudio implements ShouldQueue, ShouldBeUnique
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
            $fileName = Str::uuid() . '.mp3';
            $path = TextToSpeech::disk('s3')
                ->saveTo($fileName)
                ->convert($this->meta['text'], [
                    'voice' => 'Kevin',
                    'engine' => 'neural'
                ]);
            // Log::debug($path);
            $this->repo->updateMeta('audio_file', $path);
            // $chatGpt = new ChatGPT();
            // $response = $chatGpt->request([
            //     [
            //         'role' => 'user',
            //         'content' =>   $this->promptHelper->writeOutline(
            //             $this->document->context,
            //             [
            //                 'tone' => $this->document->meta['tone'],
            //                 'style' => $this->document->meta['style'] ?? null,
            //                 'maxsubtopics' => $this->document->meta['target_headers_count']
            //             ]
            //         )
            //     ]
            // ]);
            // $this->repo->updateMeta('outline', $response['content']);
            // $this->repo->updateMeta('raw_structure', DocumentHelper::parseOutlineToRawStructure($response['content']));
            // $this->repo->addHistory(
            //     [
            //         'field' => 'outline',
            //         'content' => $response['content']
            //     ],
            //     $response['token_usage']
            // );
            $this->jobSucceded();
        } catch (Exception $e) {
            $this->jobFailed('Failed to convert text to audio: ' . $e->getMessage());
        }
    }

    /**
     * The unique ID of the job.
     */
    public function uniqueId(): string
    {
        return 'text_to_speech_' . $this->meta['process_id'] ?? $this->document->id;
    }
}
