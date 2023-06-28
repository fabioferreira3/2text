<?php

namespace App\Jobs\TextToSpeech;

use App\Enums\LanguageModels;
use App\Events\AudioGenerated;
use App\Jobs\Traits\JobEndings;
use App\Models\Document;
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
            Log::debug($this->meta);
            $fileName = Str::uuid() . '.mp3';
            $path = TextToSpeech::disk('s3')
                ->language($this->meta['iso_language'])
                ->saveTo($fileName)
                ->convert($this->meta['text'], [
                    'voice' => $this->meta['voice'],
                    'engine' => 'neural'
                ]);
            $this->repo->updateMeta('audio_file', $path);
            $this->repo->addHistory([
                'field' => 'audio_generation',
                'content' => $fileName,
                'word_count' => Str::wordCount($this->meta['text']),
                'char_count' => iconv_strlen($this->meta['text'])
            ], [
                'model' => LanguageModels::POLLY->value
            ]);
            AudioGenerated::dispatchIf($this->meta['user_id'] ?? false, $this->document, $this->meta['user_id']);

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
