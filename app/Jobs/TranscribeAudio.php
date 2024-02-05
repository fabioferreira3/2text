<?php

namespace App\Jobs;

use App\Enums\AIModel;
use App\Enums\DataType;
use App\Enums\DocumentTaskEnum;
use App\Events\InsufficientUnitsValidated;
use App\Exceptions\InsufficientUnitsException;
use App\Jobs\Traits\JobEndings;
use App\Models\Document;
use App\Models\User;
use App\Repositories\MediaRepository;
use App\Traits\UnitCheck;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Symfony\Component\HttpKernel\Exception\HttpException;

class TranscribeAudio implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable,
        InteractsWithQueue,
        Queueable,
        SerializesModels,
        JobEndings,
        UnitCheck;

    public Document $document;
    public array $meta;
    public $mediaRepo;

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
    public function __construct(Document $document, $meta = [])
    {
        $this->document = $document->fresh();
        $this->meta = $meta;
        $this->mediaRepo = new MediaRepository();
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            if (($this->meta['abort_when_context_present'] ?? false) && $this->document->getMeta('context')) {
                $this->jobSkipped();
                return;
            }

            $this->validateUnitCosts();

            $transcribedText = $this->mediaRepo->transcribeAudio(
                $this->document->getMeta('audio_file_path')
            );

            if ($this->meta['embed_source'] ?? false) {
                EmbedSource::dispatchSync($this->document, [
                    'data_type' => DataType::TEXT->value,
                    'source' => $transcribedText
                ]);
            }

            $this->document->update([
                'meta' => [
                    ...$this->document->meta,
                    'context' => $transcribedText,
                    'original_text' => $transcribedText
                ]
            ]);

            RegisterUnitsConsumption::dispatch(
                $this->document->account,
                'audio_transcription',
                [
                    'duration' => $this->document->getMeta('duration'),
                    'document_id' => $this->document->id,
                    'document_task_id' => $this->meta['task_id'] ?? null,
                    'job' => DocumentTaskEnum::TRANSCRIBE_AUDIO->value
                ]
            );

            RegisterAppUsage::dispatch($this->document->account, [
                'model' => AIModel::WHISPER->value,
                'length' => $this->document->getMeta('duration'),
                'meta' => [
                    'document_id' => $this->document->id,
                    'document_task_id' => $this->meta['task_id'] ?? null,
                    'name' => DocumentTaskEnum::TRANSCRIBE_AUDIO->value,
                    'length' => $this->document->getMeta('duration')
                ]
            ]);

            $this->jobSucceded();
        } catch (InsufficientUnitsException $e) {
            event(new InsufficientUnitsValidated(
                $this->document,
                DocumentTaskEnum::TRANSCRIBE_AUDIO->value
            ));
            $this->jobAborted("Insufficient units");
            $this->delete();
            return;
        } catch (HttpException $e) {
            $this->handleError($e, 'Transcribing audio error');
        }
    }

    public function validateUnitCosts()
    {
        $user = User::find($this->document->getMeta('user_id'));
        $this->estimateAudioTranscriptionCost($this->document->getMeta('duration'));
        $this->authorizeTotalCost($user->account);
    }

    /**
     * The unique ID of the job.
     */
    public function uniqueId(): string
    {
        return 'transcribing_audio_' . $this->meta['process_id'] ?? $this->document->id;
    }
}
