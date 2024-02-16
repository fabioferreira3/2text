<?php

namespace App\Jobs\TextToAudio;

use App\Enums\AIModel;
use App\Enums\DocumentTaskEnum;
use App\Enums\MediaType;
use App\Events\AudioGenerated;
use App\Exceptions\AudioGenerationException;
use App\Exceptions\AudioGenerationTimeoutException;
use App\Jobs\RegisterAppUsage;
use App\Jobs\RegisterUnitsConsumption;
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
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Talendor\ElevenLabsClient\TextToSpeech\TextToSpeech;

class GenerateAudio implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, JobEndings;

    public Document $document;
    public array $meta;
    public $filePath;

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 10;

    /**
     * The maximum number of unhandled exceptions to allow before failing.
     *
     * @var int
     */
    public $maxExceptions = 10;

    /**
     * Determine the time at which the job should timeout.
     *
     * @return \DateTime
     */
    public function retryUntil()
    {
        return now()->addMinutes(5);
    }

    /**
     * Calculate the number of seconds to wait before retrying the job.
     *
     * @return array<int, int>
     */
    public function backoff(): array
    {
        return [5, 10, 15];
    }

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Document $document, array $meta = [])
    {
        $this->document = $document->fresh();
        $this->meta = $meta;
        $this->filePath = '';
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

            if ($response['status'] === 504) {
                throw new AudioGenerationTimeoutException("Timeout when generating audio: " . $response['message']);
            }

            if ($response['status'] !== 200) {
                throw new AudioGenerationException("Failed to generate audio: " . $response['message']);
            }

            $audioContent = $response['response_body'];

            $this->filePath = 'ai-audio/' . Str::uuid() . '.mp3';
            Storage::disk('s3')->put($this->filePath, $audioContent);

            $mediaFile = MediaFile::create([
                'account_id' => $user->account_id,
                'file_path' => $this->filePath,
                'type' => MediaType::AUDIO,
                'meta' => [
                    'document_id' => $this->document->id
                ]
            ]);

            RegisterUnitsConsumption::dispatch($this->document->account, 'audio_generation', [
                'word_count' => Str::wordCount($this->meta['input_text']),
                'document_id' => $this->document->id,
                'document_task_id' => $this->meta['task_id'] ?? null,
                'name' => DocumentTaskEnum::TEXT_TO_AUDIO->value,
            ]);

            RegisterAppUsage::dispatch($this->document->account, [
                'model' => AIModel::ELEVEN_LABS->value,
                'char_count' => strlen($this->document->content),
                'meta' => [
                    'document_id' => $this->document->id,
                    'document_task_id' => $this->meta['task_id'] ?? null,
                    'name' => DocumentTaskEnum::TEXT_TO_AUDIO->value,
                ]
            ]);

            dump($this->document->getMeta('user_id'));
            AudioGenerated::dispatchIf(
                $this->document->getMeta('user_id'),
                [
                    'user_id' => $this->document->meta['user_id'],
                    'media_file_id' => $mediaFile->id,
                    'process_id' => (string) $this->meta['process_id']
                ]
            );

            $this->jobSucceded(true);
        } catch (AudioGenerationTimeoutException $e) {
            Log::error($e->getMessage());
            $this->jobFailed($e->getMessage());
        } catch (AudioGenerationException $e) {
            $this->jobAborted($e->getMessage());
        } catch (Exception $e) {
            $this->jobAborted("Failed to generate audio: " . $e->getMessage());
        }
    }

    /**
     * The unique ID of the job.
     */
    public function uniqueId(): string
    {
        $id = $this->meta['process_id'] ?? $this->document->id;
        return 'generating_audio_' . $id;
    }
}
