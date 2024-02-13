<?php

namespace App\Jobs;

use App\Enums\AIModel;
use App\Enums\DocumentTaskEnum;
use App\Events\InsufficientUnitsValidated;
use App\Exceptions\InsufficientUnitsException;
use App\Jobs\Traits\JobEndings;
use App\Models\Document;
use App\Models\User;
use App\Repositories\MediaRepository;
use App\Traits\UnitCheck;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpKernel\Exception\HttpException;

class TranscribeAudioWithDiarization implements ShouldQueue, ShouldBeUnique
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
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            $this->mediaRepo = App::make(MediaRepository::class);
            $params = [
                'document_id' => $this->document->id,
                'language' => $this->document->language,
                'task_id' => $this->meta['task_id']
            ];
            if ($this->document->getMeta('speakers_expected')) {
                $params['speakers_expected'] = (int) $this->document->getMeta('speakers_expected');
            }

            $this->validateUnitCosts();

            $this->mediaRepo->transcribeAudioWithDiarization(
                $this->document->getMeta('audio_file_path'),
                $params
            );

            RegisterUnitsConsumption::dispatch(
                $this->document->account,
                'audio_transcription',
                [
                    'duration' => $this->document->getMeta('duration'),
                    'document_id' => $this->document->id,
                    'document_task_id' => $this->meta['task_id'] ?? null,
                    'job' => DocumentTaskEnum::TRANSCRIBE_AUDIO_WITH_DIARIZATION->value
                ]
            );

            RegisterAppUsage::dispatch($this->document->account, [
                'model' => AIModel::ASSEMBLY_AI->value,
                'length' => $this->document->getMeta('duration'),
                'meta' => [
                    'document_id' => $this->document->id,
                    'document_task_id' => $this->meta['task_id'] ?? null,
                    'name' => DocumentTaskEnum::TRANSCRIBE_AUDIO_WITH_DIARIZATION->value,
                    'length' => $this->document->getMeta('duration')
                ]
            ]);

            $this->jobPending();
        } catch (InsufficientUnitsException $e) {
            event(new InsufficientUnitsValidated(
                $this->document,
                DocumentTaskEnum::TRANSCRIBE_AUDIO->value
            ));
            $this->jobAborted("Insufficient units");
            $this->delete();
            return;
        } catch (HttpException $e) {
            $this->handleError($e, 'Transcribing audio with diarization error');
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
        $id = $this->meta['process_id'] ?? $this->document->id;
        return 'transcribing_audio_with_diarization_' . $id;
    }
}
