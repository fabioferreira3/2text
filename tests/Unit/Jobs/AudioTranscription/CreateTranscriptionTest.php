<?php

use App\Enums\DocumentTaskEnum;
use App\Enums\DocumentType;
use App\Enums\Language;
use App\Enums\SourceProvider;
use App\Jobs\AudioTranscription\CreateTranscription;
use App\Jobs\DispatchDocumentTasks;
use App\Models\Document;
use Illuminate\Support\Facades\Bus;

beforeEach(function () {
    $this->youtubeUrl = "https://www.youtube.com/watch?v=VG5gaPr1Mvs";
    $this->document = Document::factory()->create([
        'type' => DocumentType::AUDIO_TRANSCRIPTION->value,
        'language' => Language::ENGLISH->value,
        'meta' => [
            'source' => SourceProvider::YOUTUBE->value,
            'source_url' => $this->youtubeUrl,
            'identify_speakers' => true,
            'speakers_expected' => 4,
            'target_language' => Language::PORTUGUESE->label()
        ]
    ]);
});

describe('Audio Transcription - CreateTranscription job', function () {
    it('can be serialized', function () {
        $job = new CreateTranscription($this->document, []);
        $serialized = serialize($job);
        expect($serialized)->toBeString();
    });

    it('registers tasks', function ($identifySpeakers) {
        $this->document->updateMeta('identify_speakers', $identifySpeakers);
        Bus::fake(DispatchDocumentTasks::class);

        $job = new CreateTranscription($this->document, []);
        $job->handle();

        $this->assertDatabaseHas('document_tasks', [
            'name' => DocumentTaskEnum::DOWNLOAD_AUDIO->value,
            'job' => DocumentTaskEnum::DOWNLOAD_AUDIO->getJob(),
            'document_id' => $this->document->id,
            'process_id' => $job->processId,
            'meta->source_url' => $this->youtubeUrl,
            'order' => 1
        ]);

        if ($this->document->getMeta('identify_speakers')) {
            $this->assertDatabaseHas('document_tasks', [
                'name' => DocumentTaskEnum::TRANSCRIBE_AUDIO_WITH_DIARIZATION->value,
                'job' => DocumentTaskEnum::TRANSCRIBE_AUDIO_WITH_DIARIZATION->getJob(),
                'document_id' => $this->document->id,
                'process_id' => $job->processId,
                'meta->speakers_expected' => 4,
                'order' => 2
            ]);
        } else {
            $this->assertDatabaseHas('document_tasks', [
                'name' => DocumentTaskEnum::TRANSCRIBE_AUDIO->value,
                'job' => DocumentTaskEnum::TRANSCRIBE_AUDIO->getJob(),
                'document_id' => $this->document->id,
                'process_id' => $job->processId,
                'meta->speakers_expected' => null,
                'order' => 2
            ]);
        }

        $this->assertDatabaseHas('document_tasks', [
            'name' => DocumentTaskEnum::PUBLISH_TEXT_BLOCK->value,
            'job' => DocumentTaskEnum::PUBLISH_TEXT_BLOCK->getJob(),
            'document_id' => $this->document->id,
            'process_id' => $job->processId,
            'meta->text' => null,
            'meta->target_language' => Language::PORTUGUESE->label(),
            'order' => 3
        ]);

        $document = $this->document;
        Bus::assertDispatched(DispatchDocumentTasks::class, function ($job) use ($document) {
            return $job->document->id === $document->id;
        });
    })->with([true, false]);
})->group('audio-transcription');
