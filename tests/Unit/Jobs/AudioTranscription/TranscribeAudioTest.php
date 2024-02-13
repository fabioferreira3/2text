<?php

use App\Enums\DocumentType;
use App\Enums\Language;
use App\Events\InsufficientUnitsValidated;
use App\Jobs\EmbedSource;
use App\Jobs\TranscribeAudio;
use App\Models\Account;
use App\Models\Document;
use App\Models\DocumentTask;
use App\Models\User;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Event;

beforeEach(function () {
    $this->filePath = fake()->filePath();
    $this->user = User::factory()->create([
        'account_id' => Account::factory()->create([
            'units' => 99999
        ])
    ]);
    $this->document = Document::factory()->create([
        'type' => DocumentType::AUDIO_TRANSCRIPTION->value,
        'language' => Language::ENGLISH->value,
        'meta' => [
            'audio_file_path' => [$this->filePath],
            'user_id' => $this->user->id,
            'duration' => 13
        ]
    ]);
});

describe('Audio Transcription - TranscribeAudio job', function () {
    it('can be serialized', function () {
        $job = new TranscribeAudio($this->document, [
            'embed_source' => true
        ]);
        $serialized = serialize($job);
        expect($serialized)->toBeString();
    });

    it('transcribes audio into text', function ($embedSource) {
        Bus::fake(EmbedSource::class);
        Storage::disk('s3')->put($this->filePath, 'some content');
        $job = new TranscribeAudio($this->document, [
            'embed_source' => $embedSource
        ]);
        $job->handle();

        if ($embedSource) {
            Bus::assertDispatched(EmbedSource::class, function ($job) {
                return $job->document->id === $this->document->id;
            });
        } else {
            Bus::assertNotDispatched(EmbedSource::class);
        }

        $this->document->refresh();
        $this->assertEquals($this->document->getMeta('context'), "Transcribed text");
        $this->assertEquals($this->document->getMeta('original_text'), "Transcribed text");
    })->with([true, false]);

    it('aborts when context present', function () {
        Event::fake([InsufficientUnitsValidated::class]);
        $task = DocumentTask::factory()->create([
            'document_id' => $this->document->id,
        ]);
        $this->document->updateMeta('context', fake()->text());
        $job = new TranscribeAudio($this->document, [
            'task_id' => $task->id,
            'abort_when_context_present' => true
        ]);
        $job->handle();

        Event::assertNotDispatched(InsufficientUnitsValidated::class);
        expect($task->fresh()->status)->toBe('skipped');
    });

    it('throws insufficient units error', function () {
        Event::fake([InsufficientUnitsValidated::class]);

        $this->user->account->update(['units' => 0]);
        $task = DocumentTask::factory()->create([
            'document_id' => $this->document->id,
        ]);
        $job = new TranscribeAudio($this->document, [
            'task_id' => $task->id
        ]);
        $job->handle();

        Event::assertDispatched(InsufficientUnitsValidated::class);
        expect($task->fresh()->status)->toBe('aborted');
    });
})->group('audio-transcription');
