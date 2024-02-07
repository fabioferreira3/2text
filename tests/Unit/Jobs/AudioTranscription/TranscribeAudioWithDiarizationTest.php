<?php

use App\Enums\DocumentType;
use App\Enums\Language;
use App\Events\InsufficientUnitsValidated;
use App\Jobs\TranscribeAudioWithDiarization;
use App\Models\Account;
use App\Models\Document;
use App\Models\DocumentTask;
use App\Models\User;
use Illuminate\Support\Facades\Event;

beforeEach(function () {
    $this->user = User::factory()->create([
        'account_id' => Account::factory()->create([
            'units' => 99999
        ])
    ]);
    $this->document = Document::factory()->create([
        'type' => DocumentType::AUDIO_TRANSCRIPTION->value,
        'language' => Language::ENGLISH->value,
        'meta' => [
            'audio_file_path' => [fake()->filePath()],
            'duration' => 6,
            'user_id' => $this->user->id,
        ]
    ]);
});

describe('Audio Transcription - TranscribeAudioWithDiarization job', function () {
    it('sends audio to be transcribed by external provider', function () {
        $task = DocumentTask::factory()->create([
            'document_id' => $this->document->id,
        ]);
        $job = new TranscribeAudioWithDiarization($this->document, [
            'task_id' => $task->id
        ]);
        $job->handle();

        expect($task->fresh()->status)->toBe('pending');
    });

    it('throws insufficient units error', function () {
        Event::fake([InsufficientUnitsValidated::class]);
        $this->user->account->update(['units' => 0]);
        $task = DocumentTask::factory()->create([
            'document_id' => $this->document->id,
        ]);

        $job = new TranscribeAudioWithDiarization($this->document, [
            'task_id' => $task->id
        ]);
        $job->handle();

        Event::assertDispatched(InsufficientUnitsValidated::class);
        expect($task->fresh()->status)->toBe('aborted');
    });
})->group('audio-transcription');
