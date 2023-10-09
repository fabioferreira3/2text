<?php

namespace App\Jobs\TextToSpeech;

use App\Enums\LanguageModels;
use App\Enums\MediaType;
use App\Events\AudioGenerated;
use App\Jobs\Traits\JobEndings;
use App\Models\Document;
use App\Models\MediaFile;
use App\Models\User;
use App\Models\Voice;
use App\Repositories\DocumentRepository;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Talendor\ElevenLabsClient\TextToSpeech\TextToSpeech;

class ConvertTextToAudio implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, JobEndings;

    protected Document $document;
    protected DocumentRepository $repo;
    protected array $meta;

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
        $this->onQueue('voice_generation');
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            $user = User::findOrFail($this->document->meta['user_id']);
            $voice = Voice::findOrFail($this->meta['voice_id']);
            $client = app(TextToSpeech::class);
            $response = $client->generate($this->meta['input_text'], $voice->external_id, 0, 'eleven_multilingual_v2');
            $audioContent = $response['response_body'];

            $filePath = 'ai-audio/' . Str::uuid() . '.mp3';
            Storage::disk('s3')->put($filePath, $audioContent);

            $mediaFile = MediaFile::create([
                'account_id' => $user->account_id,
                'file_path' => $filePath,
                'type' => MediaType::AUDIO,
                'meta' => [
                    'document_id' => $this->document->id
                ]
            ]);

            $this->repo->addHistory([
                'field' => 'audio_generation',
                'content' => $filePath,
                'word_count' => Str::wordCount($this->meta['input_text']),
                'char_count' => iconv_strlen($this->meta['input_text'])
            ], [
                'model' => LanguageModels::ELEVEN_LABS->value
            ]);

            AudioGenerated::dispatchIf(
                $this->document->meta['user_id'],
                [
                    'user_id' => $this->document->meta['user_id'],
                    'media_file_id' => $mediaFile->id,
                    'process_id' => $this->meta['process_id']
                ]
            );

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
