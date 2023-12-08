<?php

use App\Enums\DocumentType;
use App\Enums\Language;
use App\Jobs\TranscribeAudioWithDiarization;
use App\Models\Document;
use App\Models\DocumentTask;

beforeEach(function () {
    $this->document = Document::factory()->create([
        'type' => DocumentType::AUDIO_TRANSCRIPTION->value,
        'language' => Language::ENGLISH->value,
        'meta' => [
            'audio_file_path' => [fake()->filePath()]
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
        $job->mediaRepo = $this->mediaRepo;
        $job->handle();

        expect($task->fresh()->status)->toBe('pending');
    });
})->group('audio-transcription');
