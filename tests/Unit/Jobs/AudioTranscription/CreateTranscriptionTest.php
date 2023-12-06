<?php

use App\Enums\DocumentTaskEnum;
use App\Enums\DocumentType;
use App\Enums\Language;
use App\Enums\SourceProvider;
use App\Jobs\AudioTranscription\CreateTranscription;
use App\Jobs\DispatchDocumentTasks;
use App\Jobs\InquiryHub\PrepareTasks;
use App\Jobs\InquiryHub\RegisterEmbedFile;
use App\Jobs\InquiryHub\RegisterEmbedFreeText;
use App\Jobs\InquiryHub\RegisterEmbedVideoStream;
use App\Jobs\InquiryHub\RegisterEmbedWebsite;
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

    it('dispatches an embed website job', function () {
        $document = $this->document;
        $document->updateMeta('source', SourceProvider::WEBSITE_URL->value);

        $job = new PrepareTasks($document, [
            'source' => null,
            'source_type' => $document->getMeta('source'),
            'source_url' => fake()->url(),
            'video_language' => Language::ENGLISH->value
        ]);
        $job->handle();

        Bus::assertDispatched(RegisterEmbedWebsite::class, function ($job) use ($document) {
            return $job->document === $document;
        });
        Bus::assertNotDispatched(RegisterEmbedFreeText::class);
        Bus::assertNotDispatched(RegisterEmbedVideoStream::class);
        Bus::assertNotDispatched(RegisterEmbedFile::class);
    });

    it('dispatches an embed video stream job', function () {
        $document = $this->document;
        $document->updateMeta('source', SourceProvider::YOUTUBE->value);

        $job = new PrepareTasks($document, [
            'source' => null,
            'source_type' => $document->getMeta('source'),
            'source_url' => fake()->url(),
            'video_language' => Language::ENGLISH->value
        ]);
        $job->handle();

        Bus::assertDispatched(RegisterEmbedVideoStream::class, function ($job) use ($document) {
            return $job->document === $document;
        });
        Bus::assertNotDispatched(RegisterEmbedWebsite::class);
        Bus::assertNotDispatched(RegisterEmbedFreeText::class);
        Bus::assertNotDispatched(RegisterEmbedFile::class);
    });

    it('dispatches an embed file job', function ($source) {
        $document = $this->document;
        $document->updateMeta('source', $source);

        $job = new PrepareTasks($document, [
            'source' => null,
            'source_type' => $document->getMeta('source'),
            'source_url' => null,
            'video_language' => Language::ENGLISH->value
        ]);
        $job->handle();

        Bus::assertDispatched(RegisterEmbedFile::class, function ($job) use ($document) {
            return $job->document === $document;
        });
        Bus::assertNotDispatched(RegisterEmbedVideoStream::class);
        Bus::assertNotDispatched(RegisterEmbedWebsite::class);
        Bus::assertNotDispatched(RegisterEmbedFreeText::class);
    })->with([SourceProvider::PDF->value, SourceProvider::DOCX->value, SourceProvider::CSV->value]);
})->group('audio-transcription');
