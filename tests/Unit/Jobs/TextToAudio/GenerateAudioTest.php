<?php

use App\Enums\DocumentTaskEnum;
use App\Enums\MediaType;
use App\Events\AudioGenerated;
use App\Jobs\RegisterAppUsage;
use App\Jobs\RegisterUnitsConsumption;
use App\Jobs\TextToAudio\GenerateAudio;
use App\Models\Document;
use App\Models\DocumentTask;
use App\Models\MediaFile;
use App\Models\Voice;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Storage;
use Talendor\ElevenLabsClient\TextToSpeech\TextToSpeech;

beforeEach(function () {
    $this->be($this->authUser);
    $this->document = Document::factory()->create();
    $this->voice = Voice::factory()->create();
    $this->documentTask = DocumentTask::factory()->create([
        'document_id' => $this->document->id,
    ]);

    $this->textToSpeechMock = Mockery::mock('alias_' . TextToSpeech::class);
    $this->app->instance(TextToSpeech::class, $this->textToSpeechMock);
});

describe('Text to Audio - GenerateAudio job', function () {
    it('setup the job', function () {
        $job = new GenerateAudio($this->document, []);
        expect($job->queue)->toBe('voice_generation');
        $serialized = serialize($job);
        expect($serialized)->toBeString();

        expect($job->uniqueId())->toBe('generating_audio_' . $this->document->id);
        expect($job->backOff())->toBeArray()->toBe([5, 10, 15]);
        expect($job->tries)->toBe(10);
        expect($job->maxExceptions)->toBe(10);

        $retryUntil = $job->retryUntil();
        $expectedTime = now()->addMinutes(5);
        expect($retryUntil->diffInSeconds($expectedTime))->toBeLessThan(5);
    });

    it('handles audio generation', function ($withUser) {
        Event::fake([AudioGenerated::class]);
        $this->textToSpeechMock->shouldReceive('generate')->once()
            ->with(
                'Test input text',
                $this->voice->external_id,
                0,
                'eleven_multilingual_v2'
            )->andReturn([
                'status' => 200,
                'response_body' => 'audio_binary_data'
            ]);

        if (!$withUser) {
            $this->document->updateMeta('user_id', null);
        }

        $job = new GenerateAudio($this->document, [
            'voice_id' => $this->voice->id,
            'input_text' => 'Test input text',
            'task_id' => $this->documentTask->id,
            'process_id' => $this->documentTask->process_id
        ]);

        $job->handle();

        Storage::disk('s3')->assertExists($job->filePath);

        $this->assertDatabaseHas('media_files', [
            'account_id' => $this->authUser->account_id,
            'file_path' => $job->filePath,
            'type' => MediaType::AUDIO,
            'meta->document_id' => $this->document->id
        ]);

        Bus::assertDispatched(RegisterUnitsConsumption::class, function ($job) {
            return $job->account->id === $this->document->account->id &&
                $job->type === 'audio_generation' &&
                $job->meta['document_id'] === $this->document->id &&
                $job->meta['word_count'] === 3 &&
                $job->meta['document_task_id'] === $this->documentTask->id &&
                $job->meta['name'] === DocumentTaskEnum::TEXT_TO_AUDIO->value;
        });

        Bus::assertDispatched(RegisterAppUsage::class, function ($job) {
            return $job->account->id === $this->document->account->id &&
                $job->params['meta']['document_id'] === $this->document->id &&
                $job->params['meta']['document_task_id'] === $this->documentTask->id &&
                $job->params['meta']['name'] === DocumentTaskEnum::TEXT_TO_AUDIO->value;
        });

        $mediaFile = MediaFile::first();

        if ($withUser) {
            Event::assertDispatched(AudioGenerated::class, function ($event) use ($mediaFile) {
                return $event->params['user_id'] === $this->document->getMeta('user_id') &&
                    $event->params['process_id'] === (string) $this->documentTask->process_id &&
                    $event->broadcastOn()->name === 'private-User.' . $this->document->getMeta('user_id') &&
                    $event->broadcastWith() == [
                        'process_id' => (string) $this->documentTask->process_id,
                        'media_file_id' => $mediaFile->id,
                    ] &&
                    $event->broadcastAs() === 'AudioGenerated';
            });
        } else {
            Event::assertNotDispatched(AudioGenerated::class);
        }

        expect($this->documentTask->fresh()->status)->toBe('finished');
    })->with([false]);
})->group('text-to-audio');
